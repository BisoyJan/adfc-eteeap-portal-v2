<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Coordinator\VerifyDocumentRequest;
use App\Models\Document;
use App\Services\DocumentService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class DocumentController extends Controller
{
    public function __construct(private readonly DocumentService $documents) {}

    /**
     * Verify or reject an applicant's document.
     */
    public function verify(VerifyDocumentRequest $request, Document $document): RedirectResponse
    {
        $verifier = $request->user();

        if ($request->validated('decision') === 'verify') {
            $this->documents->verify($document, $verifier);
        } else {
            $this->documents->reject($document, $verifier, $request->validated('rejection_reason'));
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Document status updated.')]);

        return back();
    }
}
