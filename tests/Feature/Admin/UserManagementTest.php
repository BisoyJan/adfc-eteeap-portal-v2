<?php

use App\Models\User;

test('admin can list users', function () {
    $admin = User::factory()->withRole('admin')->create();

    User::factory()->count(3)->create();

    $response = $this->actingAs($admin)->get(route('admin.users.index'));

    expect($response->status())->not->toBe(403);
});

test('admin can create a staff user', function () {
    $admin = User::factory()->withRole('admin')->create();

    $response = $this->actingAs($admin)->post(route('admin.users.store'), [
        'name' => 'Staff User',
        'email' => 'staff@adfc.edu.ph',
        'password' => 'secret123',
        'roles' => ['coordinator'],
    ]);

    $response->assertRedirect();

    expect(User::where('email', 'staff@adfc.edu.ph')->exists())->toBeTrue();
});

test('non-admin cannot create users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('admin.users.store'), [
        'name' => 'Staff User',
        'email' => 'staff@adfc.edu.ph',
        'password' => 'secret123',
        'roles' => ['coordinator'],
    ]);

    $response->assertForbidden();
});

test('admin can suspend a user', function () {
    $admin = User::factory()->withRole('admin')->create();
    $target = User::factory()->create();

    $response = $this->actingAs($admin)->put(route('admin.users.suspend', $target), [
        'reason' => 'Violated terms of service',
    ]);

    $response->assertRedirect();

    expect((bool) $target->fresh()->is_active)->toBeFalse();
});

test('admin can unsuspend a user', function () {
    $admin = User::factory()->withRole('admin')->create();
    $target = User::factory()->create([
        'is_active' => false,
    ]);

    $response = $this->actingAs($admin)->put(route('admin.users.unsuspend', $target));

    $response->assertRedirect();

    expect((bool) $target->fresh()->is_active)->toBeTrue();
});

test('admin can update user roles', function () {
    $admin = User::factory()->withRole('admin')->create();
    $target = User::factory()->create();

    $response = $this->actingAs($admin)->put(route('admin.users.update-roles', $target), [
        'roles' => ['coordinator', 'program_head'],
    ]);

    $response->assertRedirect();

    expect($target->fresh()->roles->pluck('name')->toArray())->toBe(['coordinator', 'program_head']);
});
