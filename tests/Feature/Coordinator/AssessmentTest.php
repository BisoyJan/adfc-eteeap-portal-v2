<?php

use App\Enums\ApplicationStatus;
use App\Enums\AssessmentStage;
use App\Models\Application;
use App\Models\User;

function createSubmittedApplicationForAssessment(): Application
{
    $applicant = User::factory()->create();

    return $applicant->applications()->create([
        'application_number' => 'APP-2026-0001',
        'status' => ApplicationStatus::Submitted,
        'submitted_at' => now(),
    ]);
}

test('coordinator can submit the F.1 initial screening form', function () {
    $coordinator = User::factory()->withRole('coordinator')->create();
    $application = createSubmittedApplicationForAssessment();

    $response = $this->actingAs($coordinator)->post(route('coordinator.assessments.first', $application), [
        'criteria' => [
            ['name' => 'Age and citizenship', 'score' => 10, 'max_score' => 10],
            ['name' => 'Work experience', 'score' => 18, 'max_score' => 20],
        ],
        'verdict' => 'pass',
    ]);

    $response->assertRedirect();

    $form = $application->assessmentForms()->where('stage', AssessmentStage::First)->first();
    expect($form)->not->toBeNull();
    expect((float) $form->total_score)->toBe(28.0);
    expect($form->verdict->value)->toBe('pass');
    expect($form->digital_signature)->toContain($coordinator->name);
});

test('coordinator can submit the F.2 portfolio evaluation form', function () {
    $coordinator = User::factory()->withRole('coordinator')->create();
    $application = createSubmittedApplicationForAssessment();

    $response = $this->actingAs($coordinator)->post(route('coordinator.assessments.second', $application), [
        'criteria' => [
            ['name' => 'Portfolio completeness', 'score' => 40, 'max_score' => 50],
        ],
        'verdict' => 'conditional',
    ]);

    $response->assertRedirect();
    expect($application->assessmentForms()->where('stage', AssessmentStage::Second)->exists())->toBeTrue();
});

test('non-coordinators cannot submit assessment forms', function () {
    $applicant = User::factory()->create();
    $application = createSubmittedApplicationForAssessment();

    $response = $this->actingAs($applicant)->post(route('coordinator.assessments.first', $application), [
        'criteria' => [['name' => 'x', 'score' => 1, 'max_score' => 1]],
        'verdict' => 'pass',
    ]);

    $response->assertForbidden();
});
