<?php

namespace App\Http\Controllers\Adviser;

use App\Http\Controllers\Controller;
use App\Http\Requests\Adviser\StoreGradeRequest;
use App\Http\Requests\Adviser\UpdateGradeRequest;
use App\Models\EnrollmentSubject;
use App\Models\Grade;
use App\Models\Rubric;
use App\Models\User;
use App\Services\GradeService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class GradeController extends Controller
{
    public function __construct(private readonly GradeService $grades) {}

    public function index(): Response
    {
        $user = request()->user();

        $grades = Grade::query()
            ->where('adviser_id', $user->id)
            ->with('student', 'curriculumSubject', 'rubric')
            ->latest()
            ->get()
            ->map(function (Grade $g) {
                return [
                    'id' => $g->id,
                    'student_name' => $g->student->name,
                    'subject_code' => $g->curriculumSubject->code,
                    'subject_title' => $g->curriculumSubject->title,
                    'grade' => $g->grade,
                    'grade_point' => $g->grade_point,
                    'rubric_title' => $g->rubric?->title,
                    'rubric_score' => $g->rubric_score,
                    'status' => $g->status->value,
                ];
            });

        return Inertia::render('adviser/grades/index', ['grades' => $grades]);
    }

    public function create(): Response
    {
        $user = request()->user();

        $students = User::query()
            ->whereHas('enrollments.subjects', function ($q) {
                $q->where('status', 'enrolled');
            })
            ->whereHas('studentAssignments', function ($q) use ($user) {
                $q->where('adviser_id', $user->id)->where('is_active', true);
            })
            ->with(['enrollments.subjects' => function ($q) {
                $q->where('status', 'enrolled')->with('curriculumSubject');
            }])
            ->get()
            ->map(function (User $s) {
                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'email' => $s->email,
                    'enrolled_subjects' => $s->enrollments->pluck('subjects')->flatten()->map(fn ($es) => [
                        'id' => $es->id,
                        'subject_code' => $es->curriculumSubject->code,
                        'subject_title' => $es->curriculumSubject->title,
                    ]),
                ];
            });

        $rubrics = Rubric::query()
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('is_template', true);
            })
            ->get(['id', 'title']);

        return Inertia::render('adviser/grades/create', [
            'students' => $students,
            'rubrics' => $rubrics,
        ]);
    }

    public function store(StoreGradeRequest $request): RedirectResponse
    {
        /** @var EnrollmentSubject $enrollmentSubject */
        $enrollmentSubject = EnrollmentSubject::with('enrollment')->findOrFail($request->enrollment_subject_id);
        $student = $enrollmentSubject->enrollment->student;

        $this->grades->store(
            $enrollmentSubject->id,
            $request->user(),
            $student,
            $request->only(['grade', 'grade_point', 'rubric_id', 'rubric_score']),
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Grade recorded as draft.']);

        return to_route('adviser.grades.index');
    }

    public function update(UpdateGradeRequest $request, Grade $grade): RedirectResponse
    {
        $this->grades->update($grade, $request->only(['grade', 'grade_point', 'rubric_id', 'rubric_score']));

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Grade updated.']);

        return back();
    }

    public function submit(Grade $grade): RedirectResponse
    {
        $this->grades->submit($grade);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Grade submitted for approval.']);

        return back();
    }
}
