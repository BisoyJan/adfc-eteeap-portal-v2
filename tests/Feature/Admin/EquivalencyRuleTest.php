<?php

use App\Models\CurriculumSubject;
use App\Models\EquivalencyRule;
use App\Models\User;

test('admin can view the equivalency rules page', function () {
    $admin = User::factory()->withRole('admin')->create();

    $response = $this->actingAs($admin)->get(route('admin.equivalency-rules.index'));

    $response->assertOk();
});

test('admin can create an equivalency rule', function () {
    $admin = User::factory()->withRole('admin')->create();
    $subject = CurriculumSubject::create([
        'code' => 'CC201',
        'title' => 'Networking 1',
        'units' => 3,
        'category' => 'major',
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)->post(route('admin.equivalency-rules.store'), [
        'rule_type' => 'experience',
        'condition_key' => 'years_experience',
        'condition_value' => '5',
        'curriculum_subject_id' => $subject->id,
        'equivalent_credits' => 3,
        'priority' => 0,
        'is_active' => true,
    ]);

    $response->assertRedirect();
    expect(EquivalencyRule::where('curriculum_subject_id', $subject->id)->exists())->toBeTrue();
});

test('non-admins cannot manage equivalency rules', function () {
    $applicant = User::factory()->create();

    $response = $this->actingAs($applicant)->get(route('admin.equivalency-rules.index'));

    $response->assertForbidden();
});
