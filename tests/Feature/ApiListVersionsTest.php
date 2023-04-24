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
        $versions = $this->seedVersions(
            majorCount: 10,
            minorCount: 5,
            patchCount: 2
        );

        LaravelVersion::withoutGlobalScope('front')
            ->whereIn('semver', [
                '10.0.0',
                '10.1.1',
                '6.0.0',
                '6.1.0',
                '6.1.1',
                '5.8.0',
                '5.8.1',
                '5.0.0',
            ]
                )->get()->each(function ($version) {
                    $response = $this->get(route('api.versions.show', $version->semver));

                    $this->assertJsonStringEqualsJsonString($this->getVersionJsonResponse($version), $response->getContent());
                });
    }

    private function getVersionsJsonResponse(Collection $versions): string
    {
        $versions = $versions->map(fn ($version) => [
            'ends_bugfixes_at' => $version->ends_bugfixes_at,
            'ends_securityfixes_at' => $version->ends_securityfixes_at,
            'global' => [
                'latest_version' => LaravelVersion::withoutGlobalScope('front')->latest('order')->first()->semver,
                'latest_version_is_lts' => LaravelVersion::withoutGlobalScope('front')->latest('order')->first()->is_lts,
            ],
            'is_lts' => $version->is_lts,
            'latest' => $version->lastRelease->semver,
            $version->major < 6 ? 'minor' : 'latest_minor' => $version->lastRelease->minor,
            'latest_patch' => $version->lastRelease->patch,
            'links' => [
                [
                    'href' => $version->api_url,
                    'rel' => 'self',
                    'type' => 'GET',
                ], [
                    'href' => $version->lastRelease->api_url,
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
            $version->is_front ? [] : [
                'type' => 'GET',
                'rel' => 'major',
                'href' => $version->firstRelease->api_url,
            ],
            [
                'type' => 'GET',
                'rel' => 'self',
                'href' => $version->api_url,
            ],
            [
                'type' => 'GET',
                'rel' => 'latest',
                'href' => $version->lastRelease->api_url,
            ],
        ]));

        if (! $version->is_front) {
            $versionResponse->specific_version = [
                'needs_major_upgrade' => $version->needs_major_upgrade,
                'needs_patch' => $version->needs_patch,
                'provided' => $version->semver,
            ];
        }

        return json_encode(['data' => $versionResponse]);
    }
}
