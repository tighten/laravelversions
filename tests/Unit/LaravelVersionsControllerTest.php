<?php

namespace Tests\Unit;

use App\Http\Controllers\LaravelVersionsController;
use App\Models\LaravelVersion;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class LaravelVersionsControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function index_method_caches_laravel_versions_query_on_3600_seconds()
    {
        LaravelVersion::factory()->count(2)->create();

        $shouldReturn = LaravelVersion::released()->orderBy('major', 'desc')->orderBy('minor', 'desc')->get();

        Cache::shouldReceive('remember')
            ->once()
            ->with('laravel-versions', 3600, Closure::class)
            ->andReturn($shouldReturn);

        app(LaravelVersionsController::class)->index();
    }
}
