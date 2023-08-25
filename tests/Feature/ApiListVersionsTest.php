<?php

namespace Tests\Feature;

use App\Models\LaravelVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
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
        LaravelVersion::factory()->active()->create();
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
        LaravelVersion::factory()->active()->create();
        $response = $this->get(route('api.versions.index'));
        $entry = $response->json()['data'][0];

        $this->assertFalse(array_key_exists('specific_version', $entry));
    }

    /** @test */
    public function it_lists_versions_in_expected_format()
    {
        $versions = $this->seedVersions(
            majorCount: 10,
            minorCount: 5,
            patchCount: 2
        );

        $response = $this->get(route('api.versions.index'));

        $this->assertJsonStringEqualsJsonString($this->getVersionsJsonResponse($versions), $response->getContent());
    }

    /** @test */
    public function it_lists_specific_version_in_expected_format()
    {
        $this->seedVersions(
            majorCount: 10,
            minorCount: 5,
            patchCount: 2
        );

        LaravelVersion::withoutGlobalScope('first')
            ->get()->each(function ($version) {
                $response = $this->get($version->api_url);
                $this->assertJsonStringEqualsJsonString($this->getVersionJsonResponse($version), $response->getContent());
            });
    }

    private function getVersionsJsonResponse(Collection $versions): string
    {
        $versions = $versions->map(fn ($version) => [
            'ends_bugfixes_at' => $version->ends_bugfixes_at,
            'ends_securityfixes_at' => $version->ends_securityfixes_at,
            'supported_php' => explode(', ', $version->supported_php),
            'global' => [
                'latest_version' => LaravelVersion::withoutGlobalScope('first')->latest('order')->first()->semver,
            ],
            'latest' => $version->last->semver,
            $version->major < 6 ? 'minor' : 'latest_minor' => $version->last->minor,
            'latest_patch' => $version->last->patch,
            'links' => [
                [
                    'href' => $version->api_url,
                    'rel' => 'self',
                    'type' => 'GET',
                ], [
                    'href' => $version->last->api_url,
                    'rel' => 'latest',
                    'type' => 'GET',
                ],
            ],
            'major' => (int) $version->majorish,
            'released_at' => $version->released_at,
            'status' => $version->status,
        ]);

        return json_encode(['data' => [...$versions]]);
    }

    private function getVersionJsonResponse($version)
    {
        $versionResponse = json_decode($this->getVersionsJsonResponse(collect([$version])))->data[0];

        $versionResponse->links = array_values(array_filter([
            $version->is_first ? [] : [
                'type' => 'GET',
                'rel' => 'major',
                'href' => $version->first->api_url,
            ],
            [
                'type' => 'GET',
                'rel' => 'self',
                'href' => $version->api_url,
            ],
            [
                'type' => 'GET',
                'rel' => 'latest',
                'href' => $version->last->api_url,
            ],
        ]));

        if (! $version->is_first) {
            $versionResponse->specific_version = [
                'needs_major_upgrade' => $version->needs_major_upgrade,
                'needs_patch' => $version->needs_patch,
                'provided' => $version->semver,
            ];
        }

        return json_encode(['data' => $versionResponse]);
    }
}
