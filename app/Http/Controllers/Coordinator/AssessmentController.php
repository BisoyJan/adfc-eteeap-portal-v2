<?php

namespace App\Http\Controllers\Coordinator;

use App\Enums\AssessmentStage;
use App\Enums\AssessmentVerdict;
use App\Http\Controllers\Controller;
use App\Http\Requests\Assessment\SubmitAssessmentFormRequest;
use App\Models\Application;
use App\Services\AssessmentService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class AssessmentController extends Controller
{
    public function __construct(private readonly AssessmentService $assessments) {}

    /**
     * Submit the F.1 initial screening assessment form.
     */
    public function submitFirst(SubmitAssessmentFormRequest $request, Application $application): RedirectResponse
    {
        $this->assessments->submit(
            $application,
            AssessmentStage::First,
            $request->user(),
            $request->validated('criteria'),
            AssessmentVerdict::from($request->validated('verdict')),
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('F.1 assessment saved.')]);

        return back();
    }

    /**
     * Submit the F.2 portfolio evaluation assessment form.
     */
    public function submitSecond(SubmitAssessmentFormRequest $request, Application $application): RedirectResponse
    {
        $this->assessments->submit(
            $application,
            AssessmentStage::Second,
            $request->user(),
            $request->validated('criteria'),
            AssessmentVerdict::from($request->validated('verdict')),
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('F.2 assessment saved.')]);

        return back();
    }
}
