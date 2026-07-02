<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Applicant\StoreDocumentRequest;
use App\Models\Document;
use App\Services\ApplicationService;
use App\Services\DocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DocumentController extends Controller
{
    public function __construct(
        private readonly DocumentService $documents,
        private readonly ApplicationService $applications,
    ) {}

    /**
     * Show the applicant's document checklist.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('applicant/documents/index', [
            'checklist' => $this->documents->checklist($request->user()),
            'progress' => $this->documents->progress($request->user()),
        ]);
    }

    /**
     * Upload a document against the current application.
     */
    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        $application = $this->applications->currentDraftFor($request->user());

        $this->documents->store(
            $request->user(),
            $application,
            $request->validated('document_type'),
            $request->file('file'),
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Document uploaded.')]);

        return back();
    }

    /**
     * Delete an unverified document belonging to the authenticated applicant.
     */
    public function destroy(Request $request, Document $document): RedirectResponse
    {
        abort_unless($document->user_id === $request->user()->id, 403);
        abort_if($document->status->value === 'verified', 403, 'Verified documents cannot be deleted.');

        $document->delete();

        return back();
    }
}
