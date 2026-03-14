<?php

use Illuminate\Support\Facades\Route;
use Modules\Reporting\Http\Controllers\ReportingController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('Reportings', ReportingController::class)->names('Reporting');
});

