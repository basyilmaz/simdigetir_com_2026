<?php

use Illuminate\Support\Facades\Route;
use Modules\AdsMeta\Http\Controllers\AdsMetaController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('AdsMetas', AdsMetaController::class)->names('AdsMeta');
});

