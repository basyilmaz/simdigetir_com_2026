<?php

use Illuminate\Support\Facades\Route;
use Modules\AdsMeta\Http\Controllers\AdsMetaController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('AdsMetas', AdsMetaController::class)->names('AdsMeta');
});

