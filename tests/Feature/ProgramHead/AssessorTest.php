<?php

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\User;

function createApplicationForPanel(): Application
{
    $applicant = User::factory()->create();

    return $applicant->applications()->create([
        'application_number' => 'APP-2026-0002',
        'status' => ApplicationStatus::UnderReview,
    ]);
}

test('program head can view available assessors', function () {
    $programHead = User::factory()->withRole('program_head')->create();
    User::factory()->withRole('assessor_internal')->create();

    $response = $this->actingAs($programHead)->get(route('program-head.assessors.available'));

    $response->assertOk();
});

test('program head can assign a valid assessment panel', function () {
    $programHead = User::factory()->withRole('program_head')->create();
    $internal = User::factory()->withRole('assessor_internal')->create();
    $external = User::factory()->withRole('assessor_external')->create();
    $another = User::factory()->withRole('assessor_internal')->create();
    $application = createApplicationForPanel();

    $response = $this->actingAs($programHead)->post(route('program-head.panel.assign', $application), [
        'panelist_ids' => [$internal->id, $external->id, $another->id],
    ]);

    $response->assertRedirect();
    expect($application->fresh()->interviews()->first()->panelists()->count())->toBe(3);
});

test('program head cannot assign a panel with fewer than the minimum assessors', function () {
    $programHead = User::factory()->withRole('program_head')->create();
    $internal = User::factory()->withRole('assessor_internal')->create();
    $external = User::factory()->withRole('assessor_external')->create();
    $application = createApplicationForPanel();

    $response = $this->actingAs($programHead)->post(route('program-head.panel.assign', $application), [
        'panelist_ids' => [$internal->id, $external->id],
    ]);

    $response->assertSessionHasErrors('panelist_ids');
});

test('program head cannot assign a panel without both an internal and external assessor', function () {
    $programHead = User::factory()->withRole('program_head')->create();
    $internalOne = User::factory()->withRole('assessor_internal')->create();
    $internalTwo = User::factory()->withRole('assessor_internal')->create();
    $internalThree = User::factory()->withRole('assessor_internal')->create();
    $application = createApplicationForPanel();

    $response = $this->actingAs($programHead)->post(route('program-head.panel.assign', $application), [
        'panelist_ids' => [$internalOne->id, $internalTwo->id, $internalThree->id],
    ]);

    $response->assertSessionHasErrors('panelist_ids');
});
