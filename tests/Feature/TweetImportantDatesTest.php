<?php

namespace Tests\Feature;

use App\Models\LaravelVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TweetImportantDatesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_tweets_on_the_day_security_fixes_end(): void
    {
        Http::fake();

        $version = LaravelVersion::factory()->active()->create([
            'ends_securityfixes_at' => now()->startOfDay(),
        ]);

        Artisan::call('tweet-important-dates');

        Http::assertSent(function (Request $request) use ($version) {
            return Str::contains($request['text'], "Laravel version {$version->major} will stop receiving security fixes today.");
        });
    }

    #[Test]
    public function it_tweets_the_day_before_security_fixes_end(): void
    {
        Http::fake();

        $version = LaravelVersion::factory()->active()->create([
            'ends_securityfixes_at' => now()->addDay()->startOfDay(),
        ]);

        Artisan::call('tweet-important-dates');

        Http::assertSent(function (Request $request) use ($version) {
            return Str::contains($request['text'], "Laravel version {$version->major} will stop receiving security fixes tomorrow.");
        });
    }

    #[Test]
    public function it_tweets_the_week_before_security_fixes_end(): void
    {
        Http::fake();

        $version = LaravelVersion::factory()->active()->create([
            'ends_securityfixes_at' => now()->addWeek()->startOfDay(),
        ]);

        Artisan::call('tweet-important-dates');

        Http::assertSent(function (Request $request) use ($version) {
            return Str::contains($request['text'], "Laravel version {$version->major} will stop receiving security fixes in one week.");
        });
    }

    #[Test]
    public function it_tweets_on_the_day_bug_fixes_end(): void
    {
        Http::fake();

        $version = LaravelVersion::factory()->active()->create([
            'ends_bugfixes_at' => now()->startOfDay(),
        ]);

        Artisan::call('tweet-important-dates');

        Http::assertSent(function (Request $request) use ($version) {
            return Str::contains($request['text'], "Laravel version {$version->major} will stop receiving bug fixes today.");
        });
    }

    #[Test]
    public function it_tweets_the_day_before_bug_fixes_end(): void
    {
        Http::fake();

        $version = LaravelVersion::factory()->active()->create([
            'ends_bugfixes_at' => now()->addDay()->startOfDay(),
        ]);

        Artisan::call('tweet-important-dates');

        Http::assertSent(function (Request $request) use ($version) {
            return Str::contains($request['text'], "Laravel version {$version->major} will stop receiving bug fixes tomorrow.");
        });
    }

    #[Test]
    public function it_tweets_the_week_before_bug_fixes_end(): void
    {
        Http::fake();

        $version = LaravelVersion::factory()->active()->create([
            'ends_bugfixes_at' => now()->addWeek()->startOfDay(),
        ]);

        Artisan::call('tweet-important-dates');

        Http::assertSent(function (Request $request) use ($version) {
            return Str::contains($request['text'], "Laravel version {$version->major} will stop receiving bug fixes in one week.");
        });
    }
}
