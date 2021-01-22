<?php

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

    return view('welcome', [
        'activeVersions' => $activeVersions,
        'inactiveVersions' => $inActiveVersions,
    ]);
});

Route::get('{version}', function ($path) {
    // @todo regex this with someone who's smarter at regex-ing... this is obviously super dumb
    $segments = explode('.', $path);

    if ((int) $segments[0] === 0) {
        abort(404);
    }

    if ($segments[0] < 6) {
        if (! isset($segments[1])) {
            abort(404);
        }

        $version = LaravelVersion::where([
            'major' => $segments[0],
            'minor' => $segments[1],
        ])->firstOrFail();
    } else {
        $version = LaravelVersion::where([
            'major' => $segments[0],
        ])->firstOrFail();
    }

    return view('versions.show', [
        'version' => $version,
        'path' => $path,
        'segments' => $segments,
    ]);
});
