<?php

use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditService;

test('audit service creates a log entry', function () {
    $user = User::factory()->create();

    $log = app(AuditService::class)->log(
        actor: $user,
        action: 'test_action',
        targetType: 'user',
        targetId: $user->id,
        old: ['name' => 'Old Name'],
        new: ['name' => 'New Name'],
    );

    expect($log)->toBeInstanceOf(AuditLog::class);
    expect($log->user_id)->toBe($user->id);
    expect($log->action)->toBe('test_action');
    expect($log->target_type)->toBe('user');
    expect($log->target_id)->toBe($user->id);
    expect($log->old_values)->toBe(['name' => 'Old Name']);
    expect($log->new_values)->toBe(['name' => 'New Name']);

    expect(AuditLog::count())->toBe(1);
});

test('audit log stores ip and user agent', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $log = app(AuditService::class)->log(
        actor: $user,
        action: 'login',
        targetType: 'session',
        targetId: $user->id,
    );

    expect($log->ip_address)->toBe('127.0.0.1');
    expect($log->user_agent)->not->toBeNull();
});
