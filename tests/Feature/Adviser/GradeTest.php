<?php

use App\Enums\EnrollmentSubjectStatus;
use App\Models\CurriculumSubject;
use App\Models\Enrollment;
use App\Models\EnrollmentSubject;
use App\Models\Grade;
use App\Models\Rubric;
use App\Models\User;

function createEnrolledStudent(User $adviser): User
{
    $student = User::factory()->withRole('student')->create();
    $subject = CurriculumSubject::factory()->create();

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'enrollment_date' => now(),
        'student_id_number' => 'ETEEAP-2026-0001',
        'status' => 'enrolled',
    ]);

    EnrollmentSubject::create([
        'enrollment_id' => $enrollment->id,
        'curriculum_subject_id' => $subject->id,
        'status' => EnrollmentSubjectStatus::Enrolled,
    ]);

    $student->studentAssignments()->create([
        'adviser_id' => $adviser->id,
        'assigned_by' => User::factory()->withRole('program_head')->create()->id,
        'assigned_at' => now(),
        'is_active' => true,
    ]);

    return $student;
}

test('adviser can view their grades list', function () {
    $adviser = User::factory()->withRole('adviser')->create();

    $response = $this->actingAs($adviser)->get(route('adviser.grades.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('adviser/grades/index'));
});

test('adviser can view the grade entry form', function () {
    $adviser = User::factory()->withRole('adviser')->create();
    $student = createEnrolledStudent($adviser);

    $response = $this->actingAs($adviser)->get(route('adviser.grades.create'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('adviser/grades/create'));
});

test('adviser can store a draft grade', function () {
    $adviser = User::factory()->withRole('adviser')->create();
    $student = createEnrolledStudent($adviser);
    $enrollmentSubject = $student->enrollments()->first()->subjects()->first();

    $response = $this->actingAs($adviser)->post(route('adviser.grades.store'), [
        'enrollment_subject_id' => $enrollmentSubject->id,
        'grade' => '1.75',
        'grade_point' => 1.75,
    ]);

    $response->assertRedirect(route('adviser.grades.index'));

    $grade = Grade::where('adviser_id', $adviser->id)->first();
    expect($grade)->not->toBeNull();
    expect($grade->grade)->toBe('1.75');
    expect($grade->status->value)->toBe('draft');
});

test('adviser can update a draft grade', function () {
    $adviser = User::factory()->withRole('adviser')->create();
    $student = createEnrolledStudent($adviser);
    $enrollmentSubject = $student->enrollments()->first()->subjects()->first();

    $this->actingAs($adviser)->post(route('adviser.grades.store'), [
        'enrollment_subject_id' => $enrollmentSubject->id,
        'grade' => '2.0',
    ]);

    $grade = Grade::where('adviser_id', $adviser->id)->first();

    $response = $this->actingAs($adviser)->put(route('adviser.grades.update', $grade), [
        'grade' => '1.5',
        'grade_point' => 1.5,
    ]);

    $response->assertRedirect();
    expect($grade->fresh()->grade)->toBe('1.5');
});

test('adviser can submit a draft grade with rubric when grade is not CE/T', function () {
    $adviser = User::factory()->withRole('adviser')->create();
    $student = createEnrolledStudent($adviser);
    $enrollmentSubject = $student->enrollments()->first()->subjects()->first();
    $rubric = Rubric::factory()->create(['user_id' => $adviser->id]);

    $this->actingAs($adviser)->post(route('adviser.grades.store'), [
        'enrollment_subject_id' => $enrollmentSubject->id,
        'grade' => '1.75',
        'rubric_id' => $rubric->id,
        'rubric_score' => 85,
    ]);

    $grade = Grade::where('adviser_id', $adviser->id)->first();

    $response = $this->actingAs($adviser)->post(route('adviser.grades.submit', $grade));

    $response->assertRedirect();
    expect($grade->fresh()->status->value)->toBe('submitted');
});

test('adviser cannot submit a draft grade without rubric when grade is not CE/T', function () {
    $adviser = User::factory()->withRole('adviser')->create();
    $student = createEnrolledStudent($adviser);
    $enrollmentSubject = $student->enrollments()->first()->subjects()->first();

    $this->actingAs($adviser)->post(route('adviser.grades.store'), [
        'enrollment_subject_id' => $enrollmentSubject->id,
        'grade' => '1.75',
    ]);

    $grade = Grade::where('adviser_id', $adviser->id)->first();

    $response = $this->actingAs($adviser)->post(route('adviser.grades.submit', $grade));

    $response->assertSessionHasErrors('rubric_id');
    expect($grade->fresh()->status->value)->toBe('draft');
});

test('adviser can submit a CE/T grade without rubric', function () {
    $adviser = User::factory()->withRole('adviser')->create();
    $student = createEnrolledStudent($adviser);
    $enrollmentSubject = $student->enrollments()->first()->subjects()->first();

    $this->actingAs($adviser)->post(route('adviser.grades.store'), [
        'enrollment_subject_id' => $enrollmentSubject->id,
        'grade' => 'CE',
    ]);

    $grade = Grade::where('adviser_id', $adviser->id)->first();

    $response = $this->actingAs($adviser)->post(route('adviser.grades.submit', $grade));

    $response->assertRedirect();
    expect($grade->fresh()->status->value)->toBe('submitted');
});

test('non-adviser cannot access grade routes', function () {
    $student = User::factory()->create();

    $response = $this->actingAs($student)->get(route('adviser.grades.index'));
    $response->assertForbidden();

    $response = $this->actingAs($student)->post(route('adviser.grades.store'), []);
    $response->assertForbidden();
});
