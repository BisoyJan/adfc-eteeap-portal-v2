<?php

use App\Models\User;

test('applicant can view their dashboard', function () {
    $applicant = User::factory()->withRole('applicant')->create();

    $response = $this->actingAs($applicant)->get(route('applicant.dashboard'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('applicant/dashboard'));
});

test('non-applicant cannot access applicant dashboard', function () {
    $user = User::factory()->withRole('student')->create();

    $response = $this->actingAs($user)->get(route('applicant.dashboard'));

    $response->assertForbidden();
});
