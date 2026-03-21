<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

class LocalizedFallbackController extends Controller
{
    public function __invoke(Request $request)
    {
        if (Config::get('localized-routes.redirect_to_localized_urls')) {
            $localizedUrl = Route::localizedUrl();
            $matched = Route::getRoutes()->match(Request::create($localizedUrl));

            if (! $matched->isFallback) {
                return redirect($localizedUrl, 301)
                    ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
                    ->header('Vary', 'Accept-Language');
            }
        }

        abort(404);
    }
}
