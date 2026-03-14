<?php

use Illuminate\Support\Facades\Route;
use Modules\AdsCore\Http\Controllers\AdsCoreController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('adscores', AdsCoreController::class)->names('adscore');
});
