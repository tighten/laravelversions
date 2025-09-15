<?php

namespace Tests\Feature;

use App\Models\LaravelVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ApiShowVersionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_loads(): void
    {
        $version = $this->seedVersions(
            majorCount: 1,
            minorCount: 1,
            patchCount: 1
        )->first();

        $response = $this->get($version->api_url);

        $response->assertStatus(200);
    }

    #[Test]
    public function it_loads_latest_version(): void
    {
        $this->seedVersions(
            majorCount: 2,
            minorCount: 5,
            patchCount: 2
        );

        $newest = LaravelVersion::withoutGlobalScope('first')->latest('order')->first();
        $older = LaravelVersion::where('major', $newest->major - 1)->first();

        $this->getJson($older->api_url)
            ->assertJsonFragment([
                'latest_version' => $newest->semver,
            ]);
    }
}
