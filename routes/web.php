<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::inertia('/', 'home')->name('home');

Route::middleware('auth')->group(function () {
    Route::redirect('dashboard', '/admin')->name('dashboard');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::inertia('/', 'admin/placeholder')->name('analytics');
});
