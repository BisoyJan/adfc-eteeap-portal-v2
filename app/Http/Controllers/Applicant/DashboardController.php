<?php

namespace App\Http\Controllers\Applicant;

use App\Services\DocumentService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController
{
    public function __construct(
        protected DocumentService $documentService,
    ) {}

    public function index(Request $request): Response
    {
        $user = $request->user();

        $application = $user->applications()
            ->with('portfolio')
            ->latest()
            ->first();

        $documentProgress = $this->documentService->progress($user);

        return Inertia::render('applicant/dashboard', [
            'application' => $application ? [
                'id' => $application->id,
                'application_number' => $application->application_number,
                'status' => $application->status->value,
                'status_label' => $application->status->label(),
                'submitted_at' => $application->submitted_at?->format('M j, Y'),
            ] : null,
            'documentProgress' => $documentProgress,
            'portfolio' => $application?->portfolio ? [
                'id' => $application->portfolio->id,
                'status' => $application->portfolio->status->label(),
                'status_value' => $application->portfolio->status->value,
            ] : null,
        ]);
    }
}
