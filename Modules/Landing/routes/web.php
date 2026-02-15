<?php

use Illuminate\Support\Facades\Route;
use Modules\Landing\Http\Controllers\LandingController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('landings', LandingController::class)->names('landing');
});
