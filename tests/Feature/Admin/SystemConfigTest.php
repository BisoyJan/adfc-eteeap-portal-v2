<?php

use App\Models\User;

test('admin can view system health page', function () {
    $admin = User::factory()->withRole('admin')->create();

    $response = $this->actingAs($admin)->get(route('admin.system.health'));

    expect($response->status())->not->toBe(403);
});

test('admin can update config', function () {
    $admin = User::factory()->withRole('admin')->create();

    $response = $this->actingAs($admin)->put(route('admin.system.update-config'), [
        'min_age' => 20,
        'min_experience_years' => 3,
    ]);

    $response->assertRedirect();

    expect(\App\Models\Setting::get('min_age'))->toBe('20');
    expect(\App\Models\Setting::get('min_experience_years'))->toBe('3');
});

test('non-admin cannot update config', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->put(route('admin.system.update-config'), [
        'min_age' => 20,
    ]);

    $response->assertForbidden();
});
