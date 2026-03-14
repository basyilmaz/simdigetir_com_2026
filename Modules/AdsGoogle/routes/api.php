<?php

use Illuminate\Support\Facades\Route;
use Modules\AdsGoogle\Http\Controllers\AdsGoogleController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('AdsGoogles', AdsGoogleController::class)->names('AdsGoogle');
});

