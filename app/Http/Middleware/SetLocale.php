<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->detect($request);

        if ($locale) {
            App::setLocale($locale);
            session()->put('locale', $locale);
            cookie()->queue(cookie('locale', $locale, 60 * 24 * 365));
        }

        return $next($request);
    }

    protected function detect(Request $request): ?string
    {
        $supported = Config::get('localized-routes.supported_locales', []);

        // 1. Check route action (set by Route::localized())
        $route = $request->route();
        if ($route && ($locale = $route->getAction('locale')) && in_array($locale, $supported)) {
            return $locale;
        }

        // 2. Check URL segment
        $segment = $request->segment(1);
        if ($segment && in_array($segment, $supported)) {
            return $segment;
        }

        // 3. Check session
        $sessionLocale = session('locale');
        if ($sessionLocale && in_array($sessionLocale, $supported)) {
            return $sessionLocale;
        }

        // 4. Check cookie
        $cookieLocale = $request->cookie('locale');
        if ($cookieLocale && in_array($cookieLocale, $supported)) {
            return $cookieLocale;
        }

        // 5. Check browser Accept-Language
        $preferred = $request->getPreferredLanguage($supported);
        if ($preferred) {
            return $preferred;
        }

        // 6. Fall back to app locale
        return App::getLocale();
    }
}
