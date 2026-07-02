<?php

use App\Enums\ApplicationStatus;
use App\Enums\GradeStatus;
use App\Models\AdviserAssignment;
use App\Models\Application;
use App\Models\CurriculumSubject;
use App\Models\Document;
use App\Models\EnhancementActivity;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Notification;
use App\Models\User;

test('creates notification for coordinators when application is submitted', function () {
    User::factory()->withRole('coordinator')->create();

    $applicant = User::factory()->create();
    $applicant->applications()->create([
        'application_number' => 'APP-TEST-001',
        'status' => ApplicationStatus::Submitted,
        'submitted_at' => now(),
    ]);

    expect(Notification::where('type', 'application_submitted')->exists())->toBeTrue();
});

test('creates notification for applicant when application is approved', function () {
    $applicant = User::factory()->create();
    $application = $applicant->applications()->create([
        'application_number' => 'APP-TEST-002',
        'status' => ApplicationStatus::Submitted,
        'submitted_at' => now(),
    ]);

    $application->update(['status' => ApplicationStatus::Approved]);

    expect(
        Notification::where('user_id', $applicant->id)
            ->where('type', 'application_approved')
            ->exists()
    )->toBeTrue();
});

test('creates notification for applicant when application is rejected', function () {
    $applicant = User::factory()->create();
    $application = $applicant->applications()->create([
        'application_number' => 'APP-TEST-003',
        'status' => ApplicationStatus::Submitted,
        'submitted_at' => now(),
    ]);

    $application->update([
        'status' => ApplicationStatus::Rejected,
        'rejection_reason' => 'Incomplete documents.',
    ]);

    expect(
        Notification::where('user_id', $applicant->id)
            ->where('type', 'application_rejected')
            ->exists()
    )->toBeTrue();
});

test('creates notification when document is verified', function () {
    $user = User::factory()->create();
    $application = $user->applications()->create([
        'application_number' => 'APP-DOC-VFY',
        'status' => ApplicationStatus::Draft,
    ]);
    $document = Document::create([
        'user_id' => $user->id,
        'application_id' => $application->id,
        'document_type' => 'psa_birth',
        'file_name' => 'birth.pdf',
        'file_path' => 'documents/birth.pdf',
        'mime_type' => 'application/pdf',
        'file_size' => 1024,
        'status' => 'uploaded',
    ]);

    $document->update(['status' => 'verified']);

    expect(
        Notification::where('user_id', $user->id)
            ->where('type', 'document_verified')
            ->exists()
    )->toBeTrue();
});

test('creates notification when document is rejected', function () {
    $user = User::factory()->create();
    $application = $user->applications()->create([
        'application_number' => 'APP-DOC-REJ',
        'status' => ApplicationStatus::Draft,
    ]);
    $document = Document::create([
        'user_id' => $user->id,
        'application_id' => $application->id,
        'document_type' => 'psa_birth',
        'file_name' => 'birth.pdf',
        'file_path' => 'documents/birth.pdf',
        'mime_type' => 'application/pdf',
        'file_size' => 1024,
        'status' => 'uploaded',
    ]);

    $document->update([
        'status' => 'rejected',
        'rejection_reason' => 'Illegible scan.',
    ]);

    expect(
        Notification::where('user_id', $user->id)
            ->where('type', 'document_rejected')
            ->exists()
    )->toBeTrue();
});

test('creates notifications for student and adviser on adviser assignment', function () {
    $student = User::factory()->withRole('student')->create();
    $adviser = User::factory()->withRole('adviser')->create();

    $programHead = User::factory()->withRole('program_head')->create();
    AdviserAssignment::create([
        'student_id' => $student->id,
        'adviser_id' => $adviser->id,
        'assigned_by' => $programHead->id,
        'is_active' => true,
        'assigned_at' => now(),
    ]);

    expect(Notification::where('user_id', $student->id)->where('type', 'adviser_assigned')->exists())->toBeTrue();
    expect(Notification::where('user_id', $adviser->id)->where('type', 'student_assigned')->exists())->toBeTrue();
});

test('creates notification for student when enhancement activity is created', function () {
    $student = User::factory()->withRole('student')->create();
    $adviser = User::factory()->withRole('adviser')->create();

    EnhancementActivity::create([
        'student_id' => $student->id,
        'adviser_id' => $adviser->id,
        'title' => 'Complete Module 1',
        'activity_type' => 'modular_study',
        'status' => 'assigned',
        'due_date' => now()->addDays(7),
    ]);

    expect(
        Notification::where('user_id', $student->id)
            ->where('type', 'activity_assigned')
            ->exists()
    )->toBeTrue();
});

test('creates notification for adviser when activity is submitted', function () {
    $student = User::factory()->withRole('student')->create();
    $adviser = User::factory()->withRole('adviser')->create();
    $activity = EnhancementActivity::create([
        'student_id' => $student->id,
        'adviser_id' => $adviser->id,
        'title' => 'Complete Module 1',
        'activity_type' => 'modular_study',
        'status' => 'assigned',
        'due_date' => now()->addDays(7),
    ]);

    $activity->update(['status' => 'submitted']);

    expect(
        Notification::where('user_id', $adviser->id)
            ->where('type', 'activity_submitted')
            ->exists()
    )->toBeTrue();
});

test('creates notification for student when grade is approved', function () {
    $student = User::factory()->withRole('student')->create();
    $adviser = User::factory()->withRole('adviser')->create();
    $subject = CurriculumSubject::factory()->create();
    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'enrollment_date' => now(),
        'student_id_number' => 'ETEEAP-TEST-GRADE',
        'status' => 'enrolled',
    ]);

    $grade = Grade::create([
        'student_id' => $student->id,
        'curriculum_subject_id' => $subject->id,
        'enrollment_id' => $enrollment->id,
        'adviser_id' => $adviser->id,
        'grade' => '1.5',
        'status' => GradeStatus::Submitted,
    ]);

    $grade->update(['status' => GradeStatus::Approved]);

    expect(
        Notification::where('user_id', $student->id)
            ->where('type', 'grade_posted')
            ->exists()
    )->toBeTrue();
});
