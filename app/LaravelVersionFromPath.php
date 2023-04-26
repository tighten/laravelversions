<?php

namespace App;

use App\Exceptions\FixableInvalidVersionException;
use App\Models\LaravelVersion;
use Exception;
use PHLAK\SemVer\Version;

class LaravelVersionFromPath
{
    public function __invoke($path)
    {
        try {
            $semver = new Version(str($path)->explode('.')->pad(3, 0)->implode('.'));
        } catch (Exception) {
            abort(404);
        }

        if ($semver->major < 6 && (string) $semver->major === $path) {
            $routeName = request()->route()->getPrefix() === 'api' ? 'api.versions.show' : 'versions.show';

            throw FixableInvalidVersionException::toUrl(route($routeName, "{$semver->major}.{$semver->minor}"));
        }

        return LaravelVersion::withoutGlobalScope('front')->where([
            'major' => $semver->major,
            'minor' => $semver->minor,
            'patch' => $semver->patch,
        ])->firstOrFail();
    }
}
