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

        $validPaths = collect([
            $semver->major > 5 ? "{$semver->major}" : null,
            "{$semver->major}.{$semver->minor}",
            "{$semver->major}.{$semver->minor}.{$semver->patch}"
        ])->filter();

        abort_unless($validPaths->containsStrict($path),404);

        return LaravelVersion::withoutGlobalScope('front')->where([
            'major' => $semver->major,
            'minor' => $semver->minor,
            'patch' => $semver->patch,
        ])->firstOrFail();
    }
}
