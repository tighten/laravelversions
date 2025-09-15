<?php

namespace Tests\Feature;

use App\Models\LaravelVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LaravelVersionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_gets_only_released_versions(): void
    {
        LaravelVersion::factory()->create([
            'major' => 10,
            'minor' => 0,
            'patch' => 0,
            'released_at' => now()->addYear(),
        ]);

        $yes = LaravelVersion::factory()->create([
            'major' => 9,
            'minor' => 0,
            'patch' => 0,
            'released_at' => now()->subYear(),
        ]);

        $released = LaravelVersion::released()->get();

        $this->assertCount(1, $released);
        $this->assertEquals($yes->id, $released->first()->id);
    }
}
