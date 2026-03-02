<?php

use App\Models\LaravelVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

it('tweets on the day security fixes end', function () {
    Http::fake();

    $version = LaravelVersion::factory()->active()->create([
        'ends_securityfixes_at' => now()->startOfDay(),
    ]);

    Artisan::call('tweet-important-dates');

    Http::assertSent(function (Request $request) use ($version) {
        return Str::contains($request['text'], "Laravel version {$version->major} will stop receiving security fixes today.");
    });
});

it('tweets the day before security fixes end', function () {
    Http::fake();

    $version = LaravelVersion::factory()->active()->create([
        'ends_securityfixes_at' => now()->addDay()->startOfDay(),
    ]);

    Artisan::call('tweet-important-dates');

    Http::assertSent(function (Request $request) use ($version) {
        return Str::contains($request['text'], "Laravel version {$version->major} will stop receiving security fixes tomorrow.");
    });
});

it('tweets the week before security fixes end', function () {
    Http::fake();

    $version = LaravelVersion::factory()->active()->create([
        'ends_securityfixes_at' => now()->addWeek()->startOfDay(),
    ]);

    Artisan::call('tweet-important-dates');

    Http::assertSent(function (Request $request) use ($version) {
        return Str::contains($request['text'], "Laravel version {$version->major} will stop receiving security fixes in one week.");
    });
});

it('tweets on the day bug fixes end', function () {
    Http::fake();

    $version = LaravelVersion::factory()->active()->create([
        'ends_bugfixes_at' => now()->startOfDay(),
    ]);

    Artisan::call('tweet-important-dates');

    Http::assertSent(function (Request $request) use ($version) {
        return Str::contains($request['text'], "Laravel version {$version->major} will stop receiving bug fixes today.");
    });
});

it('tweets the day before bug fixes end', function () {
    Http::fake();

    $version = LaravelVersion::factory()->active()->create([
        'ends_bugfixes_at' => now()->addDay()->startOfDay(),
    ]);

    Artisan::call('tweet-important-dates');

    Http::assertSent(function (Request $request) use ($version) {
        return Str::contains($request['text'], "Laravel version {$version->major} will stop receiving bug fixes tomorrow.");
    });
});

it('tweets the week before bug fixes end', function () {
    Http::fake();

    $version = LaravelVersion::factory()->active()->create([
        'ends_bugfixes_at' => now()->addWeek()->startOfDay(),
    ]);

    Artisan::call('tweet-important-dates');

    Http::assertSent(function (Request $request) use ($version) {
        return Str::contains($request['text'], "Laravel version {$version->major} will stop receiving bug fixes in one week.");
    });
});
