<?php

use App\Http\Controllers\Assessor\DashboardController;
use App\Http\Controllers\Assessor\InterviewController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:assessor_internal,assessor_external'])->prefix('assessor')->name('assessor.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('interviews', [InterviewController::class, 'index'])->name('interviews.index');
    Route::get('interviews/{interview}', [InterviewController::class, 'show'])->name('interviews.show');
    Route::post('interviews/{interview}/validate', [InterviewController::class, 'submitValidation'])->name('interviews.validate');
    Route::post('interviews/{interview}/oral', [InterviewController::class, 'submitOral'])->name('interviews.oral');
});
