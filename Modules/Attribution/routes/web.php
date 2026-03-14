<?php

use Illuminate\Support\Facades\Route;
use Modules\Attribution\Http\Controllers\AttributionController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('Attributions', AttributionController::class)->names('Attribution');
});

