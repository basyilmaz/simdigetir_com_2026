<?php

use Illuminate\Support\Facades\Route;
use Modules\Attribution\Http\Controllers\AttributionController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('Attributions', AttributionController::class)->names('Attribution');
});

