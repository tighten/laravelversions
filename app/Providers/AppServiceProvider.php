<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        DB::macro('concat', function (...$parts) {
            switch (config('database.default')) {
                case 'sqlite':
                    return DB::raw(implode(' || ', $parts));
                default:
                    return DB::raw('CONCAT(' . implode(', ', $parts) . ')');
            }
        });
    }
}
