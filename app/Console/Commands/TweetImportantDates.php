<?php

namespace App\Console\Commands;

use App\Models\LaravelVersion;
use Illuminate\Console\Command;

class TweetImportantDates extends Command
{
    protected $signature = 'tweet-important-dates';

    protected $description = 'Tweet out any important dates today.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->versionsNeedingNotification()->each(function () {
            // @todo Dispatch a notification
        });
    }

    public function versionsNeedingNotification()
    {
        // This could likely eventaully be a scope
        return LaravelVersion::all()->filter(function ($version) {
            return $version->needsNotification();
        });
    }
}
