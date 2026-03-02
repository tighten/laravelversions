<?php

use App\Models\LaravelVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

test('versions need notification if today is a fix end date', function () {
    $bugfix = LaravelVersion::factory()->create([
        'ends_bugfixes_at' => now()->startOfDay(),
    ]);

    $security = LaravelVersion::factory()->create([
        'ends_securityfixes_at' => now()->startOfDay(),
    ]);

    $release = LaravelVersion::factory()->create([
        'released_at' => now()->startOfDay(),
        'ends_bugfixes_at' => now()->addYear(),
        'ends_securityfixes_at' => now()->addYears(2),
    ]);

    $none = LaravelVersion::factory()->create([
        'ends_bugfixes_at' => now()->addYear(),
        'ends_bugfixes_at' => now()->addYears(2),
        'ends_securityfixes_at' => now()->addYears(3),
    ]);

    $this->assertTrue($bugfix->needsNotification());
    $this->assertTrue($security->needsNotification());
    $this->assertFalse($release->needsNotification());
    $this->assertFalse($none->needsNotification());
});

it('handles null fix dates', function () {
    $null_security = LaravelVersion::factory()->create([
        'ends_securityfixes_at' => null,
    ]);

    $null_bug = LaravelVersion::factory()->create([
        'ends_bugfixes_at' => null,
    ]);

    $this->assertFalse($null_security->needsNotification());
    $this->assertFalse($null_bug->needsNotification());
});
