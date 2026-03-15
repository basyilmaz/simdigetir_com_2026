<?php

use Illuminate\Support\Facades\Route;
use Modules\Checkout\Http\Controllers\CheckoutPageController;
use Modules\Checkout\Http\Controllers\CustomerPortalDashboardController;
use Modules\Checkout\Http\Controllers\CustomerPortalOrderController;
use Modules\Checkout\Http\Controllers\CustomerPortalSessionController;
use Modules\Checkout\Http\Controllers\OrderTrackingPageController;

Route::get('/checkout/{checkoutSession:token}', [CheckoutPageController::class, 'show'])
    ->name('checkout.show');

Route::get('/siparis-takip', [OrderTrackingPageController::class, 'show'])
    ->name('checkout.tracking');

Route::get('/hesabim/giris', [CustomerPortalSessionController::class, 'showLogin'])
    ->name('checkout.customer.login');
Route::post('/hesabim/giris', [CustomerPortalSessionController::class, 'login'])
    ->name('checkout.customer.login.submit');
Route::get('/hesabim', [CustomerPortalDashboardController::class, 'show'])
    ->name('checkout.customer.dashboard');
Route::get('/hesabim/siparisler/{orderNo}', [CustomerPortalOrderController::class, 'show'])
    ->name('checkout.customer.orders.show');
Route::post('/hesabim/cikis', [CustomerPortalSessionController::class, 'logout'])
    ->name('checkout.customer.logout');
