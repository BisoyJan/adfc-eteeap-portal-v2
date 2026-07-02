<?php

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\User;

test('coordinator can view reports index', function () {
    $coordinator = User::factory()->withRole('coordinator')->create();

    $response = $this->actingAs($coordinator)->get(route('coordinator.reports.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('coordinator/reports/index'));
});

test('coordinator can download a PDF report', function () {
    $coordinator = User::factory()->withRole('coordinator')->create();
    $applicant = User::factory()->create();
    $applicant->applications()->create([
        'application_number' => 'APP-RPT-001',
        'status' => ApplicationStatus::Submitted,
        'submitted_at' => now(),
    ]);

    $response = $this->actingAs($coordinator)->get(route('coordinator.reports.show', 'pipeline'));

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toContain('application/pdf');
});

test('coordinator can download an Excel report', function () {
    $coordinator = User::factory()->withRole('coordinator')->create();
    $applicant = User::factory()->create();
    $applicant->applications()->create([
        'application_number' => 'APP-RPT-002',
        'status' => ApplicationStatus::Submitted,
        'submitted_at' => now(),
    ]);

    $response = $this->actingAs($coordinator)->get(
        route('coordinator.reports.show', ['type' => 'pipeline', 'format' => 'excel'])
    );

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toContain('vnd.openxmlformats');
});

test('coordinator gets 404 for invalid report type', function () {
    $coordinator = User::factory()->withRole('coordinator')->create();

    $response = $this->actingAs($coordinator)->get(route('coordinator.reports.show', 'invalid_type'));

    $response->assertNotFound();
});

test('non-coordinator cannot access coordinator reports', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('coordinator.reports.index'));

    $response->assertForbidden();
});
