<?php

use App\Http\Controllers\Adviser\ActivityController;
use App\Http\Controllers\Adviser\DashboardController;
use App\Http\Controllers\Adviser\GradeController;
use App\Http\Controllers\Adviser\RubricController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:adviser'])->prefix('adviser')->name('adviser.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('activities', [ActivityController::class, 'index'])->name('activities.index');
    Route::get('activities/create', [ActivityController::class, 'create'])->name('activities.create');
    Route::post('activities', [ActivityController::class, 'store'])->name('activities.store');
    Route::get('activities/{activity}', [ActivityController::class, 'show'])->name('activities.show');
    Route::put('activities/{activity}', [ActivityController::class, 'update'])->name('activities.update');
    Route::put('activities/{activity}/score', [ActivityController::class, 'score'])->name('activities.score');
    Route::get('activities/submissions/pending', [ActivityController::class, 'pendingSubmissions'])->name('activities.submissions');

    Route::get('rubrics', [RubricController::class, 'index'])->name('rubrics.index');
    Route::get('rubrics/create', [RubricController::class, 'create'])->name('rubrics.create');
    Route::post('rubrics', [RubricController::class, 'store'])->name('rubrics.store');

    Route::get('grades', [GradeController::class, 'index'])->name('grades.index');
    Route::get('grades/create', [GradeController::class, 'create'])->name('grades.create');
    Route::post('grades', [GradeController::class, 'store'])->name('grades.store');
    Route::put('grades/{grade}', [GradeController::class, 'update'])->name('grades.update');
    Route::post('grades/{grade}/submit', [GradeController::class, 'submit'])->name('grades.submit');
});
