<?php

namespace Tests;

use App\Models\LaravelVersion;
use Database\Seeders\LaravelVersionSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Collection;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function seedVersions($majorCount = 1, $minorCount = 1, $patchCount = 1): Collection
    {
        return (new LaravelVersionSeeder)->versions()->slice(0, $majorCount)->map(function ($version) use ($minorCount, $patchCount) {
            $front = LaravelVersion::factory()->create($version);

            collect(range(1, $minorCount))->each(function ($minor) use ($version, $front, $patchCount) {
                LaravelVersion::factory()->create(array_merge($version, [
                    'minor' => $minor,
                    'semver' => "{$front->major}.{$minor}.0",
                    'first_release' => $front->semver,
                    'is_front' => false,
                    'order' => LaravelVersion::calculateOrder($front->major, $minor, 0),
                ]));

                collect(range(1, $patchCount))->each(function ($patch) use ($version, $front, $minor) {
                    LaravelVersion::factory()->create(array_merge($version, [
                        'minor' => $minor,
                        'patch' => $patch,
                        'semver' => "{$front->major}.{$minor}.{$patch}",
                        'first_release' => $front->semver,
                        'is_front' => false,
                        'order' => LaravelVersion::calculateOrder($front->major, $minor, $patch),
                    ]));
                });
            });

            return $front;
        });
    }
}
