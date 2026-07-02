<?php

use App\Http\Controllers\ProgramHead\AdviserAssignmentController;
use App\Http\Controllers\ProgramHead\AssessorController;
use App\Http\Controllers\ProgramHead\CurriculumController;
use App\Http\Controllers\ProgramHead\DashboardController;
use App\Http\Controllers\ProgramHead\EquivalencyController;
use App\Http\Controllers\ProgramHead\GradeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:program_head'])->prefix('program-head')->name('program-head.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('equivalency', [EquivalencyController::class, 'pending'])->name('equivalency.pending');
    Route::get('applications/{application}/equivalency', [EquivalencyController::class, 'show'])->name('equivalency.show');
    Route::put('applications/{application}/equivalency', [EquivalencyController::class, 'store'])->name('equivalency.update');
    Route::post('applications/{application}/equivalency/submit', [EquivalencyController::class, 'submit'])->name('equivalency.submit');

    Route::get('assessors', [AssessorController::class, 'available'])->name('assessors.available');
    Route::get('applications/{application}/panel', [AssessorController::class, 'panelForm'])->name('panel.show');
    Route::post('applications/{application}/panel', [AssessorController::class, 'assignPanel'])->name('panel.assign');

    // Phase 4: Curriculum
    Route::get('curriculum', [CurriculumController::class, 'index'])->name('curriculum.index');
    Route::get('curriculum/create', [CurriculumController::class, 'create'])->name('curriculum.create');
    Route::post('curriculum', [CurriculumController::class, 'store'])->name('curriculum.store');
    Route::get('curriculum/{subject}/edit', [CurriculumController::class, 'edit'])->name('curriculum.edit');
    Route::put('curriculum/{subject}', [CurriculumController::class, 'update'])->name('curriculum.update');
    Route::post('curriculum/version', [CurriculumController::class, 'newVersion'])->name('curriculum.version');

    // Phase 4: Adviser Assignments
    Route::get('adviser-assignments', [AdviserAssignmentController::class, 'index'])->name('adviser-assignments.index');
    Route::get('adviser-assignments/create', [AdviserAssignmentController::class, 'create'])->name('adviser-assignments.create');
    Route::post('adviser-assignments', [AdviserAssignmentController::class, 'store'])->name('adviser-assignments.store');
    Route::get('adviser-workload', [AdviserAssignmentController::class, 'workload'])->name('adviser-workload');

    // Phase 5: Grade Approval
    Route::get('grades/pending', [GradeController::class, 'pending'])->name('grades.pending');
    Route::put('grades/{grade}/approve', [GradeController::class, 'approve'])->name('grades.approve');
});
