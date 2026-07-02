<?php

use App\Enums\GraduationClearanceStatus;
use App\Models\GraduationClearance;
use App\Models\User;

test('command anonymizes old graduated students', function () {
    config(['eteeap.retention_years' => 1]);

    $student = User::factory()->withRole('student')->create([
        'name' => 'Original Name',
        'email' => 'original@adfc.edu.ph',
    ]);

    GraduationClearance::create([
        'student_id' => $student->id,
        'status' => GraduationClearanceStatus::Completed,
        'tor_released_at' => now()->subYears(2),
    ]);

    $this->artisan('anonymize:graduated-students')->assertSuccessful();

    expect($student->fresh()->name)->toStartWith('Anonymized Student');
    expect($student->fresh()->email)->toContain('anonymized.');
});

test('command skips recent graduates', function () {
    config(['eteeap.retention_years' => 5]);

    $student = User::factory()->withRole('student')->create([
        'name' => 'Recent Graduate',
        'email' => 'recent@adfc.edu.ph',
    ]);

    GraduationClearance::create([
        'student_id' => $student->id,
        'status' => GraduationClearanceStatus::Completed,
        'tor_released_at' => now()->subYear(),
    ]);

    $this->artisan('anonymize:graduated-students')->assertSuccessful();

    expect($student->fresh()->name)->toBe('Recent Graduate');
});
