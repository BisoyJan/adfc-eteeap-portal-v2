<?php

use App\Models\AdviserAssignment;
use App\Models\User;

test('adviser can view their dashboard', function () {
    $adviser = User::factory()->withRole('adviser')->create();

    $response = $this->actingAs($adviser)->get(route('adviser.dashboard'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('adviser/dashboard'));
});

test('adviser dashboard shows their advisee count', function () {
    $adviser = User::factory()->withRole('adviser')->create();
    $student = User::factory()->withRole('student')->create();

    $programHead = User::factory()->withRole('program_head')->create();
    AdviserAssignment::create([
        'student_id' => $student->id,
        'adviser_id' => $adviser->id,
        'assigned_by' => $programHead->id,
        'is_active' => true,
        'assigned_at' => now(),
    ]);

    $response = $this->actingAs($adviser)->get(route('adviser.dashboard'));

    $response->assertInertia(fn ($page) => $page
        ->has('myAdviseesCount')
        ->has('studentProgress')
    );
});

test('non-adviser cannot access adviser dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('adviser.dashboard'));

    $response->assertForbidden();
});
