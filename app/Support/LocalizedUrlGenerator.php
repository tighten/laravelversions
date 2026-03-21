<?php

namespace App\Support;

use Illuminate\Routing\UrlGenerator as BaseUrlGenerator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

class LocalizedUrlGenerator extends BaseUrlGenerator
{
    public function route($name, $parameters = [], $absolute = true)
    {
        // If the route exists as-is, use it directly
        if (Route::has($name)) {
            return parent::route($name, $parameters, $absolute);
        }

        // Try prefixing with the current locale
        $supported = Config::get('localized-routes.supported_locales', []);
        $locale = App::getLocale();

        // Strip any existing locale prefix
        $parts = explode('.', $name);
        if (in_array($parts[0], $supported)) {
            array_shift($parts);
        }
        $baseName = implode('.', $parts);

        $localizedName = "{$locale}.{$baseName}";
        if (Route::has($localizedName)) {
            return parent::route($localizedName, $parameters, $absolute);
        }

        // Fall back to the original name (will throw RouteNotFoundException)
        return parent::route($name, $parameters, $absolute);
    }
}
