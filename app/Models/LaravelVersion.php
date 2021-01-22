<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaravelVersion extends Model
{
    use HasFactory;

    protected $fillable = [];

    protected $casts = [
        'released_at' => 'date',
        'ends_bugfixes_at' => 'date',
        'ends_securityfixes_at' => 'date',
        'is_lts' => 'bool',
    ];

    public function getStatusAttribute()
    {
        // active, security, inactive
        if ($this->released_at->gt(now())) {
            return 'active';
        }

        if ($this->ends_bugfixes_at && $this->ends_bugfixes_at->gt(now())) {
            return 'active';
        }

        if ($this->ends_securityfixes_at && $this->ends_securityfixes_at->gt(now())) {
            return 'security';
        }

        return 'inactive';
    }

    public function getUrlAttribute()
    {
        if ($this->major < 6) {
            return url($this->major . '.' . $this->minor);
        }

        return url($this->major);
    }
}
