<?php

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\User;
use Illuminate\Support\Facades\DB;

test('command purges old draft applications', function () {
    config(['eteeap.inactive_purge_months' => 12]);

    $user = User::factory()->create();

    $application = $user->applications()->create([
        'application_number' => 'APP-OLD-001',
        'status' => ApplicationStatus::Draft,
    ]);

    Application::where('id', $application->id)->update(['updated_at' => now()->subMonths(13)]);

    $this->artisan('applications:purge-incomplete')->assertSuccessful();

    expect(Application::find($application->id))->toBeNull();
});

test('command skips recent drafts', function () {
    config(['eteeap.inactive_purge_months' => 12]);

    $user = User::factory()->create();

    $application = $user->applications()->create([
        'application_number' => 'APP-RECENT-001',
        'status' => ApplicationStatus::Draft,
        'updated_at' => now()->subMonth(),
    ]);

    $this->artisan('applications:purge-incomplete')->assertSuccessful();

    expect(Application::find($application->id))->not->toBeNull();
});
