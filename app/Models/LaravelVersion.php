<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class LaravelVersion extends Model
{
    use HasFactory;

    const STATUS_FUTURE = 'future';

    const STATUS_ACTIVE = 'active';

    const STATUS_SECURITY = 'security';

    const STATUS_ENDOFLIFE = 'end-of-life';

    protected $guarded = [];

    public static function notificationDays(): array
    {
        return [
            'today' => now()->startOfDay(),
            'tomorrow' => now()->addDay()->startOfDay(),
            'in one week' => now()->addWeek()->startOfDay(),
        ];
    }

    public static function calculateOrder($major, $minor, $patch): int
    {
        return (int) ($major * 1000000 + $minor * 1000 + $patch);
    }

    protected static function booted(): void
    {
        static::addGlobalScope('first', function (Builder $builder) {
            $builder->whereRaw('first_release = ' . DB::concat('major', "'.'", 'minor', "'.'", 'patch')->getValue(DB::connection()->getQueryGrammar()));
        });

        static::saving(function (self $version) {
            $version->first_release = $version->is_first
                ? $version->semver
                : str($version->majorish)->explode('.')->pad(3, 0)->implode('.');

            $version->order = static::calculateOrder($version->major, $version->minor, $version->patch);
        });
    }

    public function first(): HasOne
    {
        return new HasOne(
            (new static)->newQuery(),
            $this,
            DB::concat('major', '"."', 'minor', '"."', 'patch'),
            'first_release'
        );
    }

    public function last(): HasOne
    {
        return $this->hasOne(static::class, 'first_release', 'first_release')
            ->withoutGlobalScope('first')
            ->ofMany(['order' => 'MAX']);
    }

    public function getReleases(): Collection
    {
        return static::withoutGlobalScope('first')
            ->where('major', $this->major)
            ->when($this->pre_semver, fn ($query) => $query->where('minor', $this->minor))
            ->orderBy('released_at', 'DESC')
            ->get();
    }

    public function getIsFirstAttribute(): bool
    {
        return (collect([5, 4, 3])->contains($this->major) && $this->patch === 0)
            || ($this->minor === 0 && $this->patch === 0);
    }

    public function getSemverAttribute(): string
    {
        return "{$this->major}.{$this->minor}.{$this->patch}";
    }

    public function getStatusAttribute()
    {
        // active, security, end-of-life
        if ($this->released_at->gt(now())) {
            return self::STATUS_FUTURE;
        }

        if ($this->ends_bugfixes_at && $this->ends_bugfixes_at->gt(now())) {
            return self::STATUS_ACTIVE;
        }

        if ($this->ends_securityfixes_at && $this->ends_securityfixes_at->gt(now())) {
            return self::STATUS_SECURITY;
        }

        return self::STATUS_ENDOFLIFE;
    }

    /**
     * Returns major for semver Laravel, or major.minor for pre-semver Laravel
     */
    public function getMajorishAttribute(): string
    {
        return $this->pre_semver
            ? $this->major . '.' . $this->minor
            : $this->major;
    }

    public function getPreSemverAttribute()
    {
        return $this->major < 6;
    }

    public function getUrlAttribute()
    {
        return route('versions.show', $this->is_first ? $this->majorish : $this->semver);
    }

    public function getApiUrlAttribute()
    {
        return route('api.versions.show', $this->is_first ? $this->majorish : $this->semver);
    }

    public function getNeedsPatchAttribute(): bool
    {
        return $this->last->semver !== $this->semver;
    }

    public function getNeedsMajorUpgradeAttribute(): bool
    {
        return $this->status === 'end-of-life';
    }

    public function needsNotification()
    {
        foreach (self::notificationDays() as $day) {
            if (($this->ends_bugfixes_at && $this->ends_bugfixes_at->eq($day))
                || ($this->ends_securityfixes_at && $this->ends_securityfixes_at->eq($day))) {
                return true;
            }
        }

        return false;
    }

    #[Scope]
    protected function released($query)
    {
        $query->where('released_at', '<=', now());
    }

    protected function casts(): array
    {
        return [
            'released_at' => 'date',
            'ends_bugfixes_at' => 'date',
            'ends_securityfixes_at' => 'date',
        ];
    }

    public function __toString()
    {
        return implode('.', [
            $this->major,
            $this->minor,
            $this->patch,
        ]);
    }
}
