<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\EquivalencyRuleController;
use App\Http\Controllers\Admin\SystemController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentDownloadController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TemplateDownloadController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::get('curriculum/subjects', [CurriculumController::class, 'index'])->name('curriculum.subjects');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::put('notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::put('notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');

    Route::get('documents/{document}/download', [DocumentDownloadController::class, 'download'])->name('documents.download');
    Route::get('documents/{document}/preview', [DocumentDownloadController::class, 'preview'])->name('documents.preview');
    Route::get('templates/{type}/download', [TemplateDownloadController::class, 'download'])->name('templates.download');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::inertia('dashboard', 'admin/dashboard')->name('dashboard');

        Route::get('equivalency-rules', [EquivalencyRuleController::class, 'index'])->name('equivalency-rules.index');
        Route::post('equivalency-rules', [EquivalencyRuleController::class, 'store'])->name('equivalency-rules.store');

        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::put('users/{user}/roles', [UserController::class, 'updateRoles'])->name('users.update-roles');
        Route::put('users/{user}/suspend', [UserController::class, 'suspend'])->name('users.suspend');
        Route::put('users/{user}/unsuspend', [UserController::class, 'unsuspend'])->name('users.unsuspend');

        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');

        Route::get('system', [SystemController::class, 'health'])->name('system.health');
        Route::put('system/config', [SystemController::class, 'updateConfig'])->name('system.update-config');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/applicant.php';
require __DIR__.'/coordinator.php';
require __DIR__.'/assessor.php';
require __DIR__.'/program-head.php';
require __DIR__.'/qa-officer.php';
require __DIR__.'/adviser.php';
require __DIR__.'/student.php';
require __DIR__.'/vpaa.php';
