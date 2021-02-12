<?php

namespace App\Models;

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
    ];

    public static function notificationDays()
    {
        return [
            'today' => now()->startOfDay(),
            'tomorrow' => now()->subDay()->startOfDay(),
            'in one week' => now()->subWeek()->startOfDay(),
        ];
    }

    public function scopeReleased($query)
    {
        $query->where('released_at', '<=', now());
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

    public function getUrlAttribute()
    {
        if ($this->major < 6) {
            return route('versions.show', ['version' => $this->major . '.' . $this->minor]);
        }

        return route('versions.show', ['version' => $this->major]);
    }

    public function getApiUrlAttribute()
    {
        $path = $this->major;

        if ($this->major < 6) {
            $path .= '.' . $this->minor;
        }

        return route('api.versions.show', [$path]);
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
