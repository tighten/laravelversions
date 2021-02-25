<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LaravelVersionResource;
use App\LaravelVersionFromPath;
use App\Models\LaravelVersion;
use Illuminate\Support\Facades\Cache;

class LaravelVersionsController extends Controller
{
    public function index()
    {
        $versions = Cache::remember('laravel-versions', 3600, function () {
            return LaravelVersion::released()->orderBy('major', 'desc')->orderBy('minor', 'desc')->get();
        });

        return LaravelVersionResource::collection($versions);
    }

    public function show($path)
    {
        [$version] = (new LaravelVersionFromPath())($path);

        return new LaravelVersionResource($version);
    }
}
