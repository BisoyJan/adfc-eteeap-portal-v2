<?php

use App\Models\Rubric;
use App\Models\User;

test('adviser can view the rubrics index', function () {
    $adviser = User::factory()->withRole('adviser')->create();

    $response = $this->actingAs($adviser)->get(route('adviser.rubrics.index'));

    $response->assertOk();
});

test('adviser can view the rubric creation form', function () {
    $adviser = User::factory()->withRole('adviser')->create();

    $response = $this->actingAs($adviser)->get(route('adviser.rubrics.create'));

    $response->assertOk();
});

test('adviser can create a rubric with a computed max score', function () {
    $adviser = User::factory()->withRole('adviser')->create();

    $response = $this->actingAs($adviser)->post(route('adviser.rubrics.store'), [
        'title' => 'Case Analysis Rubric',
        'is_template' => false,
        'criteria' => [
            [
                'name' => 'Content Quality',
                'weight' => 60,
                'levels' => [
                    ['score' => 4, 'description' => 'Excellent'],
                    ['score' => 2, 'description' => 'Needs improvement'],
                ],
            ],
            [
                'name' => 'Presentation',
                'weight' => 40,
                'levels' => [
                    ['score' => 4, 'description' => 'Excellent'],
                    ['score' => 2, 'description' => 'Needs improvement'],
                ],
            ],
        ],
    ]);

    $response->assertRedirect(route('adviser.rubrics.index'));

    $rubric = Rubric::where('title', 'Case Analysis Rubric')->first();
    expect($rubric)->not->toBeNull();
    // (4 * 0.6) + (4 * 0.4) = 4.0
    expect((float) $rubric->max_score)->toBe(4.0);
});
