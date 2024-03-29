<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use PHLAK\SemVer\Version;

class LaravelVersionFactory extends Factory
{
    public function definition(): array
    {
        $semver = new Version(implode('.', [rand(1, 11), rand(0, 20), rand(0, 80)]));

        $released_at = now()
            ->subYears(collect(range(1, 11))->reverse()->values()->get($semver->major - 1))
            ->subDays($semver->minor);

        return [
            'major' => $semver->major,
            'minor' => $semver->minor,
            'patch' => $semver->patch,
            'changelog' => null,
            'released_at' => $released_at,
            'ends_bugfixes_at' => $released_at->clone()->addYear(),
            'ends_securityfixes_at' => $released_at->clone()->addYears(2),
        ];
    }

    public function active()
    {
        return $this->state(function (array $attributes) {
            $futureDate = now()->addYear(1);

            return [
                'major' => 99999,
                'minor' => 0,
                'patch' => 0,
                'ends_bugfixes_at' => $futureDate,
                'ends_securityfixes_at' => $futureDate,
            ];
        });
    }
}
