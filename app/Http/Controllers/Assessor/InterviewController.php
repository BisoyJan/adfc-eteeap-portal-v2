<?php

namespace App\Http\Controllers\Assessor;

use App\Enums\AssessmentStage;
use App\Enums\AssessmentVerdict;
use App\Http\Controllers\Controller;
use App\Http\Requests\Assessment\SubmitAssessmentFormRequest;
use App\Models\Interview;
use App\Services\AssessmentService;
use App\Services\DocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InterviewController extends Controller
{
    public function __construct(private readonly AssessmentService $assessments) {}

    /**
     * List interviews the authenticated assessor is assigned to.
     */
    public function index(Request $request): Response
    {
        $interviews = $request->user()->panelInterviews()
            ->with('application.user:id,name,email')
            ->latest()
            ->get();

        return Inertia::render('assessor/interviews/index', [
            'interviews' => $interviews,
        ]);
    }

    /**
     * Show an interview's application detail for the assessor to review.
     */
    public function show(Request $request, Interview $interview, DocumentService $documents): Response
    {
        abort_unless($interview->panelists->contains('id', $request->user()->id), 403);

        $interview->load(['application.user:id,name,email']);

        return Inertia::render('assessor/interviews/show', [
            'interview' => $interview,
            'checklist' => $documents->checklist($interview->application->user),
            'myValidation' => $interview->application->assessmentForms()
                ->where('stage', AssessmentStage::ThirdValidation)
                ->where('assessor_id', $request->user()->id)
                ->first(),
            'myOral' => $interview->application->assessmentForms()
                ->where('stage', AssessmentStage::ThirdOral)
                ->where('assessor_id', $request->user()->id)
                ->first(),
        ]);
    }

    /**
     * Submit the F.3 panel validation tool.
     */
    public function submitValidation(SubmitAssessmentFormRequest $request, Interview $interview): RedirectResponse
    {
        abort_unless($interview->panelists->contains('id', $request->user()->id), 403);

        $this->assessments->submit(
            $interview->application,
            AssessmentStage::ThirdValidation,
            $request->user(),
            $request->validated('criteria'),
            AssessmentVerdict::from($request->validated('verdict')),
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Validation tool submitted.')]);

        return back();
    }

    /**
     * Submit the F.3 oral competency form.
     */
    public function submitOral(SubmitAssessmentFormRequest $request, Interview $interview): RedirectResponse
    {
        abort_unless($interview->panelists->contains('id', $request->user()->id), 403);

        $this->assessments->submit(
            $interview->application,
            AssessmentStage::ThirdOral,
            $request->user(),
            $request->validated('criteria'),
            AssessmentVerdict::from($request->validated('verdict')),
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Oral competency form submitted.')]);

        return back();
    }
}
