<?php

namespace App\Http\Controllers\ProgramHead;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Services\GradeService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class GradeController extends Controller
{
    public function __construct(private readonly GradeService $grades) {}

    public function pending(): Response
    {
        $grades = Grade::query()
            ->where('status', 'submitted')
            ->with('student', 'curriculumSubject', 'adviser')
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
                    'adviser_name' => $g->adviser->name,
                    'rubric_score' => $g->rubric_score,
                    'created_at' => $g->created_at->format('Y-m-d'),
                ];
            });

        return Inertia::render('program-head/grades/index', ['grades' => $grades]);
    }

    public function approve(Grade $grade): RedirectResponse
    {
        $this->grades->approve($grade, request()->user());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Grade approved successfully.']);

        return back();
    }
}
