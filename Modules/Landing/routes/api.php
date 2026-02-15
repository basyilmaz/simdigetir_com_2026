<?php

use Illuminate\Support\Facades\Route;
use Modules\Landing\Http\Controllers\LandingController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('landings', LandingController::class)->names('landing');
});
