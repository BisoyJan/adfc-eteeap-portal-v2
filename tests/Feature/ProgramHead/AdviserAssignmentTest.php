<?php

use App\Models\AdviserAssignment;
use App\Models\User;

test('program head can view the adviser assignments index', function () {
    $programHead = User::factory()->withRole('program_head')->create();

    $response = $this->actingAs($programHead)->get(route('program-head.adviser-assignments.index'));

    $response->assertOk();
});

test('program head can view the adviser workload report', function () {
    $programHead = User::factory()->withRole('program_head')->create();
    User::factory()->withRole('adviser')->create();

    $response = $this->actingAs($programHead)->get(route('program-head.adviser-workload'));

    $response->assertOk();
});

test('program head can assign an adviser to a student', function () {
    $programHead = User::factory()->withRole('program_head')->create();
    $adviser = User::factory()->withRole('adviser')->create();
    $student = User::factory()->withRole('student')->create();

    $response = $this->actingAs($programHead)->post(route('program-head.adviser-assignments.store'), [
        'student_id' => $student->id,
        'adviser_id' => $adviser->id,
        'term' => '1st Sem 2026-2027',
    ]);

    $response->assertRedirect(route('program-head.adviser-assignments.index'));
    expect(AdviserAssignment::where('adviser_id', $adviser->id)->where('student_id', $student->id)->exists())->toBeTrue();
});

test('program head cannot exceed the adviser max student load', function () {
    $programHead = User::factory()->withRole('program_head')->create();
    $adviser = User::factory()->withRole('adviser')->create();
    $maxStudents = (int) config('eteeap.adviser_max_students');

    for ($i = 0; $i < $maxStudents; $i++) {
        AdviserAssignment::create([
            'student_id' => User::factory()->withRole('student')->create()->id,
            'adviser_id' => $adviser->id,
            'assigned_by' => $programHead->id,
            'assigned_at' => now(),
            'is_active' => true,
        ]);
    }

    $oneMoreStudent = User::factory()->withRole('student')->create();

    $response = $this->actingAs($programHead)->post(route('program-head.adviser-assignments.store'), [
        'student_id' => $oneMoreStudent->id,
        'adviser_id' => $adviser->id,
    ]);

    $response->assertStatus(422);
    expect(AdviserAssignment::where('adviser_id', $adviser->id)->count())->toBe($maxStudents);
});
