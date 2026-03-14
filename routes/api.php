<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\OpsController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/auth/login', [AuthController::class, 'login'])->name('api.v1.auth.login');
    Route::get('/ops/health', [OpsController::class, 'health'])->name('api.v1.ops.health');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('/auth/me', [AuthController::class, 'me'])->name('api.v1.auth.me');
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.v1.auth.logout');

        Route::get('/orders', fn () => response()->json([
            'success' => true,
            'data' => [],
            'meta' => ['total' => 0],
        ]))->name('api.v1.orders.index');

        Route::get('/kpi/overview', fn () => response()->json([
            'success' => true,
            'data' => [
                'orders_total' => 0,
                'orders_delivered' => 0,
                'delivery_success_rate_pct' => 0,
            ],
        ]))->name('api.v1.kpi.overview');
    });
});
