<?php

use App\Enums\GradeStatus;
use App\Models\CurriculumSubject;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\User;

test('program head can view their dashboard', function () {
    $programHead = User::factory()->withRole('program_head')->create();

    $response = $this->actingAs($programHead)->get(route('program-head.dashboard'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('program-head/dashboard'));
});

test('program head dashboard shows grade approvals and adviser data', function () {
    $programHead = User::factory()->withRole('program_head')->create();
    $adviser = User::factory()->withRole('adviser')->create();
    $subject = CurriculumSubject::factory()->create();
    $student = User::factory()->withRole('student')->create();

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'enrollment_date' => now(),
        'student_id_number' => 'ETEEAP-PH-DASH',
        'status' => 'enrolled',
    ]);

    Grade::create([
        'student_id' => $student->id,
        'curriculum_subject_id' => $subject->id,
        'enrollment_id' => $enrollment->id,
        'adviser_id' => $adviser->id,
        'grade' => '2.0',
        'status' => GradeStatus::Submitted,
    ]);

    $response = $this->actingAs($programHead)->get(route('program-head.dashboard'));

    $response->assertInertia(fn ($page) => $page
        ->has('pendingGradeApprovals')
        ->has('advisers')
        ->has('curriculumStats')
    );
});

test('non-program-head cannot access program head dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('program-head.dashboard'));

    $response->assertForbidden();
});
