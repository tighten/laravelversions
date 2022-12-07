<?php

namespace Tests\Feature;

use App\Models\LaravelVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiShowVersionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_loads()
    {
        $version = LaravelVersion::factory()->create();
        $response = $this->get(route('api.versions.show', [$version->__toString()]));

        $response->assertStatus(200);
    }

    /** @test */
    public function it_loads_latest_version()
    {
        $newest = LaravelVersion::factory()->create([
            'major' => 8,
            'is_lts' => false,
        ]);

        // older patch
        LaravelVersion::factory()->create([
            'major' => $newest->major,
            'minor' => $newest->minor,
            'patch' => $newest->minor - 1,
        ]);

        //older major version
        $oldest = LaravelVersion::factory()->create([
            'major' => $newest->major - 1,
        ]);

        $this->getJson(route('api.versions.show', [$oldest->__toString()]))
            ->assertJsonFragment([
                'latest_version' => $newest->__toString(),
                'latest_version_is_lts' => false,
        ]);
    }
}
