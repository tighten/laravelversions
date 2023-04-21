<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaravelVersion extends Model
{
    use HasFactory;

    const STATUS_FUTURE = 'future';

    const STATUS_ACTIVE = 'active';

    const STATUS_SECURITY = 'security';

    const STATUS_ENDOFLIFE = 'end-of-life';

    protected $guarded = [];

    protected $casts = [
        'released_at' => 'date',
        'ends_bugfixes_at' => 'date',
        'ends_securityfixes_at' => 'date',
        'is_lts' => 'bool',
        'is_front' => 'bool',
    ];

    public static function notificationDays()
    {
        return [
            'today' => now()->startOfDay(),
            'tomorrow' => now()->addDay()->startOfDay(),
            'in one week' => now()->addWeek()->startOfDay(),
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope('front', function (Builder $builder) {
            $builder->where('is_front', true);
        });
    }

    public function scopeReleased($query)
    {
        $query->where('released_at', '<=', now());
    }

    public function getReleases(): Collection
    {
        return static::withoutGlobalScope('front')
            ->where('major', $this->major)
            ->when($this->pre_semver, fn ($query) => $query->where('minor', $this->minor))
            ->orderBy('released_at', 'DESC')
            ->get();
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
        return route('versions.show', [$this->majorish]);
    }

    public function getApiUrlAttribute()
    {
        return route('api.versions.show', [$this->majorish]);
    }

    public function getLatestPatchApiUrlAttribute()
    {
        return route('api.versions.show', [$this->__toString()]);
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

    public function __toString()
    {
        return implode('.', [
            $this->major,
            $this->minor,
            $this->patch,
        ]);
    }
}
