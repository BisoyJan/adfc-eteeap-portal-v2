<?php

use App\Enums\EnrollmentSubjectStatus;
use App\Models\CurriculumSubject;
use App\Models\Enrollment;
use App\Models\EnrollmentSubject;
use App\Models\GraduationClearance;
use App\Models\User;

$inReviewCounter = 0;

function createInReviewClearance(): GraduationClearance
{
    global $inReviewCounter;
    $inReviewCounter++;

    $coordinator = User::factory()->withRole('coordinator')->create();

    $student = User::factory()->withRole('student')->create();

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'enrollment_date' => now(),
        'student_id_number' => sprintf('ETEEAP-2026-%04d', 200 + $inReviewCounter),
        'status' => 'enrolled',
    ]);

    $subject = CurriculumSubject::factory()->create(['is_active' => true]);

    EnrollmentSubject::create([
        'enrollment_id' => $enrollment->id,
        'curriculum_subject_id' => $subject->id,
        'status' => EnrollmentSubjectStatus::Completed,
    ]);

    $clearance = GraduationClearance::create([
        'student_id' => $student->id,
        'status' => 'in_review',
    ]);

    $docTypes = [
        'endorsement_letter', 'rubric_final_rating', 'summary_requirements',
        'enrollment_form', 'equivalency_matrix_compliance', 'panel_evaluation',
        'application_letter', 'ched_form', 'cv', 'psa_documents', 'coe', 'narrative_reports',
    ];

    foreach ($docTypes as $type) {
        $clearance->documents()->create([
            'document_type' => $type,
            'is_verified' => true,
            'verified_by' => $coordinator->id,
            'verified_at' => now(),
        ]);
    }

    return $clearance;
}

test('vpaa can view pending approvals', function () {
    $vpaa = User::factory()->withRole('vpaa')->create();
    createInReviewClearance();

    $response = $this->actingAs($vpaa)->get(route('vpaa.approvals.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('vpaa/approvals/index'));
});

test('vpaa can approve an in_review clearance', function () {
    $vpaa = User::factory()->withRole('vpaa')->create();
    $clearance = createInReviewClearance();

    $response = $this->actingAs($vpaa)->put(
        route('vpaa.approvals.approve', $clearance->id),
    );

    $response->assertRedirect();

    $clearance->refresh();
    expect($clearance->status->value)->toBe('approved');
    expect($clearance->registry_number)->toMatch('/^RGN-2026-/');
    expect($clearance->vpaa_approved_at)->not->toBeNull();
    expect($clearance->diploma_printed_at)->not->toBeNull();
});

test('approval of non-in-review clearance is blocked', function () {
    $vpaa = User::factory()->withRole('vpaa')->create();

    $student = User::factory()->withRole('student')->create();
    $clearance = GraduationClearance::create([
        'student_id' => $student->id,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($vpaa)->put(
        route('vpaa.approvals.approve', $clearance->id),
    );

    $response->assertSessionHasErrors('clearance');
});

test('registry number format is correct on approval', function () {
    $vpaa = User::factory()->withRole('vpaa')->create();
    $clearance = createInReviewClearance();

    $this->actingAs($vpaa)->put(route('vpaa.approvals.approve', $clearance->id));

    $clearance->refresh();
    expect($clearance->registry_number)->toMatch('/^RGN-\d{4}-\d{4}$/');
});

test('registry number is unique', function () {
    $vpaa = User::factory()->withRole('vpaa')->create();
    $clearance1 = createInReviewClearance();
    $clearance2 = createInReviewClearance();

    $this->actingAs($vpaa)->put(route('vpaa.approvals.approve', $clearance1->id));
    $this->actingAs($vpaa)->put(route('vpaa.approvals.approve', $clearance2->id));

    expect($clearance1->fresh()->registry_number)->not->toBe($clearance2->fresh()->registry_number);
});

test('vpaa can view graduation audit', function () {
    $vpaa = User::factory()->withRole('vpaa')->create();
    createInReviewClearance();

    $response = $this->actingAs($vpaa)->get(route('vpaa.graduation-audit'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('vpaa/graduation-audit/index'));
});

test('non-vpaa cannot access vpaa routes', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('vpaa.approvals.index'));
    $response->assertForbidden();
});
