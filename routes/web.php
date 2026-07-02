<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:applicant')->prefix('applicant')->name('applicant.')->group(function () {
        Route::inertia('dashboard', 'applicant/dashboard')->name('dashboard');
    });

    Route::middleware('role:coordinator')->prefix('coordinator')->name('coordinator.')->group(function () {
        Route::inertia('dashboard', 'coordinator/dashboard')->name('dashboard');
    });

    Route::middleware('role:assessor_internal,assessor_external')->prefix('assessor')->name('assessor.')->group(function () {
        Route::inertia('dashboard', 'assessor/dashboard')->name('dashboard');
    });

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::inertia('dashboard', 'admin/dashboard')->name('dashboard');
    });
});

require __DIR__.'/settings.php';
