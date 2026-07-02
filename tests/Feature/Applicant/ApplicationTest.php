<?php

use App\Enums\ApplicationStatus;
use App\Models\User;

function makeQualifiedApplicant(): User
{
    $user = User::factory()->create();

    $user->applicantProfile()->create([
        'birth_date' => now()->subYears(30),
        'citizenship' => 'Filipino',
        'highest_education' => 'College Graduate',
    ]);

    $user->experienceRecords()->create([
        'company' => 'Acme Corp',
        'position' => 'Software Developer',
        'start_date' => now()->subYears(6),
        'is_current' => true,
    ]);

    return $user;
}

test('applicant can view their application edit page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('applicant.application.edit'));

    $response->assertOk();
    expect($user->applications()->count())->toBe(1);
});

test('applicant can save their profile information', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->put(route('applicant.application.update'), [
        'birth_date' => '1995-01-01',
        'citizenship' => 'Filipino',
        'highest_education' => 'College Graduate',
    ]);

    $response->assertRedirect(route('applicant.application.edit'));
    expect($user->applicantProfile()->first())->not->toBeNull();
});

test('an unqualified applicant cannot submit their application', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('applicant.application.submit'));

    $response->assertSessionHasErrors('qualification');
    expect($user->applications()->first()->status)->toBe(ApplicationStatus::Draft);
});

test('a qualified applicant can submit their application', function () {
    $user = makeQualifiedApplicant();

    $response = $this->actingAs($user)->post(route('applicant.application.submit'));

    $response->assertRedirect(route('applicant.application.edit'));
    expect($user->applications()->first()->status)->toBe(ApplicationStatus::Submitted);
});
