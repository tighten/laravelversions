<?php

namespace App\Http\Controllers;

use App\LaravelVersionFromPath;
use App\Models\LaravelVersion;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class LaravelVersionsController extends Controller
{
    public function index(): View
    {
        $versions = Cache::remember('laravel-versions', 3600, function () {
            return LaravelVersion::query()->orderBy('major', 'desc')->orderBy('minor', 'desc')->get();
        });

        $activeVersions = $versions->filter(function ($version) {
            return $version->released_at->endOfDay()->gt(now())
                || ($version->ends_securityfixes_at && $version->ends_securityfixes_at->endOfDay()->gt(now()));
        });

        $inActiveVersions = $versions->filter(function ($version) {
            return $version->released_at->endOfDay()->lt(now()) &&
                (! $version->ends_securityfixes_at || $version->ends_securityfixes_at->endOfDay()->lt(now()));
        });

        return view('versions.index', [
            'activeVersions' => $activeVersions,
            'inactiveVersions' => $inActiveVersions,
        ]);
    }

    public function show($path): View
    {
        $version = Cache::remember('laravel-versions-' . $path, 3600, function () use ($path) {
            return (new LaravelVersionFromPath)($path);
        });

        $releases = Cache::remember('laravel-versions-' . $version->majorish . '-releases', 3600, function () use ($version) {
            return $version->getReleases();
        });

        return view('versions.show', [
            'version' => $version,
            'path' => (string) $version,
            'releases' => $releases,
        ]);
    }
}
