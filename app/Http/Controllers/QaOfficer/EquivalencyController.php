<?php

namespace App\Http\Controllers\QaOfficer;

use App\Enums\EquivalencyMatrixType;
use App\Enums\InterviewStatus;
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
     * List applications whose panel interview is complete and awaiting a final matrix.
     */
    public function pending(): Response
    {
        $applications = Application::query()
            ->with('user:id,name,email')
            ->whereHas('interviews', fn ($query) => $query->where('status', InterviewStatus::Completed))
            ->latest()
            ->get();

        return Inertia::render('qa-officer/equivalency/index', [
            'applications' => $applications,
        ]);
    }

    /**
     * Show the final equivalency matrix editor for an application.
     */
    public function show(Request $request, Application $application): Response
    {
        $initialMatrix = $this->equivalency->forApplication($application, EquivalencyMatrixType::Initial, $request->user())->load('items');
        $finalMatrix = $this->equivalency->forApplication($application, EquivalencyMatrixType::Final, $request->user())->load('items');

        return Inertia::render('qa-officer/equivalency/show', [
            'application' => $application->load('user:id,name,email'),
            'initialMatrix' => $initialMatrix,
            'finalMatrix' => $finalMatrix,
        ]);
    }

    /**
     * Save the final equivalency matrix's line items.
     */
    public function store(SaveEquivalencyItemsRequest $request, Application $application): RedirectResponse
    {
        $matrix = $this->equivalency->forApplication($application, EquivalencyMatrixType::Final, $request->user());

        $this->equivalency->saveItems($matrix, $request->validated('items'));

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Final equivalency matrix saved.')]);

        return back();
    }

    /**
     * Approve the final equivalency matrix.
     */
    public function approve(Request $request, Application $application): RedirectResponse
    {
        $matrix = $this->equivalency->forApplication($application, EquivalencyMatrixType::Final, $request->user());

        $this->equivalency->approve($matrix, $request->user());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Final equivalency matrix approved.')]);

        return to_route('qa-officer.equivalency.show', $application);
    }
}
