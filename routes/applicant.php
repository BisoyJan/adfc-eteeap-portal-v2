<?php

use App\Http\Controllers\Applicant\ApplicationController;
use App\Http\Controllers\Applicant\DashboardController;
use App\Http\Controllers\Applicant\DocumentController;
use App\Http\Controllers\Applicant\EducationRecordController;
use App\Http\Controllers\Applicant\ExperienceRecordController;
use App\Http\Controllers\Applicant\PortfolioController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:applicant'])->prefix('applicant')->name('applicant.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('application', [ApplicationController::class, 'edit'])->name('application.edit');
    Route::put('application', [ApplicationController::class, 'update'])->name('application.update');
    Route::post('application/submit', [ApplicationController::class, 'submit'])->name('application.submit');

    Route::post('experience-records', [ExperienceRecordController::class, 'store'])->name('experience-records.store');
    Route::put('experience-records/{experienceRecord}', [ExperienceRecordController::class, 'update'])->name('experience-records.update');
    Route::delete('experience-records/{experienceRecord}', [ExperienceRecordController::class, 'destroy'])->name('experience-records.destroy');

    Route::post('education-records', [EducationRecordController::class, 'store'])->name('education-records.store');
    Route::put('education-records/{educationRecord}', [EducationRecordController::class, 'update'])->name('education-records.update');
    Route::delete('education-records/{educationRecord}', [EducationRecordController::class, 'destroy'])->name('education-records.destroy');

    Route::get('documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::post('documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::delete('documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

    Route::get('portfolio', [PortfolioController::class, 'show'])->name('portfolio.show');
    Route::post('portfolio/submit', [PortfolioController::class, 'submit'])->name('portfolio.submit');
    Route::get('portfolio/download', [PortfolioController::class, 'download'])->name('portfolio.download');
});
