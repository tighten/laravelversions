<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

class Lang
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $lang = $request->segment(1);

        if (app()->getLocale() == $lang) {
            return redirect()->route(rtrim(Route::currentRouteName(), '.lang'), ['version' => $request->segment(2)]);
        }

        App::setlocale($lang);
        $request->setlocale($lang);

        return $next($request);
    }
}
