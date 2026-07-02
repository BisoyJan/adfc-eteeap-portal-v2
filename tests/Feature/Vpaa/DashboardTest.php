<?php

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\GraduationClearance;
use App\Models\User;

test('vpaa can view their dashboard', function () {
    $vpaa = User::factory()->withRole('vpaa')->create();

    $response = $this->actingAs($vpaa)->get(route('vpaa.dashboard'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('vpaa/dashboard'));
});

test('vpaa dashboard shows program overview and audit summary', function () {
    $vpaa = User::factory()->withRole('vpaa')->create();
    $student = User::factory()->withRole('student')->create();

    GraduationClearance::create([
        'student_id' => $student->id,
        'status' => 'in_review',
    ]);

    $response = $this->actingAs($vpaa)->get(route('vpaa.dashboard'));

    $response->assertInertia(fn ($page) => $page
        ->has('programOverview')
        ->has('pendingApprovals')
        ->has('auditSummary')
    );
});

test('non-vpaa cannot access vpaa dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('vpaa.dashboard'));

    $response->assertForbidden();
});
