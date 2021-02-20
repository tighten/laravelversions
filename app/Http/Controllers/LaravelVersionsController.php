<?php

namespace App\Http\Controllers;

use App\LaravelVersionFromPath;
use App\Models\LaravelVersion;
use Illuminate\Support\Facades\Cache;

class LaravelVersionsController extends Controller
{
    public function index()
    {
        $versions = Cache::remember(3600, 'laravel-versions', function () {
            return LaravelVersion::orderBy('major', 'desc')->orderBy('minor', 'desc')->get();
        });

        $activeVersions = $versions->filter(function ($version) {
            return $version->released_at->gt(now())
                || ($version->ends_securityfixes_at && $version->ends_securityfixes_at->gt(now()));
        });

        $inActiveVersions = $versions->filter(function ($version) {
            return $version->released_at->lt(now()) &&
                (! $version->ends_securityfixes_at || $version->ends_securityfixes_at->lt(now()));
        });

        return view('versions.index', [
            'activeVersions' => $activeVersions,
            'inactiveVersions' => $inActiveVersions,
        ]);
    }

    public function show($path)
    {
        [$version, $sanitizedPath, $segments] = (new LaravelVersionFromPath())($path);

        return view('versions.show', [
            'version' => $version,
            'path' => $sanitizedPath,
            'segments' => $segments,
        ]);
    }
}
