<?php

namespace App;

use App\Models\LaravelVersion;

class LowestSupportedVersion
{
    public function __invoke($greaterThanVersion = null)
    {
        $query = LaravelVersion::where('ends_securityfixes_at', '>', now())
            ->orderBy('major');

        if ($greaterThanVersion) {
            $query->where('major', '>', $greaterThanVersion->major);
        }

        return $query->firstOrFail();
    }
}
