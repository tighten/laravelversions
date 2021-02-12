<?php

use App\Http\Controllers\LaravelVersionsController;
use App\Http\Middleware\Lang;
use Illuminate\Support\Facades\Route;

Route::group([
        'prefix' => '{lang}',
        'middleware' => Lang::class,
        'where' => ['lang' => '^([a-zA-Z]{2})([-_][a-zA-Z]{2})?$'],
    ], function () {
        Route::get('/', [LaravelVersionsController::class, 'index'])->name('versions.index.lang');
        Route::get('{version}', [LaravelVersionsController::class, 'show'])->name('versions.show.lang');
    });

Route::get('/', [LaravelVersionsController::class, 'index'])->name('versions.index');
Route::get('{version}', [LaravelVersionsController::class, 'show'])->name('versions.show');
