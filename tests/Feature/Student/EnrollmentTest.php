<?php

use App\Enums\EnrollmentSubjectStatus;
use App\Models\CurriculumSubject;
use App\Models\Enrollment;
use App\Models\EnrollmentSubject;
use App\Models\User;

test('student can view their curriculum checklist', function () {
    $student = User::factory()->withRole('student')->create();

    $response = $this->actingAs($student)->get(route('student.curriculum-checklist'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('student/curriculum-checklist'));
});

test('curriculum checklist shows not_taken for unenrolled subjects', function () {
    $student = User::factory()->withRole('student')->create();
    $subject = CurriculumSubject::factory()->create(['is_active' => true]);

    $response = $this->actingAs($student)->get(route('student.curriculum-checklist'));

    $response->assertOk();
});

test('curriculum checklist shows credited status for credited subjects', function () {
    $student = User::factory()->withRole('student')->create();
    $subject = CurriculumSubject::factory()->create(['is_active' => true]);

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'enrollment_date' => now(),
        'student_id_number' => 'ETEEAP-2026-0004',
        'status' => 'enrolled',
    ]);

    EnrollmentSubject::create([
        'enrollment_id' => $enrollment->id,
        'curriculum_subject_id' => $subject->id,
        'status' => EnrollmentSubjectStatus::Credited,
    ]);

    $response = $this->actingAs($student)->get(route('student.curriculum-checklist'));

    $response->assertOk();
});

test('non-student cannot access curriculum checklist', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('student.curriculum-checklist'));

    $response->assertForbidden();
});
