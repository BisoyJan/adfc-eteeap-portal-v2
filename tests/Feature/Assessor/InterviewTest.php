<?php

use App\Enums\ApplicationStatus;
use App\Enums\AssessmentStage;
use App\Models\User;
use App\Services\InterviewService;

function createInterviewWithPanel(): array
{
    $applicant = User::factory()->create();
    $application = $applicant->applications()->create([
        'application_number' => 'APP-2026-0005',
        'status' => ApplicationStatus::UnderReview,
    ]);

    $programHead = User::factory()->withRole('program_head')->create();
    $internal = User::factory()->withRole('assessor_internal')->create();
    $external = User::factory()->withRole('assessor_external')->create();
    $another = User::factory()->withRole('assessor_internal')->create();

    $interview = app(InterviewService::class)->assignPanel($application, $programHead, [
        $internal->id, $external->id, $another->id,
    ]);

    return [$interview, $internal, $external];
}

test('assessor can view their assigned interviews', function () {
    [$interview, $internal] = createInterviewWithPanel();

    $response = $this->actingAs($internal)->get(route('assessor.interviews.index'));

    $response->assertOk();
});

test('assessor can view an assigned interview detail', function () {
    [$interview, $internal] = createInterviewWithPanel();

    $response = $this->actingAs($internal)->get(route('assessor.interviews.show', $interview));

    $response->assertOk();
});

test('assessor cannot view an interview they are not assigned to', function () {
    [$interview] = createInterviewWithPanel();
    $outsider = User::factory()->withRole('assessor_internal')->create();

    $response = $this->actingAs($outsider)->get(route('assessor.interviews.show', $interview));

    $response->assertForbidden();
});

test('assessor can submit the validation tool for their interview', function () {
    [$interview, $internal] = createInterviewWithPanel();

    $response = $this->actingAs($internal)->post(route('assessor.interviews.validate', $interview), [
        'criteria' => [
            ['name' => 'Employment history relevance', 'score' => 9, 'max_score' => 10],
        ],
        'verdict' => 'pass',
    ]);

    $response->assertRedirect();
    expect($interview->application->assessmentForms()->where('stage', AssessmentStage::ThirdValidation)->where('assessor_id', $internal->id)->exists())->toBeTrue();
});

test('assessor can submit the oral competency form for their interview', function () {
    [$interview, , $external] = createInterviewWithPanel();

    $response = $this->actingAs($external)->post(route('assessor.interviews.oral', $interview), [
        'criteria' => [
            ['name' => 'Technical proficiency', 'score' => 8, 'max_score' => 10],
        ],
        'verdict' => 'pass',
    ]);

    $response->assertRedirect();
    expect($interview->application->assessmentForms()->where('stage', AssessmentStage::ThirdOral)->where('assessor_id', $external->id)->exists())->toBeTrue();
});
