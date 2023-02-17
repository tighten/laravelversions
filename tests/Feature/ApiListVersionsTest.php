<?php

namespace Tests\Feature;

use App\Models\LaravelVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiListVersionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_loads(): void
    {
        $response = $this->get(route('api.versions.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function it_lists_valid_versions(): void
    {
        $version = LaravelVersion::factory()->create([
            'major' => 6,
        ]);
        $response = $this->get(route('api.versions.index'));
        $this->assertCount(1, $response->json()['data']);
    }

    /** @test */
    public function it_doesnt_list_future_versions(): void
    {
        LaravelVersion::factory()->create([
            'released_at' => now()->addDays(20),
        ]);

        $response = $this->get(route('api.versions.index'));
        $this->assertEmpty($response->json()['data']);
    }

    /** @test */
    public function entries_arent_given_specific_version_key(): void
    {
        LaravelVersion::factory()->create();
        $response = $this->get(route('api.versions.index'));
        $entry = $response->json()['data'][0];

        $this->assertFalse(array_key_exists('specific_version', $entry));
    }
}
