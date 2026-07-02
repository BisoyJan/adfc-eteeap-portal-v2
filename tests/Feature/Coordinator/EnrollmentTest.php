<?php

use App\Enums\ApplicationStatus;
use App\Enums\EquivalencyMatrixStatus;
use App\Enums\EquivalencyMatrixType;
use App\Enums\RecommendedAction;
use App\Models\Application;
use App\Models\CurriculumSubject;
use App\Models\EquivalencyMatrix;
use App\Models\User;

function createEligibleApplication(): Application
{
    $applicant = User::factory()->create();
    $application = $applicant->applications()->create([
        'application_number' => 'APP-2026-0100',
        'status' => ApplicationStatus::Approved,
    ]);

    $matrix = EquivalencyMatrix::create([
        'application_id' => $application->id,
        'type' => EquivalencyMatrixType::Final,
        'status' => EquivalencyMatrixStatus::Approved,
        'prepared_by' => User::factory()->withRole('program_head')->create()->id,
        'approved_by' => User::factory()->withRole('qa_officer')->create()->id,
        'approved_at' => now(),
    ]);

    $subject = CurriculumSubject::factory()->create();

    $matrix->items()->create([
        'curriculum_subject_id' => $subject->id,
        'subject_code' => $subject->code,
        'subject_title' => $subject->title,
        'units' => $subject->units,
        'competency_level' => 'full',
        'equivalent_credits' => $subject->units,
        'recommended_action' => RecommendedAction::Credit,
    ]);

    return $application;
}

test('coordinator can view eligible applications for enrollment', function () {
    $coordinator = User::factory()->withRole('coordinator')->create();
    createEligibleApplication();

    $response = $this->actingAs($coordinator)->get(route('coordinator.enrollments.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('coordinator/enrollments/index'));
});

test('coordinator can create an enrollment from an eligible application', function () {
    $coordinator = User::factory()->withRole('coordinator')->create();
    $application = createEligibleApplication();

    $response = $this->actingAs($coordinator)->post(route('coordinator.enrollments.store'), [
        'application_id' => $application->id,
        'academic_term' => 'AY 2026-2027 1st Sem',
    ]);

    $response->assertRedirect();

    $enrollment = $application->fresh()->enrollment;
    expect($enrollment)->not->toBeNull();
    expect($enrollment->student_id)->toBe($application->user_id);
    expect($enrollment->academic_term)->toBe('AY 2026-2027 1st Sem');
    expect($enrollment->student_id_number)->toMatch('/^ETEEAP-2026-/');
    expect($enrollment->status->value)->toBe('enrolled');
});

test('enrollment attaches the student role to the user', function () {
    $coordinator = User::factory()->withRole('coordinator')->create();
    $application = createEligibleApplication();
    $applicant = $application->user;

    expect($applicant->hasRole('student'))->toBeFalse();

    $this->actingAs($coordinator)->post(route('coordinator.enrollments.store'), [
        'application_id' => $application->id,
    ]);

    expect($applicant->fresh()->hasRole('student'))->toBeTrue();
});

test('enrollment auto-encodes subjects from the matrix', function () {
    $coordinator = User::factory()->withRole('coordinator')->create();
    $applicant = User::factory()->create();
    $application = $applicant->applications()->create([
        'application_number' => 'APP-2026-0101',
        'status' => ApplicationStatus::Approved,
    ]);

    $matrix = EquivalencyMatrix::create([
        'application_id' => $application->id,
        'type' => EquivalencyMatrixType::Final,
        'status' => EquivalencyMatrixStatus::Approved,
        'prepared_by' => User::factory()->withRole('program_head')->create()->id,
        'approved_by' => User::factory()->withRole('qa_officer')->create()->id,
        'approved_at' => now(),
    ]);

    $creditSubject = CurriculumSubject::factory()->create(['code' => 'CS-300']);
    $enrollSubject = CurriculumSubject::factory()->create(['code' => 'CS-301']);

    $matrix->items()->create([
        'curriculum_subject_id' => $creditSubject->id,
        'subject_code' => $creditSubject->code,
        'subject_title' => $creditSubject->title,
        'units' => $creditSubject->units,
        'competency_level' => 'full',
        'equivalent_credits' => $creditSubject->units,
        'recommended_action' => RecommendedAction::Credit,
    ]);

    $matrix->items()->create([
        'curriculum_subject_id' => $enrollSubject->id,
        'subject_code' => $enrollSubject->code,
        'subject_title' => $enrollSubject->title,
        'units' => $enrollSubject->units,
        'competency_level' => 'partial',
        'equivalent_credits' => 1,
        'recommended_action' => RecommendedAction::Enroll,
    ]);

    $this->actingAs($coordinator)->post(route('coordinator.enrollments.store'), [
        'application_id' => $application->id,
    ]);

    $enrollment = $application->fresh()->enrollment;
    expect($enrollment->subjects()->where('status', 'credited')->count())->toBe(1);
    expect($enrollment->subjects()->where('status', 'enrolled')->count())->toBe(1);
});

test('non-coordinator cannot access enrollment routes', function () {
    $applicant = User::factory()->create();

    $response = $this->actingAs($applicant)->get(route('coordinator.enrollments.index'));
    $response->assertForbidden();

    $response = $this->actingAs($applicant)->post(route('coordinator.enrollments.store'), [
        'application_id' => 1,
    ]);
    $response->assertForbidden();
});
