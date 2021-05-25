<?php

use App\Http\Controllers\LaravelVersionsController;
use CodeZero\LocalizedRoutes\Controller\FallbackController;
use CodeZero\LocalizedRoutes\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::localized(function () {
    Route::get('/', [LaravelVersionsController::class, 'index'])->name('versions.index');
    Route::get('{version}', [LaravelVersionsController::class, 'show'])->name('versions.show');
});

Route::fallback(FallbackController::class)->middleware(SetLocale::class);
