<?php

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\User;

function makeSubmittedApplication(): Application
{
    $applicant = User::factory()->create();

    return $applicant->applications()->create([
        'application_number' => 'APP-2026-0001',
        'status' => ApplicationStatus::Submitted,
        'submitted_at' => now(),
    ]);
}

test('coordinator can view the application kanban board', function () {
    $coordinator = User::factory()->withRole('coordinator')->create();

    $response = $this->actingAs($coordinator)->get(route('coordinator.applications.index'));

    $response->assertOk();
});

test('coordinator can view an application detail page', function () {
    $coordinator = User::factory()->withRole('coordinator')->create();
    $application = makeSubmittedApplication();

    $response = $this->actingAs($coordinator)->get(route('coordinator.applications.show', $application));

    $response->assertOk();
});

test('coordinator can approve an application', function () {
    $coordinator = User::factory()->withRole('coordinator')->create();
    $application = makeSubmittedApplication();

    $response = $this->actingAs($coordinator)->put(route('coordinator.applications.review', $application), [
        'decision' => 'approve',
    ]);

    $response->assertRedirect(route('coordinator.applications.show', $application));
    expect($application->fresh()->status)->toBe(ApplicationStatus::Approved);
});

test('coordinator can reject an application with a reason', function () {
    $coordinator = User::factory()->withRole('coordinator')->create();
    $application = makeSubmittedApplication();

    $response = $this->actingAs($coordinator)->put(route('coordinator.applications.review', $application), [
        'decision' => 'reject',
        'rejection_reason' => 'Does not meet the minimum experience requirement.',
    ]);

    $response->assertRedirect(route('coordinator.applications.show', $application));
    expect($application->fresh()->status)->toBe(ApplicationStatus::Rejected);
});

test('coordinator can request revisions on an application', function () {
    $coordinator = User::factory()->withRole('coordinator')->create();
    $application = makeSubmittedApplication();

    $response = $this->actingAs($coordinator)->put(route('coordinator.applications.review', $application), [
        'decision' => 'request_revision',
        'notes' => 'Please upload a clearer copy of your TOR.',
    ]);

    $response->assertRedirect(route('coordinator.applications.show', $application));
    expect($application->fresh()->status)->toBe(ApplicationStatus::RevisionRequested);
});

test('applicants cannot access coordinator application routes', function () {
    $applicant = User::factory()->create();

    $response = $this->actingAs($applicant)->get(route('coordinator.applications.index'));

    $response->assertForbidden();
});
