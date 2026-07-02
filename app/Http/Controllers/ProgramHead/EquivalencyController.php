<?php

namespace App\Http\Controllers\ProgramHead;

use App\Enums\ApplicationStatus;
use App\Enums\EquivalencyMatrixType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Equivalency\SaveEquivalencyItemsRequest;
use App\Models\Application;
use App\Services\EquivalencyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EquivalencyController extends Controller
{
    public function __construct(private readonly EquivalencyService $equivalency) {}

    /**
     * List applications awaiting an initial equivalency matrix.
     */
    public function pending(): Response
    {
        $applications = Application::query()
            ->with('user:id,name,email')
            ->whereIn('status', [ApplicationStatus::Submitted, ApplicationStatus::UnderReview])
            ->latest()
            ->get();

        return Inertia::render('program-head/equivalency/index', [
            'applications' => $applications,
        ]);
    }

    /**
     * Show the initial equivalency matrix editor for an application.
     */
    public function show(Request $request, Application $application): Response
    {
        $matrix = $this->equivalency->forApplication($application, EquivalencyMatrixType::Initial, $request->user());

        return Inertia::render('program-head/equivalency/show', [
            'application' => $application->load('user:id,name,email'),
            'matrix' => $matrix->load('items'),
        ]);
    }

    /**
     * Save the initial equivalency matrix's line items.
     */
    public function store(SaveEquivalencyItemsRequest $request, Application $application): RedirectResponse
    {
        $matrix = $this->equivalency->forApplication($application, EquivalencyMatrixType::Initial, $request->user());

        $this->equivalency->saveItems($matrix, $request->validated('items'));

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Equivalency matrix saved.')]);

        return back();
    }

    /**
     * Submit the initial equivalency matrix.
     */
    public function submit(Request $request, Application $application): RedirectResponse
    {
        $matrix = $this->equivalency->forApplication($application, EquivalencyMatrixType::Initial, $request->user());

        $this->equivalency->submit($matrix);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Equivalency matrix submitted.')]);

        return to_route('program-head.equivalency.show', $application);
    }
}
