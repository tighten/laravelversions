<?php

use App\Models\LaravelVersion;

it('gets only released versions', function () {
    LaravelVersion::factory()->create([
        'major' => 10,
        'minor' => 0,
        'patch' => 0,
        'released_at' => now()->addYear(),
    ]);

    $yes = LaravelVersion::factory()->create([
        'major' => 9,
        'minor' => 0,
        'patch' => 0,
        'released_at' => now()->subYear(),
    ]);

    $released = LaravelVersion::released()->get();

    expect($released)->toHaveCount(1);
    expect($released->first()->id)->toEqual($yes->id);
});
