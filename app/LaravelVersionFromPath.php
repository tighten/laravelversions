<?php

namespace App;

use App\Models\LaravelVersion;

class LaravelVersionFromPath
{
    public function __invoke($path)
    {
        // @todo regex this with someone who's smarter at regex-ing... this is obviously super dumb
        $segments = explode('.', $path);
        $segments = array_slice($segments, 0, 3);
        $sanitizedPath = implode('.', $segments);

        if ($path !== $sanitizedPath) {
            return redirect('/' . $sanitizedPath);
        }

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

        return [
            $version,
            $sanitizedPath,
            $segments,
        ];
    }
}
