<?php

use Illuminate\Support\Facades\Route;
use Modules\Leads\Http\Controllers\LeadApiController;

/*
|--------------------------------------------------------------------------
| Leads API Routes
|--------------------------------------------------------------------------
*/

// Public endpoint - lead oluşturma (rate limited)
Route::post('leads', [LeadApiController::class, 'store'])->name('api.leads.store');
