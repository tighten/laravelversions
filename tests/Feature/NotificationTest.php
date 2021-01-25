<?php

namespace Tests\Feature;

use App\Models\LaravelVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function versions_need_notification_if_today_is_a_fix_end_date()
    {
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
    }

    /** @test */
    public function it_handles_null_fix_dates()
    {
        $null_security = LaravelVersion::factory()->create([
            'ends_securityfixes_at' => null,
        ]);

        $null_bug = LaravelVersion::factory()->create([
            'ends_bugfixes_at' => null,
        ]);

        $this->assertFalse($null_security->needsNotification());
        $this->assertFalse($null_bug->needsNotification());
    }
}
