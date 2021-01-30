<?php

namespace App\Http\Controllers;

use App\LaravelVersionFromPath;
use App\Models\LaravelVersion;
use Illuminate\Support\Facades\Cache;
use PHLAK\SemVer\Version;

class LaravelVersionsController extends Controller
{
    public function index()
    {
        $versions = Cache::remember('laravel-versions', 3600, function () {
            return LaravelVersion::front()->orderBy('major', 'desc')->orderBy('minor', 'desc')->get();
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
        $laravelVersion = Cache::remember('laravel-versions-' . $path, 3600, function () use ($path) {
            return (new LaravelVersionFromPath())($path);
        });
        [$version, $sanitizedPath, $segments] = $laravelVersion;
        $semver = new Version($version);

        $releases = Cache::remember('laravel-versions-' . $semver . '-releases', 3600, function () use ($segments, $semver) {
            $query = LaravelVersion::where('major', '=', $semver->major)->orderBy('released_at', 'DESC');
            if (count($segments) >= 2) {
                $query->where('minor', '=', $semver->minor);
            }
            if (count($segments) >= 3) {
                $query->where('patch', '=', $semver->patch);
            }

            return $query->get();
        });

        return view('versions.show', [
            'version' => $version,
            'path' => $sanitizedPath,
            'segments' => $segments,
            'releases' => $releases,
        ]);
    }
}
