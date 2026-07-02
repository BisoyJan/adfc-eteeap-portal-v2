<?php

use App\Models\User;

test('assessor can view their dashboard', function () {
    $assessor = User::factory()->withRole('assessor_internal')->create();

    $response = $this->actingAs($assessor)->get(route('assessor.dashboard'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('assessor/dashboard'));
});

test('non-assessor cannot access assessor dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('assessor.dashboard'));

    $response->assertForbidden();
});
