<?php

use App\LaravelVersionFromPath;
use App\Models\LaravelVersion;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $versions = Cache::remember(3600, 'laravel-versions', function () {
        return LaravelVersion::orderBy('major', 'desc')->orderBy('minor', 'desc')->get();
    });

    $activeVersions = $versions->filter(function ($version) {
        return $version->released_at->gt(now())
            || ($version->ends_securityfixes_at&& $version->ends_securityfixes_at->gt(now()));
    });

    $inActiveVersions = $versions->filter(function ($version) {
        return $version->released_at->lt(now()) &&
            (! $version->ends_securityfixes_at || $version->ends_securityfixes_at->lt(now()));
    });

    return view('versions.index', [
        'activeVersions' => $activeVersions,
        'inactiveVersions' => $inActiveVersions,
    ]);
});

Route::get('{version}', function ($path) {
    [$version, $sanitizedPath, $segments] = (new LaravelVersionFromPath())($path);

    return view('versions.show', [
        'version' => $version,
        'path' => $sanitizedPath,
        'segments' => $segments,
    ]);
});
