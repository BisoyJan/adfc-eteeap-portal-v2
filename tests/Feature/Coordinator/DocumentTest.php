<?php

use App\Enums\DocumentStatus;
use App\Models\User;

test('coordinator can verify a document', function () {
    $coordinator = User::factory()->withRole('coordinator')->create();
    $applicant = User::factory()->create();

    $document = $applicant->documents()->create([
        'document_type' => 'cv',
        'file_path' => 'documents/fake.pdf',
        'file_name' => 'fake.pdf',
        'file_size' => 100,
        'mime_type' => 'application/pdf',
        'version' => 1,
        'status' => DocumentStatus::Uploaded,
    ]);

    $response = $this->actingAs($coordinator)->put(route('coordinator.documents.verify', $document), [
        'decision' => 'verify',
    ]);

    $response->assertRedirect();
    expect($document->fresh()->status)->toBe(DocumentStatus::Verified);
});

test('coordinator can reject a document with a reason', function () {
    $coordinator = User::factory()->withRole('coordinator')->create();
    $applicant = User::factory()->create();

    $document = $applicant->documents()->create([
        'document_type' => 'cv',
        'file_path' => 'documents/fake.pdf',
        'file_name' => 'fake.pdf',
        'file_size' => 100,
        'mime_type' => 'application/pdf',
        'version' => 1,
        'status' => DocumentStatus::Uploaded,
    ]);

    $response = $this->actingAs($coordinator)->put(route('coordinator.documents.verify', $document), [
        'decision' => 'reject',
        'rejection_reason' => 'Image is too blurry to read.',
    ]);

    $response->assertRedirect();
    expect($document->fresh()->status)->toBe(DocumentStatus::Rejected);
});
