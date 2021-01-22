<?php

use App\Models\LaravelVersion;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $versions = Cache::remember(3600, 'versions', function () {
        return LaravelVersion::orderBy('major', 'desc')->orderBy('minor', 'desc')->get();
    });

    $activeVersions = $versions->filter(function ($version) {
        return $version->ends_securityfixes_at && $version->ends_securityfixes_at->gt(now()) || $version->released_at->gt(now());
    });

    $inActiveVersions = $versions->filter(function ($version) {
        return $version->released_at->lt(now()) &&
            (! $version->ends_securityfixes_at || $version->ends_securityfixes_at->lt(now()));
    });

    return view('welcome', [
        'activeVersions' => $activeVersions,
        'inactiveVersions' => $inActiveVersions,
    ]);
});
