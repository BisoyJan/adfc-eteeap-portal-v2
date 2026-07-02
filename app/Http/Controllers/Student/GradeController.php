<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use Inertia\Inertia;
use Inertia\Response;

class GradeController extends Controller
{
    public function index(): Response
    {
        $grades = Grade::query()
            ->where('student_id', request()->user()->id)
            ->with('curriculumSubject', 'adviser')
            ->latest()
            ->get()
            ->map(function (Grade $g) {
                return [
                    'id' => $g->id,
                    'subject_code' => $g->curriculumSubject->code,
                    'subject_title' => $g->curriculumSubject->title,
                    'grade' => $g->grade,
                    'grade_point' => $g->grade_point,
                    'status' => $g->status->value,
                    'adviser_name' => $g->adviser->name,
                    'approved_at' => $g->approved_at?->format('Y-m-d'),
                ];
            });

        return Inertia::render('student/grades/index', ['grades' => $grades]);
    }
}
