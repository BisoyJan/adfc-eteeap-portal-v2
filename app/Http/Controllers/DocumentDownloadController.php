<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentDownloadController extends Controller
{
    public function download(Document $document, Request $request): StreamedResponse
    {
        $this->authorizeAccess($document, $request);

        return Storage::disk(config('eteeap.document.storage_disk'))
            ->download($document->file_path, $document->file_name);
    }

    public function preview(Document $document, Request $request): StreamedResponse
    {
        $this->authorizeAccess($document, $request);

        $mime = $document->mime_type;

        abort_if(! in_array($mime, ['application/pdf', 'image/jpeg', 'image/png'], true), 415);

        return Storage::disk(config('eteeap.document.storage_disk'))
            ->response($document->file_path, $document->file_name, [
                'Content-Disposition' => 'inline; filename="' . $document->file_name . '"',
            ]);
    }

    private function authorizeAccess(Document $document, Request $request): void
    {
        $user = $request->user();

        $isOwner = $document->user_id === $user->id;
        if ($isOwner) {
            return;
        }

        $hasRole = $user->hasRole('coordinator') || $user->hasRole('program_head') || $user->hasRole('qa_officer');
        $isAdviser = $user->hasRole('adviser') && $user->studentAssignments()->where('student_id', $document->user_id)->exists();

        abort_unless($hasRole || $isAdviser, 403);
    }
}
