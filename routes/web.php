<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\HeartbeatController;
use App\Http\Controllers\LabsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::inertia('/', 'home')->name('home');

Route::middleware('throttle:120,1')->group(function () {
    Route::post('/analytics/track', [AnalyticsController::class, 'track'])->name('analytics.track');
    Route::post('/analytics/heartbeat', HeartbeatController::class)->name('analytics.heartbeat');
});

Route::middleware('auth')->group(function () {
    Route::redirect('dashboard', '/admin')->name('dashboard');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AnalyticsController::class, 'index'])->name('analytics');
    Route::get('/export', [AnalyticsController::class, 'export'])->name('analytics.export');
    Route::get('/labs', [LabsController::class, 'index'])->name('labs');
});
