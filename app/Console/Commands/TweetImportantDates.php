<?php

namespace App\Console\Commands;

use App\Models\LaravelVersion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TweetImportantDates extends Command
{
    protected $signature = 'tweet-important-dates';

    protected $description = 'Tweet out any important dates today.';

    public function handle()
    {
        $this->versionsNeedingNotification()->each(function (LaravelVersion $version) {
            $this->tweetForVersion($version);
        });
    }

    public function versionsNeedingNotification()
    {
        // This could likely eventaully be a scope
        return LaravelVersion::all()->filter(function ($version) {
            return $version->needsNotification();
        });
    }

    public function tweetForVersion(LaravelVersion $version)
    {
        foreach (LaravelVersion::notificationDays() as $label => $day) {
            if ($version->ends_securityfixes_at->eq($day)) {
                $message = sprintf(
                    '⚠️ Laravel version %s will stop receiving security fixes %s. Be sure to upgrade soon!',
                    $version->major,
                    $label
                );
                break;
            }

            if ($version->ends_bugfixes_at->eq($day)) {
                $message = sprintf(
                    '⚠️ Laravel version %s will stop receiving bug fixes %s. Be sure to upgrade soon! Its security fix end date is %s.',
                    $version->major,
                    $label,
                    $version->ends_securityfixes_at->format('F j, Y')
                );
                break;
            }
        }

        Http::post(config('services.zapier.twitter_webhook_url'), ['text' => $message]);
    }
}
