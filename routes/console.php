<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('fetch-laravel-versions')->hourly();
Schedule::command('tweet-important-dates')->dailyAt('08:00')->timezone('America/New_York');
