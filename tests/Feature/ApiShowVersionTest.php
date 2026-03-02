<?php

use App\Models\LaravelVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


it('loads', function () {
    $version = $this->seedVersions(
        majorCount: 1,
        minorCount: 1,
        patchCount: 1
    )->first();

    $response = $this->get($version->api_url);

    $response->assertStatus(200);
});

it('loads latest version', function () {
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
});
