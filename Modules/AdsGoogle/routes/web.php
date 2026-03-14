<?php

use Illuminate\Support\Facades\Route;
use Modules\AdsGoogle\Http\Controllers\AdsGoogleController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('AdsGoogles', AdsGoogleController::class)->names('AdsGoogle');
});

