<?php

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\Interview;
use App\Models\User;

test('coordinator can view their dashboard', function () {
    $coordinator = User::factory()->withRole('coordinator')->create();

    $response = $this->actingAs($coordinator)->get(route('coordinator.dashboard'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('coordinator/dashboard'));
});

test('coordinator dashboard shows pipeline data', function () {
    $coordinator = User::factory()->withRole('coordinator')->create();
    $applicant = User::factory()->create();
    $applicant->applications()->create([
        'application_number' => 'APP-PLN-001',
        'status' => ApplicationStatus::Submitted,
        'submitted_at' => now(),
    ]);
    $applicant->applications()->create([
        'application_number' => 'APP-PLN-002',
        'status' => ApplicationStatus::Approved,
        'submitted_at' => now(),
    ]);

    $response = $this->actingAs($coordinator)->get(route('coordinator.dashboard'));

    $response->assertInertia(fn ($page) => $page
        ->has('pipeline')
        ->has('totalApplicants')
        ->has('totalEnrolled')
    );
});

test('coordinator dashboard shows upcoming interviews', function () {
    $coordinator = User::factory()->withRole('coordinator')->create();
    $applicant = User::factory()->create();
    $application = $applicant->applications()->create([
        'application_number' => 'APP-INT-001',
        'status' => ApplicationStatus::UnderReview,
    ]);
    Interview::create([
        'application_id' => $application->id,
        'created_by' => $coordinator->id,
        'status' => 'scheduled',
        'scheduled_at' => now()->addDays(3),
        'location' => 'Room A',
    ]);

    $response = $this->actingAs($coordinator)->get(route('coordinator.dashboard'));

    $response->assertInertia(fn ($page) => $page
        ->has('upcomingInterviews', 1)
        ->where('upcomingInterviews.0.location', 'Room A')
    );
});

test('non-coordinator cannot access coordinator dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('coordinator.dashboard'));

    $response->assertForbidden();
});
