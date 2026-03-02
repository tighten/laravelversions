<?php

use App\Models\LaravelVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

it('loads', function () {
    $response = $this->get(route('api.versions.index'));

    $response->assertStatus(200);
});

it('lists valid versions', function () {
    LaravelVersion::factory()->active()->create();
    $response = $this->get(route('api.versions.index'));
    expect($response->json()['data'])->toHaveCount(1);
});

it('doesnt list future versions', function () {
    LaravelVersion::factory()->create([
        'released_at' => now()->addDays(20),
    ]);

    $response = $this->get(route('api.versions.index'));
    expect($response->json()['data'])->toBeEmpty();
});

test('entries arent given specific version key', function () {
    LaravelVersion::factory()->active()->create();
    $response = $this->get(route('api.versions.index'));
    $entry = $response->json()['data'][0];

    expect(array_key_exists('specific_version', $entry))->toBeFalse();
});

it('lists versions in expected format', function () {
    $versions = $this->seedVersions(
        majorCount: 10,
        minorCount: 5,
        patchCount: 2
    );

    $response = $this->get(route('api.versions.index'));

    $this->assertJsonStringEqualsJsonString(getVersionsJsonResponse(LaravelVersion::all()), $response->getContent());
});

it('lists specific version in expected format', function () {
    $this->seedVersions(
        majorCount: 10,
        minorCount: 5,
        patchCount: 2
    );

    LaravelVersion::withoutGlobalScope('first')
        ->get()->each(function ($version) {
            $response = $this->get($version->api_url);
            $this->assertJsonStringEqualsJsonString(getVersionJsonResponse($version), $response->getContent());
        });
});

// Helpers
function getVersionsJsonResponse(Collection $versions): string
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

function getVersionJsonResponse($version)
{
    $versionResponse = json_decode(test()->getVersionsJsonResponse(collect([$version])))->data[0];

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
