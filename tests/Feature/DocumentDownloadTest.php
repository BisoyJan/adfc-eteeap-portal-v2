<?php

use App\Enums\DocumentStatus;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

test('document owner can download', function () {
    Storage::fake('local');

    $user = User::factory()->create();

    $document = $user->documents()->create([
        'document_type' => 'cv',
        'file_path' => 'documents/test.pdf',
        'file_name' => 'test.pdf',
        'file_size' => 100,
        'mime_type' => 'application/pdf',
        'version' => 1,
        'status' => DocumentStatus::Uploaded,
    ]);

    Storage::disk('local')->put('documents/test.pdf', 'fake content');

    $response = $this->actingAs($user)->get(route('documents.download', $document));

    $response->assertOk();
});

test('coordinator can download any document', function () {
    Storage::fake('local');

    $coordinator = User::factory()->withRole('coordinator')->create();
    $applicant = User::factory()->create();

    $document = $applicant->documents()->create([
        'document_type' => 'cv',
        'file_path' => 'documents/test.pdf',
        'file_name' => 'test.pdf',
        'file_size' => 100,
        'mime_type' => 'application/pdf',
        'version' => 1,
        'status' => DocumentStatus::Uploaded,
    ]);

    Storage::disk('local')->put('documents/test.pdf', 'fake content');

    $response = $this->actingAs($coordinator)->get(route('documents.download', $document));

    $response->assertOk();
});

test('unauthorized user cannot download', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();

    $document = $owner->documents()->create([
        'document_type' => 'cv',
        'file_path' => 'documents/test.pdf',
        'file_name' => 'test.pdf',
        'file_size' => 100,
        'mime_type' => 'application/pdf',
        'version' => 1,
        'status' => DocumentStatus::Uploaded,
    ]);

    $response = $this->actingAs($other)->get(route('documents.download', $document));

    $response->assertForbidden();
});
