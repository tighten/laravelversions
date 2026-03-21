<?php

namespace App\Providers;

use App\Support\LocalizedUrlGenerator;
use Closure;
use Illuminate\Contracts\Routing\UrlGenerator as UrlGeneratorContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class LocalizedRoutesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerUrlGenerator();
    }

    public function boot(): void
    {
        $this->registerLocalizedMacro();
        $this->registerLocalizedUrlMacro();
    }

    protected function registerUrlGenerator(): void
    {
        $this->app->singleton('url', function ($app) {
            $routes = $app['router']->getRoutes();
            $app->instance('routes', $routes);

            $url = new LocalizedUrlGenerator(
                $routes,
                $app->rebinding('request', function ($app, $request) {
                    $app['url']->setRequest($request);
                }),
                $app['config']['app.asset_url']
            );

            return $url;
        });

        $this->app->extend('url', function (UrlGeneratorContract $url) {
            $url->setSessionResolver(function () {
                return $this->app['session'] ?? null;
            });

            $url->setKeyResolver(function () {
                return $this->app->make('config')->get('app.key');
            });

            $this->app->rebinding('routes', function ($app, $routes) {
                $app['url']->setRoutes($routes);
            });

            return $url;
        });
    }

    protected function registerLocalizedMacro(): void
    {
        Route::macro('localized', function (Closure $closure) {
            $locales = Config::get('localized-routes.supported_locales', []);
            $currentLocale = App::getLocale();

            foreach ($locales as $locale) {
                App::setLocale($locale);

                Route::group([
                    'as' => "{$locale}.",
                    'prefix' => $locale,
                    'locale' => $locale,
                ], $closure);
            }

            App::setLocale($currentLocale);
        });
    }

    protected function registerLocalizedUrlMacro(): void
    {
        Route::macro('localizedUrl', function (?string $locale = null, $parameters = null, bool $absolute = true) {
            $request = app(Request::class);
            $route = $request->route();
            $supported = Config::get('localized-routes.supported_locales', []);

            // Determine target locale
            $locale = $locale
                ?? (in_array($request->segment(1), $supported) ? $request->segment(1) : null)
                ?? App::getLocale();

            // If we have a named route, generate via route name
            if ($route && ! $route->isFallback && $route->getName()) {
                $routeName = $route->getName();

                // Strip existing locale prefix from route name
                $parts = explode('.', $routeName);
                if (in_array($parts[0], $supported)) {
                    array_shift($parts);
                }
                $baseName = implode('.', $parts);

                $localizedName = "{$locale}.{$baseName}";

                if (Route::has($localizedName)) {
                    $routeParams = $parameters ?? $route->parameters();

                    // Resolve UrlRoutable models to their route keys
                    foreach ($routeParams as $key => $param) {
                        if ($param instanceof \Illuminate\Contracts\Routing\UrlRoutable) {
                            $bindingField = $route->bindingFieldFor($key);
                            $routeParams[$key] = $bindingField ? $param->$bindingField : $param->getRouteKey();
                        }
                    }

                    $url = route($localizedName, $routeParams, $absolute);

                    // Preserve query string
                    $query = $request->getQueryString();
                    if ($query) {
                        $url .= '?' . $query;
                    }

                    return $url;
                }
            }

            // Fallback: swap locale segment in current URL
            $url = parse_url($request->fullUrl());
            $path = trim($url['path'] ?? '/', '/');
            $segments = $path ? explode('/', $path) : [];

            // Replace or prepend locale segment
            if (! empty($segments) && in_array($segments[0], $supported)) {
                $segments[0] = $locale;
            } else {
                array_unshift($segments, $locale);
            }

            $newPath = '/' . implode('/', $segments);

            if ($absolute) {
                $scheme = $url['scheme'] ?? 'http';
                $host = $url['host'] ?? $request->getHost();
                $port = isset($url['port']) && ! in_array($url['port'], [80, 443]) ? ':' . $url['port'] : '';
                $result = "{$scheme}://{$host}{$port}{$newPath}";
            } else {
                $result = $newPath;
            }

            // Preserve query string
            if (isset($url['query'])) {
                $result .= '?' . $url['query'];
            }

            return $result;
        });
    }
}
