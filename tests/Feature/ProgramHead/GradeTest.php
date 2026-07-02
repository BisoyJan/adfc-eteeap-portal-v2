<?php

use App\Enums\EnrollmentSubjectStatus;
use App\Enums\GradeStatus;
use App\Models\CurriculumSubject;
use App\Models\Enrollment;
use App\Models\EnrollmentSubject;
use App\Models\Grade;
use App\Models\User;

function createSubmittedGrade(): Grade
{
    $adviser = User::factory()->withRole('adviser')->create();
    $student = User::factory()->withRole('student')->create();
    $subject = CurriculumSubject::factory()->create();

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'enrollment_date' => now(),
        'student_id_number' => 'ETEEAP-2026-0002',
        'status' => 'enrolled',
    ]);

    EnrollmentSubject::create([
        'enrollment_id' => $enrollment->id,
        'curriculum_subject_id' => $subject->id,
        'status' => EnrollmentSubjectStatus::Enrolled,
    ]);

    return Grade::create([
        'student_id' => $student->id,
        'curriculum_subject_id' => $subject->id,
        'enrollment_id' => $enrollment->id,
        'adviser_id' => $adviser->id,
        'grade' => '1.75',
        'status' => GradeStatus::Submitted,
    ]);
}

test('program head can view the pending grade approvals queue', function () {
    $programHead = User::factory()->withRole('program_head')->create();
    createSubmittedGrade();

    $response = $this->actingAs($programHead)->get(route('program-head.grades.pending'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('program-head/grades/index'));
});

test('program head can approve a submitted grade', function () {
    $programHead = User::factory()->withRole('program_head')->create();
    $grade = createSubmittedGrade();

    $response = $this->actingAs($programHead)->put(route('program-head.grades.approve', $grade));

    $response->assertRedirect();
    expect($grade->fresh()->status->value)->toBe('approved');
    expect($grade->fresh()->approved_by)->toBe($programHead->id);
});

test('approving a grade flips the enrollment subject to completed', function () {
    $programHead = User::factory()->withRole('program_head')->create();
    $grade = createSubmittedGrade();

    $this->actingAs($programHead)->put(route('program-head.grades.approve', $grade));

    $enrollmentSubject = EnrollmentSubject::where('enrollment_id', $grade->enrollment_id)
        ->where('curriculum_subject_id', $grade->curriculum_subject_id)
        ->first();

    expect($enrollmentSubject->status->value)->toBe('completed');
});

test('program head cannot approve a draft grade', function () {
    $programHead = User::factory()->withRole('program_head')->create();
    $student = User::factory()->withRole('student')->create();
    $subject = CurriculumSubject::factory()->create();
    $adviser = User::factory()->withRole('adviser')->create();

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'enrollment_date' => now(),
        'student_id_number' => 'ETEEAP-2026-0003',
        'status' => 'enrolled',
    ]);

    $grade = Grade::create([
        'student_id' => $student->id,
        'curriculum_subject_id' => $subject->id,
        'enrollment_id' => $enrollment->id,
        'adviser_id' => $adviser->id,
        'grade' => '1.75',
        'status' => GradeStatus::Draft,
    ]);

    $response = $this->actingAs($programHead)->put(route('program-head.grades.approve', $grade));

    $response->assertSessionHasErrors('grade');
});

test('non-program-head cannot access grade approval routes', function () {
    $student = User::factory()->create();
    $grade = createSubmittedGrade();

    $response = $this->actingAs($student)->get(route('program-head.grades.pending'));
    $response->assertForbidden();

    $response = $this->actingAs($student)->put(route('program-head.grades.approve', $grade));
    $response->assertForbidden();
});
