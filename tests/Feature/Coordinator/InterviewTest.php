<?php

use App\Enums\ApplicationStatus;
use App\Models\User;
use App\Services\InterviewService;

test('coordinator can schedule an interview once a panel has been assigned', function () {
    $applicant = User::factory()->create();
    $application = $applicant->applications()->create([
        'application_number' => 'APP-2026-0004',
        'status' => ApplicationStatus::UnderReview,
    ]);

    $programHead = User::factory()->withRole('program_head')->create();
    $internal = User::factory()->withRole('assessor_internal')->create();
    $external = User::factory()->withRole('assessor_external')->create();
    $another = User::factory()->withRole('assessor_internal')->create();

    $interview = app(InterviewService::class)->assignPanel($application, $programHead, [
        $internal->id, $external->id, $another->id,
    ]);

    $coordinator = User::factory()->withRole('coordinator')->create();

    $response = $this->actingAs($coordinator)->put(route('coordinator.interviews.schedule', $interview), [
        'scheduled_at' => now()->addDays(5)->toDateTimeString(),
        'location' => 'Conference Room A',
    ]);

    $response->assertRedirect();
    $interview->refresh();
    expect($interview->status->value)->toBe('scheduled');
    expect($interview->orientation_at->isSameDay($interview->scheduled_at->copy()->subDays(2)))->toBeTrue();
});

test('coordinator can list all interviews', function () {
    $coordinator = User::factory()->withRole('coordinator')->create();

    $response = $this->actingAs($coordinator)->get(route('coordinator.interviews.index'));

    $response->assertOk();
});
