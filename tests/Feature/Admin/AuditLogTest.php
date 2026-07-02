<?php

use App\Models\AuditLog;
use App\Models\User;

test('admin can view audit logs', function () {
    $admin = User::factory()->withRole('admin')->create();

    AuditLog::create([
        'user_id' => $admin->id,
        'action' => 'test_action',
        'target_type' => 'test',
        'created_at' => now(),
    ]);

    $response = $this->actingAs($admin)->get(route('admin.audit-logs.index'));

    expect($response->status())->not->toBe(403);
});

test('admin can view audit log detail', function () {
    $admin = User::factory()->withRole('admin')->create();

    $log = AuditLog::create([
        'user_id' => $admin->id,
        'action' => 'test_action',
        'target_type' => 'test',
        'target_id' => 1,
        'old_values' => ['field' => 'old'],
        'new_values' => ['field' => 'new'],
        'ip_address' => '127.0.0.1',
        'user_agent' => 'Test/1.0',
        'created_at' => now(),
    ]);

    $response = $this->actingAs($admin)->get(route('admin.audit-logs.show', $log));

    expect($response->status())->not->toBe(403);
});

test('non-admin cannot view audit logs', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.audit-logs.index'));

    $response->assertForbidden();
});
