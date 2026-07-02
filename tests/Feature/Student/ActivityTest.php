<?php

use App\Models\EnhancementActivity;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('student can view their activities', function () {
    $student = User::factory()->withRole('student')->create();

    $response = $this->actingAs($student)->get(route('student.activities.index'));

    $response->assertOk();
});

test('student can submit evidence for their own activity', function () {
    Storage::fake('local');

    $adviser = User::factory()->withRole('adviser')->create();
    $student = User::factory()->withRole('student')->create();
    $activity = EnhancementActivity::create([
        'student_id' => $student->id,
        'adviser_id' => $adviser->id,
        'activity_type' => 'modular_study',
        'title' => 'Complete Module 1',
        'status' => 'assigned',
    ]);

    $response = $this->actingAs($student)->post(route('student.activities.submit', $activity), [
        'notes' => 'Attached my summary.',
        'file' => UploadedFile::fake()->create('summary.pdf', 100, 'application/pdf'),
    ]);

    $response->assertRedirect(route('student.activities.index'));
    $activity->refresh();
    expect($activity->status->value)->toBe('submitted');
    expect($activity->submissions()->count())->toBe(1);
});

test('student cannot submit evidence for another student\'s activity', function () {
    $adviser = User::factory()->withRole('adviser')->create();
    $owner = User::factory()->withRole('student')->create();
    $intruder = User::factory()->withRole('student')->create();

    $activity = EnhancementActivity::create([
        'student_id' => $owner->id,
        'adviser_id' => $adviser->id,
        'activity_type' => 'modular_study',
        'title' => 'Complete Module 1',
        'status' => 'assigned',
    ]);

    $response = $this->actingAs($intruder)->post(route('student.activities.submit', $activity), [
        'notes' => 'Trying to submit on someone else\'s behalf.',
    ]);

    $response->assertForbidden();
});
