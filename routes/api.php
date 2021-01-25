<?php

use App\Http\Controllers\Api\LaravelVersionsController;
use Illuminate\Support\Facades\Route;

Route::get('versions', [LaravelVersionsController::class, 'index'])->name('versions.index');
Route::get('versions/{version}', [LaravelVersionsController::class, 'show'])->name('versions.show');
