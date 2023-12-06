<?php

namespace Database\Seeders;

use App\Models\LaravelVersion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class LaravelVersionSeeder extends Seeder
{
    public function run(): void
    {
        LaravelVersion::truncate();

        $this->versions()->each(fn ($version) => LaravelVersion::create($version));
    }

    public function versions(): Collection
    {
        return collect(json_decode(\File::get(base_path('manual-version-info.json')), true))->map(function ($version) {
            $release = str_contains($version['release'], '.') ? $version['release'] : $version['release'] . '.0';
            [$major, $minor] = explode('.', $release);
            unset($version['release']);
            $version['major'] = $major;
            $version['minor'] = $minor;
            return $version;
        });
    }
}
