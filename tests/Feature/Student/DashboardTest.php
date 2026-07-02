<?php

use App\Models\EnhancementActivity;
use App\Models\Grade;
use App\Models\User;

test('student can view their dashboard', function () {
    $student = User::factory()->withRole('student')->create();

    $response = $this->actingAs($student)->get(route('student.dashboard'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('student/dashboard'));
});

test('student dashboard shows progress and deadlines', function () {
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

    $response = $this->actingAs($student)->get(route('student.dashboard'));

    $response->assertInertia(fn ($page) => $page
        ->has('progressPercentage')
        ->has('upcomingDeadlines')
        ->has('recentGrades')
    );
});

test('non-student cannot access student dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('student.dashboard'));

    $response->assertForbidden();
});
