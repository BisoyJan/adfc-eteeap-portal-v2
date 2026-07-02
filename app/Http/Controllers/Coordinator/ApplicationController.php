<?php

namespace App\Http\Controllers\Coordinator;

use App\Enums\ApplicationStatus;
use App\Enums\AssessmentStage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Coordinator\ReviewApplicationRequest;
use App\Models\Application;
use App\Services\ApplicationService;
use App\Services\DocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ApplicationController extends Controller
{
    public function __construct(private readonly ApplicationService $applications) {}

    /**
     * Show the application kanban board, grouped by status.
     */
    public function index(Request $request): Response
    {
        $applications = Application::query()
            ->with('user:id,name,email')
            ->latest()
            ->get()
            ->groupBy(fn (Application $application) => $application->status->value);

        $columns = collect(ApplicationStatus::cases())->map(fn (ApplicationStatus $status) => [
            'status' => $status->value,
            'label' => $status->label(),
            'applications' => $applications->get($status->value, collect())->values(),
        ])->values();

        return Inertia::render('coordinator/applications/index', [
            'columns' => $columns,
        ]);
    }

    /**
     * Show a single application's full detail for review.
     */
    public function show(Application $application, DocumentService $documents): Response
    {
        $application->load(['user:id,name,email', 'reviewer:id,name', 'user.applicantProfile', 'user.experienceRecords', 'user.educationRecords']);

        return Inertia::render('coordinator/applications/show', [
            'application' => $application,
            'checklist' => $documents->checklist($application->user),
            'progress' => $documents->progress($application->user),
            'firstAssessment' => $application->assessmentForms()->where('stage', AssessmentStage::First)->first(),
            'secondAssessment' => $application->assessmentForms()->where('stage', AssessmentStage::Second)->first(),
        ]);
    }

    /**
     * Review an application: approve, reject, or request revisions.
     */
    public function review(ReviewApplicationRequest $request, Application $application): RedirectResponse
    {
        $reviewer = $request->user();

        /** @var string $decision */
        $decision = $request->validated('decision');

        match ($decision) {
            'approve' => $this->applications->approve($application, $reviewer, $request->validated('notes')),
            'reject' => $this->applications->reject($application, $reviewer, $request->validated('rejection_reason')),
            'request_revision' => $this->applications->requestRevision($application, $reviewer, $request->validated('notes') ?? ''),
            default => abort(422, 'Invalid decision.'),
        };

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Application updated.')]);

        return to_route('coordinator.applications.show', $application);
    }
}
