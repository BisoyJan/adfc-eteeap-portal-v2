<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\SubmitClearanceFormRequest;
use App\Services\GraduationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GraduationController extends Controller
{
    public function __construct(private readonly GraduationService $graduation) {}

    public function show(): Response
    {
        $user = request()->user();
        $clearance = $user->graduationClearance;

        if (! $clearance) {
            $clearance = null;
            $documents = [];
            $canDownloadDiploma = false;
            $canDownloadTor = false;
        } else {
            $clearance->load('documents');
            $documents = $clearance->documents->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'document_type' => $doc->document_type,
                    'is_verified' => $doc->is_verified,
                ];
            });

            $canDownloadDiploma = ! is_null($clearance->diploma_printed_at);
            $canDownloadTor = ! is_null($clearance->tor_released_at);
        }

        return Inertia::render('student/graduation/index', [
            'clearance' => $clearance ? [
                'id' => $clearance->id,
                'status' => $clearance->status->value,
                'status_label' => $clearance->status->label(),
                'registry_number' => $clearance->registry_number,
                'vpaa_approved_at' => $clearance->vpaa_approved_at?->format('Y-m-d H:i'),
                'tor_released_at' => $clearance->tor_released_at?->format('Y-m-d H:i'),
                'has_clearance_form' => ! is_null($clearance->clearance_form_path),
                'verification_progress' => sprintf('%d / %d', $clearance->documents->where('is_verified', true)->count(), $clearance->documents->count()),
            ] : null,
            'documents' => $documents,
            'canDownloadDiploma' => $canDownloadDiploma,
            'canDownloadTor' => $canDownloadTor,
        ]);
    }

    public function submitClearance(SubmitClearanceFormRequest $request): RedirectResponse
    {
        $user = $request->user();
        $clearance = $user->graduationClearance;

        if (! $clearance) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'No graduation clearance found.']);

            return back();
        }

        $this->graduation->submitClearanceForm($clearance, $request->file('file'));
        $this->graduation->releaseTor($clearance);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Clearance form submitted and TOR released.']);

        return back();
    }

    public function downloadDiploma(): StreamedResponse|RedirectResponse
    {
        $user = request()->user();
        $clearance = $user->graduationClearance;

        if (! $clearance || ! $clearance->diploma_printed_at) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'Diploma not available for download.']);

            return back();
        }

        $path = sprintf('graduation/%d/clearance/diploma.pdf', $user->id);

        if (! Storage::disk(config('eteeap.document.storage_disk', 'local'))->exists($path)) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'Diploma file not found.']);

            return back();
        }

        return Storage::disk(config('eteeap.document.storage_disk', 'local'))->download($path, 'diploma.pdf');
    }

    public function downloadTor(): StreamedResponse|RedirectResponse
    {
        $user = request()->user();
        $clearance = $user->graduationClearance;

        if (! $clearance || ! $clearance->tor_released_at) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'TOR not available for download.']);

            return back();
        }

        $path = sprintf('graduation/%d/clearance/tor.pdf', $user->id);

        if (! Storage::disk(config('eteeap.document.storage_disk', 'local'))->exists($path)) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'TOR file not found.']);

            return back();
        }

        return Storage::disk(config('eteeap.document.storage_disk', 'local'))->download($path, 'tor.pdf');
    }
}
