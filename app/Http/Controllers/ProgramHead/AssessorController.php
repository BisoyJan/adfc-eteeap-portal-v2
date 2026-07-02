<?php

namespace App\Http\Controllers\ProgramHead;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProgramHead\AssignPanelRequest;
use App\Models\Application;
use App\Models\User;
use App\Services\InterviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AssessorController extends Controller
{
    public function __construct(private readonly InterviewService $interviews) {}

    /**
     * List assessors available to be assigned to a panel.
     */
    public function available(): Response
    {
        $assessors = User::query()
            ->whereHas('roles', fn ($query) => $query->whereIn('name', ['assessor_internal', 'assessor_external']))
            ->with('roles:id,name')
            ->get(['id', 'name', 'email']);

        return Inertia::render('program-head/assessors/index', [
            'assessors' => $assessors,
        ]);
    }

    /**
     * Show the panel assignment form for an application.
     */
    public function panelForm(Application $application): Response
    {
        $assessors = User::query()
            ->whereHas('roles', fn ($query) => $query->whereIn('name', ['assessor_internal', 'assessor_external']))
            ->with('roles:id,name')
            ->get(['id', 'name', 'email']);

        return Inertia::render('program-head/assessors/panel', [
            'application' => $application->load('user:id,name,email'),
            'assessors' => $assessors,
            'interview' => $application->interviews()->with('panelists:id,name,email')->first(),
        ]);
    }

    /**
     * Assign the assessment panel for an application's F.3 interview.
     */
    public function assignPanel(AssignPanelRequest $request, Application $application): RedirectResponse
    {
        try {
            $this->interviews->assignPanel($application, $request->user(), $request->validated('panelist_ids'));
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Assessment panel assigned.')]);

        return back();
    }
}
