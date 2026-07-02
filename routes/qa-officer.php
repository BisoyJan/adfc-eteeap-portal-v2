<?php

use App\Http\Controllers\QaOfficer\DashboardController;
use App\Http\Controllers\QaOfficer\EnhancementPlanController;
use App\Http\Controllers\QaOfficer\EquivalencyController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:qa_officer'])->prefix('qa-officer')->name('qa-officer.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('validations', [EquivalencyController::class, 'pending'])->name('validations.pending');
    Route::get('applications/{application}/equivalency', [EquivalencyController::class, 'show'])->name('equivalency.show');
    Route::put('applications/{application}/equivalency', [EquivalencyController::class, 'store'])->name('equivalency.update');
    Route::post('applications/{application}/equivalency/approve', [EquivalencyController::class, 'approve'])->name('equivalency.approve');

    // Phase 4: Enhancement Plan
    Route::get('enhancement-plan', [EnhancementPlanController::class, 'overview'])->name('enhancement-plan.overview');
});
