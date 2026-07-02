<?php

use App\Http\Controllers\Student\ActivityController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\EnrollmentController;
use App\Http\Controllers\Student\GradeController;
use App\Http\Controllers\Student\GraduationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('activities', [ActivityController::class, 'index'])->name('activities.index');
    Route::post('activities/{activity}/submit', [ActivityController::class, 'submit'])->name('activities.submit');

    Route::get('curriculum-checklist', [EnrollmentController::class, 'checklist'])->name('curriculum-checklist');
    Route::get('grades', [GradeController::class, 'index'])->name('grades.index');

    Route::get('graduation', [GraduationController::class, 'show'])->name('graduation.show');
    Route::post('graduation/clearance/submit', [GraduationController::class, 'submitClearance'])->name('graduation.clearance.submit');
    Route::get('graduation/diploma/download', [GraduationController::class, 'downloadDiploma'])->name('graduation.diploma.download');
    Route::get('graduation/tor/download', [GraduationController::class, 'downloadTor'])->name('graduation.tor.download');
});
