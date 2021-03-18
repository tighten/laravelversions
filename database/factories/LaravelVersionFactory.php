<?php

namespace Database\Factories;

use App\Models\LaravelVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

class LaravelVersionFactory extends Factory
{
    protected $model = LaravelVersion::class;

    public function definition()
    {
        $released_at = now()->subDays(rand(0, 1000));

        return [
            'major' => rand(1, 8),
            'minor' => rand(0, 10),
            'patch' => rand(0, 80),
            'is_lts' => false,
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
                'ends_bugfixes_at' => $futureDate,
                'ends_securityfixes_at' => $futureDate,
            ];
        });
    }
}
