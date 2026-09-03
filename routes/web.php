<?php

use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HeartbeatController;
use App\Http\Controllers\LabsController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PaymentCallbackController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::inertia('/', 'home')->name('home');

Route::middleware('throttle:120,1')->group(function () {
    Route::post('/analytics/track', [AnalyticsController::class, 'track'])->name('analytics.track');
    Route::post('/analytics/heartbeat', HeartbeatController::class)->name('analytics.heartbeat');
});

if (config('analytics.mode') === 'form') {
    Route::post('/lead', [LeadController::class, 'store'])->middleware('throttle:20,1')->name('lead.store');
    Route::post('/checkout', [CheckoutController::class, 'store'])->middleware('throttle:20,1')->name('checkout.store');
    Route::get('/payment/return', [CheckoutController::class, 'returnPage'])->name('payment.return');
    Route::post('/payment/callback', [PaymentCallbackController::class, 'handle'])->name('payment.callback');
}

Route::middleware('auth')->group(function () {
    Route::redirect('dashboard', '/admin')->name('dashboard');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AnalyticsController::class, 'index'])->name('analytics');
    Route::get('/export', [AnalyticsController::class, 'export'])->name('analytics.export');
    Route::get('/labs', [LabsController::class, 'index'])->name('labs');
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('/orders/{order}/mark-paid', [AdminOrderController::class, 'markAsPaid'])->name('orders.mark-paid');
});
