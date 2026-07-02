<?php

use App\Enums\DocumentStatus;
use App\Models\User;
use App\Services\DocumentService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
});

test('applicant can upload a document', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf');

    $response = $this->actingAs($user)->post(route('applicant.documents.store'), [
        'document_type' => 'cv',
        'file' => $file,
    ]);

    $response->assertRedirect();
    expect($user->documents()->count())->toBe(1);
    expect($user->documents()->first()->status)->toBe(DocumentStatus::Uploaded);
});

test('applicant cannot upload an unsupported file type', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('resume.exe', 100, 'application/x-msdownload');

    $response = $this->actingAs($user)->post(route('applicant.documents.store'), [
        'document_type' => 'cv',
        'file' => $file,
    ]);

    $response->assertSessionHasErrors('file');
    expect($user->documents()->count())->toBe(0);
});

test('applicant can view their document checklist', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('applicant.documents.index'));

    $response->assertOk();
});

test('applicant can delete an unverified document', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf');

    $this->actingAs($user)->post(route('applicant.documents.store'), [
        'document_type' => 'cv',
        'file' => $file,
    ]);

    $document = $user->documents()->first();

    $response = $this->actingAs($user)->delete(route('applicant.documents.destroy', $document));

    $response->assertRedirect();
    expect($user->documents()->count())->toBe(0);
});

test('applicant cannot delete a verified document', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf');

    $this->actingAs($user)->post(route('applicant.documents.store'), [
        'document_type' => 'cv',
        'file' => $file,
    ]);

    $document = $user->documents()->first();
    app(DocumentService::class)->verify($document, User::factory()->withRole('coordinator')->create());

    $response = $this->actingAs($user)->delete(route('applicant.documents.destroy', $document));

    $response->assertForbidden();
    expect($user->documents()->count())->toBe(1);
});
