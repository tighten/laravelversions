<?php

use App\Console\Commands\FetchLatestReleaseNumbers;
use App\Console\Commands\TweetImportantDates;
use Illuminate\Support\Facades\Schedule;

Schedule::command(FetchLatestReleaseNumbers::class)->hourly();
Schedule::command(TweetImportantDates::class)->dailyAt('08:00')->timezone('America/New_York');
