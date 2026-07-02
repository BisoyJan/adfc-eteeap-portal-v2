<?php

use App\Enums\ApplicationStatus;
use App\Models\User;

test('program head can save and submit an initial equivalency matrix', function () {
    $programHead = User::factory()->withRole('program_head')->create();
    $applicant = User::factory()->create();
    $application = $applicant->applications()->create([
        'application_number' => 'APP-2026-0003',
        'status' => ApplicationStatus::UnderReview,
    ]);

    $response = $this->actingAs($programHead)->put(route('program-head.equivalency.update', $application), [
        'items' => [
            [
                'subject_code' => 'CC101',
                'subject_title' => 'Introduction to Computing',
                'units' => 3,
                'competency_level' => 'full',
                'equivalent_credits' => 3,
                'recommended_action' => 'credit',
            ],
        ],
    ]);

    $response->assertRedirect();
    $matrix = $application->equivalencyMatrices()->where('type', 'initial')->first();
    expect($matrix->items()->count())->toBe(1);

    $submitResponse = $this->actingAs($programHead)->post(route('program-head.equivalency.submit', $application));
    $submitResponse->assertRedirect(route('program-head.equivalency.show', $application));
    expect($matrix->fresh()->status->value)->toBe('submitted');
});

test('program head can view the pending equivalency queue', function () {
    $programHead = User::factory()->withRole('program_head')->create();

    $response = $this->actingAs($programHead)->get(route('program-head.equivalency.pending'));

    $response->assertOk();
});
