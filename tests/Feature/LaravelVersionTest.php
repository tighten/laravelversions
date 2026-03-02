<?php

use App\Models\LaravelVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

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

    $this->assertCount(1, $released);
    $this->assertEquals($yes->id, $released->first()->id);
});
