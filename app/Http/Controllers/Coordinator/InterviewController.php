<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Coordinator\ScheduleInterviewRequest;
use App\Models\Interview;
use App\Services\InterviewService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class InterviewController extends Controller
{
    public function __construct(private readonly InterviewService $interviews) {}

    /**
     * List all F.3 panel interviews.
     */
    public function index(): Response
    {
        $interviews = Interview::query()
            ->with(['application.user:id,name,email', 'panelists:id,name,email'])
            ->latest()
            ->get();

        return Inertia::render('coordinator/interviews/index', [
            'interviews' => $interviews,
        ]);
    }

    /**
     * Schedule an interview that already has a panel assigned.
     */
    public function schedule(ScheduleInterviewRequest $request, Interview $interview): RedirectResponse
    {
        $this->interviews->schedule(
            $interview,
            $request->validated('scheduled_at'),
            $request->validated('location'),
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Interview scheduled.')]);

        return back();
    }
}
