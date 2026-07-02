<?php

use App\Enums\ApplicationStatus;
use App\Enums\InterviewStatus;
use App\Models\Application;
use App\Models\User;

function createApplicationWithCompletedInterview(): Application
{
    $applicant = User::factory()->create();
    $application = $applicant->applications()->create([
        'application_number' => 'APP-2026-0006',
        'status' => ApplicationStatus::UnderReview,
    ]);

    $application->interviews()->create([
        'created_by' => User::factory()->withRole('program_head')->create()->id,
        'status' => InterviewStatus::Completed,
    ]);

    return $application;
}

test('qa officer can view applications pending final matrix', function () {
    $qaOfficer = User::factory()->withRole('qa_officer')->create();
    createApplicationWithCompletedInterview();

    $response = $this->actingAs($qaOfficer)->get(route('qa-officer.validations.pending'));

    $response->assertOk();
});

test('qa officer can save and approve a final equivalency matrix', function () {
    $qaOfficer = User::factory()->withRole('qa_officer')->create();
    $application = createApplicationWithCompletedInterview();

    $saveResponse = $this->actingAs($qaOfficer)->put(route('qa-officer.equivalency.update', $application), [
        'items' => [
            [
                'subject_code' => 'CC102',
                'subject_title' => 'Computer Programming 1',
                'units' => 3,
                'competency_level' => 'partial',
                'equivalent_credits' => 1.5,
                'recommended_action' => 'enhance',
            ],
        ],
    ]);

    $saveResponse->assertRedirect();

    $approveResponse = $this->actingAs($qaOfficer)->post(route('qa-officer.equivalency.approve', $application));

    $approveResponse->assertRedirect(route('qa-officer.equivalency.show', $application));

    $matrix = $application->equivalencyMatrices()->where('type', 'final')->first();
    expect($matrix->status->value)->toBe('approved');
    expect($matrix->approved_by)->toBe($qaOfficer->id);
});
