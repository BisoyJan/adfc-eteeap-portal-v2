<?php

use App\Enums\DocumentStatus;
use App\Enums\PortfolioStatus;
use App\Models\User;

test('portfolio submission is blocked until required documents are verified', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('applicant.portfolio.submit'));

    $response->assertSessionHasErrors('portfolio');
});

test('applicant can submit their portfolio once all required documents are verified', function () {
    $user = User::factory()->create();

    foreach (config('eteeap.document_types') as $type) {
        if (! $type['required']) {
            continue;
        }

        $user->documents()->create([
            'document_type' => $type['key'],
            'file_path' => 'documents/fake.pdf',
            'file_name' => 'fake.pdf',
            'file_size' => 100,
            'mime_type' => 'application/pdf',
            'version' => 1,
            'status' => DocumentStatus::Verified,
            'verified_at' => now(),
        ]);
    }

    $response = $this->actingAs($user)->post(route('applicant.portfolio.submit'));

    $response->assertRedirect(route('applicant.portfolio.show'));
    expect($user->applications()->first()->portfolio->status)->toBe(PortfolioStatus::Submitted);
});

test('applicant can view their portfolio page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('applicant.portfolio.show'));

    $response->assertOk();
});
