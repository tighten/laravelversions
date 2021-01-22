<?php

use App\Http\Resources\LaravelVersionResource;
use App\Models\LaravelVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::get('versions/laravel', function (Request $request) {
    $versions = Cache::remember(3600, 'laravel-versions', function () {
        return LaravelVersion::orderBy('major', 'desc')->orderBy('minor', 'desc')->get();
    });

    return LaravelVersionResource::collection($versions);
});
