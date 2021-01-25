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
}
