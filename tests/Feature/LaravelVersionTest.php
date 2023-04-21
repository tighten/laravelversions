<?php

namespace Tests\Feature;

use App\Models\LaravelVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaravelVersionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_gets_only_released_versions(): void
    {
        $no = LaravelVersion::factory()->create([
            'released_at' => now()->addYear(),
            'is_front' => true,
        ]);

        $yes = LaravelVersion::factory()->create([
            'released_at' => now()->subYear(),
            'is_front' => true,
        ]);

        $released = LaravelVersion::released()->get();

        $this->assertCount(1, $released);
        $this->assertEquals($yes->id, $released->first()->id);
    }
}
