<?php

use App\Http\Controllers\Coordinator\ApplicationController;
use App\Http\Controllers\Coordinator\AssessmentController;
use App\Http\Controllers\Coordinator\DashboardController;
use App\Http\Controllers\Coordinator\DocumentController;
use App\Http\Controllers\Coordinator\EnrollmentController;
use App\Http\Controllers\Coordinator\GraduationController;
use App\Http\Controllers\Coordinator\InterviewController;
use App\Http\Controllers\Coordinator\ReportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:coordinator'])->prefix('coordinator')->name('coordinator.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::get('applications/{application}', [ApplicationController::class, 'show'])->name('applications.show');
    Route::put('applications/{application}/review', [ApplicationController::class, 'review'])->name('applications.review');

    Route::put('documents/{document}/verify', [DocumentController::class, 'verify'])->name('documents.verify');

    Route::post('applications/{application}/assessments/first', [AssessmentController::class, 'submitFirst'])->name('assessments.first');
    Route::post('applications/{application}/assessments/second', [AssessmentController::class, 'submitSecond'])->name('assessments.second');

    Route::get('interviews', [InterviewController::class, 'index'])->name('interviews.index');
    Route::put('interviews/{interview}/schedule', [InterviewController::class, 'schedule'])->name('interviews.schedule');

    Route::get('enrollments', [EnrollmentController::class, 'index'])->name('enrollments.index');
    Route::post('enrollments', [EnrollmentController::class, 'store'])->name('enrollments.store');

    Route::get('graduation', [GraduationController::class, 'pipeline'])->name('graduation.pipeline');
    Route::get('graduation/{clearance}', [GraduationController::class, 'show'])->name('graduation.show');
    Route::post('graduation', [GraduationController::class, 'store'])->name('graduation.store');
    Route::put('graduation/{clearance}/documents/{document}/verify', [GraduationController::class, 'verifyDocument'])->name('graduation.documents.verify');
    Route::post('graduation/{clearance}/confirm', [GraduationController::class, 'confirm'])->name('graduation.confirm');

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/{type}', [ReportController::class, 'show'])->name('reports.show');
});
