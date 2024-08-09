<?php

namespace Tests;

use App\Models\LaravelVersion;
use Database\Seeders\LaravelVersionSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Collection;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(ThrottleRequests::class);
    }

    protected function seedVersions($majorCount = 1, $minorCount = 1, $patchCount = 1): Collection
    {
        return (new LaravelVersionSeeder)->versions()->slice(0, $majorCount)->map(function ($version) use ($minorCount, $patchCount) {
            $front = LaravelVersion::factory()->create($version);

            if ($front->pre_semver) {
                $this->seedPatchesForVersion($patchCount, $front->replicate());
            } else {
                $this->seedMinorsForVersion($minorCount, $front->replicate())
                    ->each(fn ($minor) => $this->seedPatchesForVersion($patchCount, $minor));
            }

            return $front;
        });
    }

    private function seedMinorsForVersion(int $minorCount, LaravelVersion $version): Collection
    {
        return collect(range(1, $minorCount))
            ->map(fn ($minor) => LaravelVersion::factory()
                ->create(tap($version, fn ($version) => $version->minor = $minor)->makeHidden('id')->toArray()));
    }

    private function seedPatchesForVersion(int $patchCount, LaravelVersion $version): Collection
    {
        return collect(range(1, $patchCount))
            ->map(fn ($patch) => LaravelVersion::factory()
                ->create(tap($version, fn ($version) => $version->patch = $patch)->makeHidden('id')->toArray()));
    }
}
