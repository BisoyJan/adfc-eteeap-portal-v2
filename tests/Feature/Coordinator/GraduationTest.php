<?php

use App\Enums\EnrollmentSubjectStatus;
use App\Models\CurriculumSubject;
use App\Models\Enrollment;
use App\Models\EnrollmentSubject;
use App\Models\User;

function createEligibleGraduationStudent(): User
{
    $student = User::factory()->withRole('student')->create();

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'enrollment_date' => now(),
        'student_id_number' => 'ETEEAP-2026-0100',
        'status' => 'enrolled',
    ]);

    $subject = CurriculumSubject::factory()->create(['is_active' => true]);

    EnrollmentSubject::create([
        'enrollment_id' => $enrollment->id,
        'curriculum_subject_id' => $subject->id,
        'status' => EnrollmentSubjectStatus::Completed,
    ]);

    return $student;
}

function createIneligibleGraduationStudent(): User
{
    $student = User::factory()->withRole('student')->create();

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'enrollment_date' => now(),
        'student_id_number' => 'ETEEAP-2026-0101',
        'status' => 'enrolled',
    ]);

    $subject = CurriculumSubject::factory()->create(['is_active' => true]);

    EnrollmentSubject::create([
        'enrollment_id' => $enrollment->id,
        'curriculum_subject_id' => $subject->id,
        'status' => EnrollmentSubjectStatus::Pending,
    ]);

    return $student;
}

test('coordinator can view graduation pipeline', function () {
    $coordinator = User::factory()->withRole('coordinator')->create();

    $response = $this->actingAs($coordinator)->get(route('coordinator.graduation.pipeline'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('coordinator/graduation/index'));
});

test('eligible students appear in the pipeline', function () {
    $coordinator = User::factory()->withRole('coordinator')->create();
    createEligibleGraduationStudent();

    $response = $this->actingAs($coordinator)->get(route('coordinator.graduation.pipeline'));

    $response->assertInertia(fn ($page) => $page->has('eligibleStudents', 1));
});

test('ineligible students do not appear in the pipeline', function () {
    $coordinator = User::factory()->withRole('coordinator')->create();
    createIneligibleGraduationStudent();

    $response = $this->actingAs($coordinator)->get(route('coordinator.graduation.pipeline'));

    $response->assertInertia(fn ($page) => $page->has('eligibleStudents', 0));
});

test('coordinator can initiate clearance for eligible student', function () {
    $coordinator = User::factory()->withRole('coordinator')->create();
    $student = createEligibleGraduationStudent();

    $response = $this->actingAs($coordinator)->post(route('coordinator.graduation.store'), [
        'student_id' => $student->id,
    ]);

    $response->assertRedirect();

    $clearance = $student->fresh()->graduationClearance;
    expect($clearance)->not->toBeNull();
    expect($clearance->status->value)->toBe('pending');
    expect($clearance->documents()->count())->toBe(12);
});

test('initiating clearance for ineligible student is blocked', function () {
    $coordinator = User::factory()->withRole('coordinator')->create();
    $student = createIneligibleGraduationStudent();

    $response = $this->actingAs($coordinator)->post(route('coordinator.graduation.store'), [
        'student_id' => $student->id,
    ]);

    $response->assertSessionHasErrors('student_id');
});

test('initiating duplicate clearance is blocked', function () {
    $coordinator = User::factory()->withRole('coordinator')->create();
    $student = createEligibleGraduationStudent();

    $this->actingAs($coordinator)->post(route('coordinator.graduation.store'), [
        'student_id' => $student->id,
    ]);

    $response = $this->actingAs($coordinator)->post(route('coordinator.graduation.store'), [
        'student_id' => $student->id,
    ]);

    $response->assertSessionHasErrors('student_id');
});

test('coordinator can verify a clearance document', function () {
    $coordinator = User::factory()->withRole('coordinator')->create();
    $student = createEligibleGraduationStudent();

    $this->actingAs($coordinator)->post(route('coordinator.graduation.store'), [
        'student_id' => $student->id,
    ]);

    $clearance = $student->fresh()->graduationClearance;
    $doc = $clearance->documents()->first();

    $response = $this->actingAs($coordinator)->put(
        route('coordinator.graduation.documents.verify', [$clearance->id, $doc->id]),
    );

    $response->assertRedirect();
    expect($doc->fresh()->is_verified)->toBeTrue();
    expect($doc->fresh()->verified_by)->toBe($coordinator->id);
});

test('confirmation is blocked until all 12 documents are verified', function () {
    $coordinator = User::factory()->withRole('coordinator')->create();
    $student = createEligibleGraduationStudent();

    $this->actingAs($coordinator)->post(route('coordinator.graduation.store'), [
        'student_id' => $student->id,
    ]);

    $clearance = $student->fresh()->graduationClearance;

    $response = $this->actingAs($coordinator)->post(
        route('coordinator.graduation.confirm', $clearance->id),
    );

    $response->assertSessionHasErrors('clearance');
});

test('non-coordinator cannot access graduation routes', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('coordinator.graduation.pipeline'));
    $response->assertForbidden();

    $response = $this->actingAs($user)->post(route('coordinator.graduation.store'), [
        'student_id' => 1,
    ]);
    $response->assertForbidden();
});
