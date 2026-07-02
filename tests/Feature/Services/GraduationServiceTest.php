<?php

use App\Enums\EnrollmentSubjectStatus;
use App\Models\CurriculumSubject;
use App\Models\Enrollment;
use App\Models\EnrollmentSubject;
use App\Models\GraduationClearance;
use App\Models\User;
use App\Services\GraduationService;

test('eligibleStudents returns students with all completed subjects', function () {
    $student = User::factory()->withRole('student')->create();

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'enrollment_date' => now(),
        'student_id_number' => 'ETEEAP-2026-0400',
        'status' => 'enrolled',
    ]);

    $subject = CurriculumSubject::factory()->create(['is_active' => true]);

    EnrollmentSubject::create([
        'enrollment_id' => $enrollment->id,
        'curriculum_subject_id' => $subject->id,
        'status' => EnrollmentSubjectStatus::Completed,
    ]);

    $eligible = app(GraduationService::class)->eligibleStudents();

    expect($eligible->pluck('id'))->toContain($student->id);
});

test('eligibleStudents excludes students with pending subjects', function () {
    $student = User::factory()->withRole('student')->create();

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'enrollment_date' => now(),
        'student_id_number' => 'ETEEAP-2026-0401',
        'status' => 'enrolled',
    ]);

    $subject = CurriculumSubject::factory()->create(['is_active' => true]);

    EnrollmentSubject::create([
        'enrollment_id' => $enrollment->id,
        'curriculum_subject_id' => $subject->id,
        'status' => EnrollmentSubjectStatus::Pending,
    ]);

    $eligible = app(GraduationService::class)->eligibleStudents();

    expect($eligible->pluck('id'))->not->toContain($student->id);
});

test('eligibleStudents excludes students with existing clearance', function () {
    $student = User::factory()->withRole('student')->create();

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'enrollment_date' => now(),
        'student_id_number' => 'ETEEAP-2026-0402',
        'status' => 'enrolled',
    ]);

    $subject = CurriculumSubject::factory()->create(['is_active' => true]);

    EnrollmentSubject::create([
        'enrollment_id' => $enrollment->id,
        'curriculum_subject_id' => $subject->id,
        'status' => EnrollmentSubjectStatus::Completed,
    ]);

    GraduationClearance::create([
        'student_id' => $student->id,
        'status' => 'pending',
    ]);

    $eligible = app(GraduationService::class)->eligibleStudents();

    expect($eligible->pluck('id'))->not->toContain($student->id);
});

test('eligibleStudents includes credited and transferred statuses', function () {
    $student1 = User::factory()->withRole('student')->create();
    $student2 = User::factory()->withRole('student')->create();

    $enrollment1 = Enrollment::create([
        'student_id' => $student1->id,
        'enrollment_date' => now(),
        'student_id_number' => 'ETEEAP-2026-0403',
        'status' => 'enrolled',
    ]);

    $enrollment2 = Enrollment::create([
        'student_id' => $student2->id,
        'enrollment_date' => now(),
        'student_id_number' => 'ETEEAP-2026-0404',
        'status' => 'enrolled',
    ]);

    $subject = CurriculumSubject::factory()->create(['is_active' => true]);

    EnrollmentSubject::create([
        'enrollment_id' => $enrollment1->id,
        'curriculum_subject_id' => $subject->id,
        'status' => EnrollmentSubjectStatus::Credited,
    ]);

    EnrollmentSubject::create([
        'enrollment_id' => $enrollment2->id,
        'curriculum_subject_id' => $subject->id,
        'status' => EnrollmentSubjectStatus::Transferred,
    ]);

    $eligible = app(GraduationService::class)->eligibleStudents();

    expect($eligible->pluck('id'))->toContain($student1->id);
    expect($eligible->pluck('id'))->toContain($student2->id);
});

test('generateRegistryNumber returns correct format', function () {
    $number = app(GraduationService::class)->generateRegistryNumber();

    expect($number)->toMatch('/^RGN-\d{4}-\d{4}$/');
});

test('generateRegistryNumber increments for each call', function () {
    $service = app(GraduationService::class);

    $num1 = $service->generateRegistryNumber();

    // Persist a clearance to force the next number to increment
    $student = User::factory()->withRole('student')->create();
    GraduationClearance::create([
        'student_id' => $student->id,
        'status' => 'approved',
        'registry_number' => $num1,
    ]);

    $num2 = $service->generateRegistryNumber();

    expect($num2)->not->toBe($num1);
});
