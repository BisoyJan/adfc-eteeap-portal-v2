<?php

use App\Console\Commands\SendDeadlineReminders;
use App\Models\EnhancementActivity;
use App\Models\Interview;
use App\Models\Notification;
use App\Models\User;

test('sends reminders for enhancement activities due in 7, 3, and 1 days', function () {
    $student = User::factory()->withRole('student')->create();
    $adviser = User::factory()->withRole('adviser')->create();

    EnhancementActivity::create([
        'student_id' => $student->id,
        'adviser_id' => $adviser->id,
        'title' => 'Activity Due 7 Days',
        'activity_type' => 'modular_study',
        'status' => 'assigned',
        'due_date' => now()->addDays(7),
    ]);

    EnhancementActivity::create([
        'student_id' => $student->id,
        'adviser_id' => $adviser->id,
        'title' => 'Activity Due 3 Days',
        'activity_type' => 'case_analysis',
        'status' => 'in_progress',
        'due_date' => now()->addDays(3),
    ]);

    EnhancementActivity::create([
        'student_id' => $student->id,
        'adviser_id' => $adviser->id,
        'title' => 'Activity Due 1 Day',
        'activity_type' => 'research',
        'status' => 'assigned',
        'due_date' => now()->addDay(),
    ]);

    $this->artisan(SendDeadlineReminders::class)->assertSuccessful();

    expect(Notification::where('user_id', $student->id)->where('type', 'deadline_reminder')->count())->toBe(3);
});

test('skips activities with null due_date', function () {
    $student = User::factory()->withRole('student')->create();
    $adviser = User::factory()->withRole('adviser')->create();

    EnhancementActivity::create([
        'student_id' => $student->id,
        'adviser_id' => $adviser->id,
        'title' => 'No Due Date',
        'activity_type' => 'modular_study',
        'status' => 'assigned',
        'due_date' => null,
    ]);

    $this->artisan(SendDeadlineReminders::class)->assertSuccessful();

    expect(Notification::where('type', 'deadline_reminder')->count())->toBe(0);
});

test('does not send duplicate reminders on same day', function () {
    $student = User::factory()->withRole('student')->create();
    $adviser = User::factory()->withRole('adviser')->create();

    EnhancementActivity::create([
        'student_id' => $student->id,
        'adviser_id' => $adviser->id,
        'title' => 'Due 7 Days',
        'activity_type' => 'modular_study',
        'status' => 'assigned',
        'due_date' => now()->addDays(7),
    ]);

    $this->artisan(SendDeadlineReminders::class)->assertSuccessful();
    $this->artisan(SendDeadlineReminders::class)->assertSuccessful();

    expect(Notification::where('user_id', $student->id)->where('type', 'deadline_reminder')->count())->toBe(1);
});

test('sends interview reminders for scheduled interviews', function () {
    $applicant = User::factory()->create();
    $application = $applicant->applications()->create([
        'application_number' => 'APP-INT-RM-001',
        'status' => 'under_review',
    ]);
    $panelist = User::factory()->withRole('assessor_internal')->create();

    $coordinator = User::factory()->withRole('coordinator')->create();
    $interview = Interview::create([
        'application_id' => $application->id,
        'created_by' => $coordinator->id,
        'status' => 'scheduled',
        'scheduled_at' => now()->addDay()->setHour(10)->setMinute(0),
        'location' => 'Room B',
    ]);

    $interview->panelists()->attach($panelist->id);

    $this->artisan(SendDeadlineReminders::class)->assertSuccessful();

    expect(
        Notification::where('type', 'interview_reminder')->count()
    )->toBe(2);

    expect(
        Notification::where('user_id', $applicant->id)->where('type', 'interview_reminder')->exists()
    )->toBeTrue();

    expect(
        Notification::where('user_id', $panelist->id)->where('type', 'interview_reminder')->exists()
    )->toBeTrue();
});

test('skips interviews that are not scheduled', function () {
    $applicant = User::factory()->create();
    $application = $applicant->applications()->create([
        'application_number' => 'APP-INT-RM-002',
        'status' => 'under_review',
    ]);

    $coordinator = User::factory()->withRole('coordinator')->create();
    Interview::create([
        'application_id' => $application->id,
        'created_by' => $coordinator->id,
        'status' => 'completed',
        'scheduled_at' => now()->addDay(),
        'location' => 'Room C',
    ]);

    $this->artisan(SendDeadlineReminders::class)->assertSuccessful();

    expect(Notification::where('type', 'interview_reminder')->count())->toBe(0);
});
