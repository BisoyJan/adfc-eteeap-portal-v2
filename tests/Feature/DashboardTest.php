<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated applicants are redirected to the applicant dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('applicant.dashboard'));

    $this->get(route('applicant.dashboard'))->assertOk();
});

test('authenticated coordinators are redirected to the coordinator dashboard', function () {
    $user = User::factory()->withRole('coordinator')->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('coordinator.dashboard'));

    $this->get(route('coordinator.dashboard'))->assertOk();
});

test('authenticated admins are redirected to the admin dashboard', function () {
    $user = User::factory()->withRole('admin')->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('admin.dashboard'));

    $this->get(route('admin.dashboard'))->assertOk();
});

test('authenticated assessors are redirected to the assessor dashboard', function () {
    $user = User::factory()->withRole('assessor_internal')->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('assessor.dashboard'));

    $this->get(route('assessor.dashboard'))->assertOk();
});

test('users cannot access a dashboard outside of their role', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('coordinator.dashboard'))->assertForbidden();
});
