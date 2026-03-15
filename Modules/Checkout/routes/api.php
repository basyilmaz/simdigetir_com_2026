<?php

use Illuminate\Support\Facades\Route;
use Modules\Checkout\Http\Controllers\CheckoutController;
use Modules\Checkout\Http\Controllers\CheckoutPaymentController;
use Modules\Checkout\Http\Controllers\OrderTrackingLookupController;

Route::prefix('v1')->group(function () {
    Route::post('/checkout-sessions', [CheckoutController::class, 'store'])->name('v1.checkout-sessions.store');
    Route::get('/checkout-sessions/{checkoutSession:token}', [CheckoutController::class, 'show'])->name('v1.checkout-sessions.show');
    Route::patch('/checkout-sessions/{checkoutSession:token}', [CheckoutController::class, 'update'])->name('v1.checkout-sessions.update');
    Route::post('/checkout-sessions/{checkoutSession:token}/finalize', [CheckoutController::class, 'finalize'])->name('v1.checkout-sessions.finalize');
    Route::post('/checkout-sessions/{checkoutSession:token}/payments/initiate', [CheckoutPaymentController::class, 'initiate'])->name('v1.checkout-sessions.payments.initiate');
    Route::get('/order-tracking', [OrderTrackingLookupController::class, 'show'])->name('v1.order-tracking.show');
});
