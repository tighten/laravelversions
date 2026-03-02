<?php

use App\Models\LaravelVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

it('loads', function () {
    $version = LaravelVersion::factory()->create([
        'ends_securityfixes_at' => now()->addYear(),
    ]);

    $response = $this->get($version->url);

    $response->assertStatus(200);
});

it('loads when bug fixes date is null', function () {
    seedLowestSupportedVersion();

    $version = LaravelVersion::factory()->create([
        'ends_bugfixes_at' => null,
    ]);
    $response = $this->get($version->url);

    $response->assertStatus(200);
});

it('loads when security fixes date is null', function () {
    seedLowestSupportedVersion();

    $version = LaravelVersion::factory()->create([
        'ends_securityfixes_at' => null,
    ]);
    $response = $this->get($version->url);

    $response->assertStatus(200);
});

// Helpers
function seedLowestSupportedVersion()
{
    LaravelVersion::factory()->active()->create();
}
