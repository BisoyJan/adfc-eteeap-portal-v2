<?php

use App\Models\EnhancementActivity;
use App\Models\User;

test('qa officer can view their dashboard', function () {
    $qaOfficer = User::factory()->withRole('qa_officer')->create();

    $response = $this->actingAs($qaOfficer)->get(route('qa-officer.dashboard'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('qa-officer/dashboard'));
});

test('qa officer dashboard shows enhancement overview', function () {
    $qaOfficer = User::factory()->withRole('qa_officer')->create();
    $student = User::factory()->withRole('student')->create();
    $adviser = User::factory()->withRole('adviser')->create();

    EnhancementActivity::create([
        'student_id' => $student->id,
        'adviser_id' => $adviser->id,
        'title' => 'Sample Activity',
        'activity_type' => 'modular_study',
        'status' => 'submitted',
    ]);

    $response = $this->actingAs($qaOfficer)->get(route('qa-officer.dashboard'));

    $response->assertInertia(fn ($page) => $page
        ->has('pendingF3Validations')
        ->has('enhancementOverview')
        ->has('recentSubmissions')
    );
});

test('non-qa-officer cannot access qa officer dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('qa-officer.dashboard'));

    $response->assertForbidden();
});
