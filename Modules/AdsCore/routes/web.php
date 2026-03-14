<?php

use Illuminate\Support\Facades\Route;
use Modules\AdsCore\Http\Controllers\AdsCoreController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('adscores', AdsCoreController::class)->names('adscore');
});
