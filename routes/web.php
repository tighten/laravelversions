<?php

use App\Http\Controllers\LaravelVersionsController;
use App\Http\Controllers\LocalizedFallbackController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::localized(function () {
    Route::get('/', [LaravelVersionsController::class, 'index'])->name('versions.index');
    Route::get('{version}', [LaravelVersionsController::class, 'show'])->name('versions.show');
});

Route::fallback(LocalizedFallbackController::class)->middleware(SetLocale::class);
