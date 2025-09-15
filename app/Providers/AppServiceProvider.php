<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /** Bootstrap any application services. */
    public function boot(): void
    {
        DB::macro('concat', function (...$parts) {
            return match (config('database.default')) {
                'sqlite' => DB::raw(implode(' || ', $parts)),
                default => DB::raw('CONCAT('.implode(', ', $parts).')'),
            };
        });
    }
}
