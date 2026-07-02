<?php

use App\Models\CurriculumSubject;
use App\Models\User;

test('program head can view the curriculum subjects index', function () {
    $programHead = User::factory()->withRole('program_head')->create();

    $response = $this->actingAs($programHead)->get(route('program-head.curriculum.index'));

    $response->assertOk();
});

test('program head can view the subject creation form', function () {
    $programHead = User::factory()->withRole('program_head')->create();

    $response = $this->actingAs($programHead)->get(route('program-head.curriculum.create'));

    $response->assertOk();
});

test('program head can create a curriculum subject', function () {
    $programHead = User::factory()->withRole('program_head')->create();

    $response = $this->actingAs($programHead)->post(route('program-head.curriculum.store'), [
        'code' => 'CC101',
        'title' => 'Introduction to Computing',
        'units' => 3,
        'category' => 'core',
        'year_level' => 1,
        'semester' => 1,
        'is_active' => true,
    ]);

    $response->assertRedirect(route('program-head.curriculum.index'));
    expect(CurriculumSubject::where('code', 'CC101')->exists())->toBeTrue();
});

test('program head can update a curriculum subject without tripping the unique code rule on itself', function () {
    $programHead = User::factory()->withRole('program_head')->create();
    $subject = CurriculumSubject::create([
        'code' => 'CC101',
        'title' => 'Introduction to Computing',
        'units' => 3,
        'category' => 'core',
        'is_active' => true,
    ]);

    $response = $this->actingAs($programHead)->put(route('program-head.curriculum.update', $subject), [
        'code' => 'CC101',
        'title' => 'Introduction to Computing Systems',
        'units' => 3,
        'category' => 'core',
        'is_active' => true,
    ]);

    $response->assertRedirect(route('program-head.curriculum.index'));
    expect($subject->fresh()->title)->toBe('Introduction to Computing Systems');
});

test('program head can set a new curriculum version', function () {
    $programHead = User::factory()->withRole('program_head')->create();
    CurriculumSubject::create([
        'code' => 'CC101',
        'title' => 'Introduction to Computing',
        'units' => 3,
        'category' => 'core',
        'is_active' => true,
    ]);

    $response = $this->actingAs($programHead)->post(route('program-head.curriculum.version'), [
        'version' => 'CMO-54-2016',
    ]);

    $response->assertRedirect(route('program-head.curriculum.index'));
    expect(CurriculumSubject::first()->curriculum_version)->toBe('CMO-54-2016');
});

test('non program heads cannot manage curriculum subjects', function () {
    $applicant = User::factory()->create();

    $response = $this->actingAs($applicant)->get(route('program-head.curriculum.index'));

    $response->assertForbidden();
});

test('guests can view the public curriculum subjects page', function () {
    CurriculumSubject::create([
        'code' => 'CC101',
        'title' => 'Introduction to Computing',
        'units' => 3,
        'category' => 'core',
        'is_active' => true,
    ]);

    $response = $this->get(route('curriculum.subjects'));

    $response->assertOk();
});
