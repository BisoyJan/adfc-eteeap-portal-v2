<?php

use App\Models\AdviserAssignment;
use App\Models\EnhancementActivity;
use App\Models\User;

function makeAdviserWithStudent(): array
{
    $adviser = User::factory()->withRole('adviser')->create();
    $student = User::factory()->withRole('student')->create();

    AdviserAssignment::create([
        'student_id' => $student->id,
        'adviser_id' => $adviser->id,
        'assigned_by' => User::factory()->withRole('program_head')->create()->id,
        'assigned_at' => now(),
        'is_active' => true,
    ]);

    return [$adviser, $student];
}

test('adviser can view their activities index', function () {
    [$adviser] = makeAdviserWithStudent();

    $response = $this->actingAs($adviser)->get(route('adviser.activities.index'));

    $response->assertOk();
});

test('adviser can view the activity creation form with their active students', function () {
    [$adviser] = makeAdviserWithStudent();

    $response = $this->actingAs($adviser)->get(route('adviser.activities.create'));

    $response->assertOk();
});

test('adviser can assign an enhancement activity to their student', function () {
    [$adviser, $student] = makeAdviserWithStudent();

    $response = $this->actingAs($adviser)->post(route('adviser.activities.store'), [
        'student_id' => $student->id,
        'activity_type' => 'modular_study',
        'title' => 'Complete Module 1',
        'description' => 'Read and summarize module 1.',
    ]);

    $response->assertRedirect(route('adviser.activities.index'));
    expect(EnhancementActivity::where('student_id', $student->id)->where('adviser_id', $adviser->id)->exists())->toBeTrue();
});

test('adviser can view an activity detail page', function () {
    [$adviser, $student] = makeAdviserWithStudent();
    $activity = EnhancementActivity::create([
        'student_id' => $student->id,
        'adviser_id' => $adviser->id,
        'activity_type' => 'modular_study',
        'title' => 'Complete Module 1',
        'status' => 'assigned',
    ]);

    $response = $this->actingAs($adviser)->get(route('adviser.activities.show', $activity));

    $response->assertOk();
});

test('adviser can score a submitted activity', function () {
    [$adviser, $student] = makeAdviserWithStudent();
    $activity = EnhancementActivity::create([
        'student_id' => $student->id,
        'adviser_id' => $adviser->id,
        'activity_type' => 'modular_study',
        'title' => 'Complete Module 1',
        'status' => 'submitted',
    ]);

    $response = $this->actingAs($adviser)->put(route('adviser.activities.score', $activity), [
        'score' => 92,
        'feedback' => 'Great work!',
    ]);

    $response->assertRedirect(route('adviser.activities.index'));
    $activity->refresh();
    expect((float) $activity->score)->toBe(92.0);
    expect($activity->status->value)->toBe('approved');
});

test('adviser can view pending submissions', function () {
    [$adviser, $student] = makeAdviserWithStudent();
    EnhancementActivity::create([
        'student_id' => $student->id,
        'adviser_id' => $adviser->id,
        'activity_type' => 'modular_study',
        'title' => 'Complete Module 1',
        'status' => 'submitted',
    ]);

    $response = $this->actingAs($adviser)->get(route('adviser.activities.submissions'));

    $response->assertOk();
});
