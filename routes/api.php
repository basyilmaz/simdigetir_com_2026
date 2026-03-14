<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CorporateAccountController;
use App\Http\Controllers\Api\V1\CourierController;
use App\Http\Controllers\Api\V1\DispatchController;
use App\Http\Controllers\Api\V1\FinanceController;
use App\Http\Controllers\Api\V1\KpiController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\OpsController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\PricingQuoteController;
use App\Http\Controllers\Api\V1\SupportController;
use App\Http\Controllers\Api\V1\TrackingController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/auth/login', [AuthController::class, 'login'])->name('api.v1.auth.login');
    Route::get('/ops/health', [OpsController::class, 'health'])->name('api.v1.ops.health');
    Route::post('/quotes', [PricingQuoteController::class, 'store'])
        ->middleware('throttle:30,1')
        ->name('api.v1.quotes.store');
    Route::post('/couriers/apply', [CourierController::class, 'apply'])->name('api.v1.couriers.apply');
    Route::post('/payments/callback/{provider}', [PaymentController::class, 'callback'])->name('api.v1.payments.callback');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('/auth/me', [AuthController::class, 'me'])->name('api.v1.auth.me');
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.v1.auth.logout');

        Route::get('/orders', [OrderController::class, 'index'])->name('api.v1.orders.index');
        Route::post('/orders', [OrderController::class, 'store'])->name('api.v1.orders.store');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('api.v1.orders.show');
        Route::get('/orders/{order}/timeline', [OrderController::class, 'timeline'])->name('api.v1.orders.timeline');
        Route::post('/orders/{order}/transition', [OrderController::class, 'transition'])->name('api.v1.orders.transition');
        Route::post('/orders/{order}/tracking-events', [TrackingController::class, 'store'])->name('api.v1.orders.tracking.store');

        Route::post('/payments/initiate', [PaymentController::class, 'initiate'])->name('api.v1.payments.initiate');
        Route::post('/payments/{order}/retry', [PaymentController::class, 'retry'])->name('api.v1.payments.retry');

        Route::post('/dispatch/orders/{order}/auto-assign', [DispatchController::class, 'autoAssign'])->name('api.v1.dispatch.auto-assign');
        Route::post('/dispatch/orders/{order}/manual-assign', [DispatchController::class, 'manualAssign'])->name('api.v1.dispatch.manual-assign');
        Route::post('/dispatch/reassign-overdue', [DispatchController::class, 'reassignOverdue'])->name('api.v1.dispatch.reassign-overdue');

        Route::post('/couriers/{courier}/availability', [CourierController::class, 'setAvailability'])->name('api.v1.couriers.availability');
        Route::get('/couriers/{courier}/tasks', [CourierController::class, 'tasks'])->name('api.v1.couriers.tasks');
        Route::post('/couriers/{courier}/orders/{order}/accept', [CourierController::class, 'accept'])->name('api.v1.couriers.orders.accept');
        Route::post('/couriers/{courier}/orders/{order}/reject', [CourierController::class, 'reject'])->name('api.v1.couriers.orders.reject');
        Route::post('/couriers/{courier}/orders/{order}/pickup', [CourierController::class, 'pickup'])->name('api.v1.couriers.orders.pickup');
        Route::post('/couriers/{courier}/orders/{order}/deliver', [CourierController::class, 'deliver'])->name('api.v1.couriers.orders.deliver');
        Route::get('/couriers/{courier}/earnings-summary', [CourierController::class, 'earningsSummary'])->name('api.v1.couriers.earnings-summary');

        Route::post('/finance/settlements/run', [FinanceController::class, 'runSettlement'])->name('api.v1.finance.settlements.run');
        Route::get('/finance/couriers/{courier}/wallet', [FinanceController::class, 'courierWallet'])->name('api.v1.finance.couriers.wallet');
        Route::post('/finance/payments/reconcile', [FinanceController::class, 'reconcilePayment'])->name('api.v1.finance.payments.reconcile');
        Route::post('/finance/payments/{transaction}/refund', [FinanceController::class, 'refund'])->name('api.v1.finance.payments.refund');

        Route::post('/notifications/templates/upsert', [NotificationController::class, 'upsertTemplate'])->name('api.v1.notifications.templates.upsert');
        Route::post('/notifications/dispatch', [NotificationController::class, 'dispatch'])->name('api.v1.notifications.dispatch');

        Route::post('/corporate/accounts', [CorporateAccountController::class, 'store'])->name('api.v1.corporate.accounts.store');
        Route::post('/support/tickets', [SupportController::class, 'storeTicket'])->name('api.v1.support.tickets.store');
        Route::post('/support/tickets/{ticket}/messages', [SupportController::class, 'addMessage'])->name('api.v1.support.tickets.messages.store');

        Route::get('/kpi/overview', [KpiController::class, 'overview'])->name('api.v1.kpi.overview');
    });
});
