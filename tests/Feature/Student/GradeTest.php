<?php

use App\Enums\GradeStatus;
use App\Models\CurriculumSubject;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\User;

test('student can view their own grades', function () {
    $student = User::factory()->withRole('student')->create();

    $response = $this->actingAs($student)->get(route('student.grades.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('student/grades/index'));
});

test('student sees only their own grades', function () {
    $student = User::factory()->withRole('student')->create();
    $otherStudent = User::factory()->withRole('student')->create();
    $adviser = User::factory()->withRole('adviser')->create();
    $subject = CurriculumSubject::factory()->create();

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'enrollment_date' => now(),
        'student_id_number' => 'ETEEAP-2026-0005',
        'status' => 'enrolled',
    ]);

    Grade::create([
        'student_id' => $student->id,
        'curriculum_subject_id' => $subject->id,
        'enrollment_id' => $enrollment->id,
        'adviser_id' => $adviser->id,
        'grade' => '1.75',
        'status' => GradeStatus::Approved,
    ]);

    $otherEnrollment = Enrollment::create([
        'student_id' => $otherStudent->id,
        'enrollment_date' => now(),
        'student_id_number' => 'ETEEAP-2026-0006',
        'status' => 'enrolled',
    ]);

    Grade::create([
        'student_id' => $otherStudent->id,
        'curriculum_subject_id' => $subject->id,
        'enrollment_id' => $otherEnrollment->id,
        'adviser_id' => $adviser->id,
        'grade' => '2.0',
        'status' => GradeStatus::Approved,
    ]);

    $response = $this->actingAs($student)->get(route('student.grades.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('student/grades/index'));
});

test('non-student cannot access grades', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('student.grades.index'));

    $response->assertForbidden();
});
