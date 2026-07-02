<?php

use App\Http\Controllers\Vpaa\DashboardController;
use App\Http\Controllers\Vpaa\GraduationController;
use App\Http\Controllers\Vpaa\ReportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:vpaa'])->prefix('vpaa')->name('vpaa.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('approvals', [GraduationController::class, 'pendingApprovals'])->name('approvals.index');
    Route::put('approvals/{clearance}/approve', [GraduationController::class, 'approve'])->name('approvals.approve');

    Route::get('graduation-audit', [GraduationController::class, 'audit'])->name('graduation-audit');

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/{type}', [ReportController::class, 'show'])->name('reports.show');
});
