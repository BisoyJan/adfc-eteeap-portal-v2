<?php

use App\Models\CurriculumSubject;
use App\Models\EquivalencyRule;
use App\Models\User;
use App\Services\EquivalencyEngine;

test('suggests a matrix item when a user matches an experience-based rule', function () {
    $subject = CurriculumSubject::create([
        'code' => 'CC201',
        'title' => 'Networking 1',
        'units' => 3,
        'category' => 'major',
        'is_active' => true,
    ]);

    EquivalencyRule::create([
        'rule_type' => 'experience',
        'condition_key' => 'years_experience',
        'condition_value' => '5',
        'curriculum_subject_id' => $subject->id,
        'equivalent_credits' => 3,
        'priority' => 0,
        'is_active' => true,
    ]);

    $user = User::factory()->create();
    $user->experienceRecords()->create([
        'company' => 'Acme Corp',
        'position' => 'Network Engineer',
        'start_date' => now()->subYears(6),
        'is_current' => true,
    ]);

    $suggestions = app(EquivalencyEngine::class)->suggestFor($user);

    expect($suggestions)->toHaveCount(1);
    expect($suggestions->first()['subject_code'])->toBe('CC201');
    expect((float) $suggestions->first()['equivalent_credits'])->toBe(3.0);
});

test('does not suggest a matrix item when the rule condition is not met', function () {
    $subject = CurriculumSubject::create([
        'code' => 'CC201',
        'title' => 'Networking 1',
        'units' => 3,
        'category' => 'major',
        'is_active' => true,
    ]);

    EquivalencyRule::create([
        'rule_type' => 'experience',
        'condition_key' => 'years_experience',
        'condition_value' => '5',
        'curriculum_subject_id' => $subject->id,
        'equivalent_credits' => 3,
        'priority' => 0,
        'is_active' => true,
    ]);

    $user = User::factory()->create();
    $user->experienceRecords()->create([
        'company' => 'Acme Corp',
        'position' => 'Junior Developer',
        'start_date' => now()->subYears(1),
        'is_current' => true,
    ]);

    $suggestions = app(EquivalencyEngine::class)->suggestFor($user);

    expect($suggestions)->toHaveCount(0);
});

test('ignores inactive rules', function () {
    $subject = CurriculumSubject::create([
        'code' => 'CC201',
        'title' => 'Networking 1',
        'units' => 3,
        'category' => 'major',
        'is_active' => true,
    ]);

    EquivalencyRule::create([
        'rule_type' => 'experience',
        'condition_key' => 'years_experience',
        'condition_value' => '5',
        'curriculum_subject_id' => $subject->id,
        'equivalent_credits' => 3,
        'priority' => 0,
        'is_active' => false,
    ]);

    $user = User::factory()->create();
    $user->experienceRecords()->create([
        'company' => 'Acme Corp',
        'position' => 'Network Engineer',
        'start_date' => now()->subYears(6),
        'is_current' => true,
    ]);

    $suggestions = app(EquivalencyEngine::class)->suggestFor($user);

    expect($suggestions)->toHaveCount(0);
});
