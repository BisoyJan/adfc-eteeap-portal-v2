<?php

use App\Enums\EnrollmentSubjectStatus;
use App\Models\CurriculumSubject;
use App\Models\Enrollment;
use App\Models\EnrollmentSubject;
use App\Models\GraduationClearance;
use App\Models\User;

function createApprovedClearance(): GraduationClearance
{
    $coordinator = User::factory()->withRole('coordinator')->create();

    $student = User::factory()->withRole('student')->create();

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'enrollment_date' => now(),
        'student_id_number' => 'ETEEAP-2026-0300',
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
        'status' => 'approved',
        'registry_number' => 'RGN-2026-0001',
        'vpaa_approved_at' => now(),
        'diploma_printed_at' => now(),
        'tor_released_at' => now(),
    ]);

    return $clearance;
}

test('student can view their graduation clearance', function () {
    $clearance = createApprovedClearance();
    $student = $clearance->student;

    $response = $this->actingAs($student)->get(route('student.graduation.show'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('student/graduation/index'));
});

test('student without clearance sees no clearance state', function () {
    $student = User::factory()->withRole('student')->create();

    $response = $this->actingAs($student)->get(route('student.graduation.show'));

    $response->assertInertia(fn ($page) => $page->where('clearance', null));
});

test('student can submit clearance form and auto-release tor', function () {
    $clearance = createApprovedClearance();
    $student = $clearance->student;

    $fakeFile = Illuminate\Http\UploadedFile::fake()->create('clearance.pdf', 100, 'application/pdf');

    $response = $this->actingAs($student)->post(
        route('student.graduation.clearance.submit'),
        ['file' => $fakeFile],
    );

    $response->assertRedirect();

    $clearance->refresh();
    expect($clearance->clearance_form_path)->not->toBeNull();
    expect($clearance->status->value)->toBe('completed');
    expect($clearance->tor_released_at)->not->toBeNull();
});

test('submitting clearance form without approval is blocked', function () {
    $coordinator = User::factory()->withRole('coordinator')->create();
    $student = User::factory()->withRole('student')->create();

    $clearance = GraduationClearance::create([
        'student_id' => $student->id,
        'status' => 'pending',
    ]);

    $fakeFile = Illuminate\Http\UploadedFile::fake()->create('clearance.pdf', 100, 'application/pdf');

    $response = $this->actingAs($student)->post(
        route('student.graduation.clearance.submit'),
        ['file' => $fakeFile],
    );

    $response->assertSessionHasErrors('clearance');
});

test('diploma download is blocked when not available', function () {
    $student = User::factory()->withRole('student')->create();

    $clearance = GraduationClearance::create([
        'student_id' => $student->id,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($student)->get(route('student.graduation.diploma.download'));

    // Should redirect with error since diploma not yet available
    $response->assertRedirect();
});

test('tor download is blocked when not available', function () {
    $student = User::factory()->withRole('student')->create();

    $clearance = GraduationClearance::create([
        'student_id' => $student->id,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($student)->get(route('student.graduation.tor.download'));

    $response->assertRedirect();
});

test('non-student cannot access graduation routes', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('student.graduation.show'));
    $response->assertForbidden();
});
