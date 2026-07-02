<?php

namespace App\Http\Controllers\Student;

use App\Models\EnhancementActivity;
use App\Models\Grade;
use App\Models\GraduationClearance;
use App\Services\EnrollmentService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController
{
    public function __construct(
        protected EnrollmentService $enrollmentService,
    ) {}

    public function index(Request $request): Response
    {
        $student = $request->user();

        $checklist = $this->enrollmentService->checklistFor($student);
        $totalSubjects = $checklist->count();
        $completedSubjects = $checklist->whereIn('status', ['completed', 'credited'])->count();
        $progressPercentage = $totalSubjects > 0 ? round(($completedSubjects / $totalSubjects) * 100) : 0;

        $upcomingDeadlines = EnhancementActivity::where('student_id', $student->id)
            ->whereIn('status', ['assigned', 'in_progress'])
            ->whereNotNull('due_date')
            ->orderBy('due_date')
            ->limit(5)
            ->get()
            ->map(fn ($a) => [
                'id' => $a->id,
                'title' => $a->title,
                'due_date' => $a->due_date->format('M j, Y'),
                'status' => $a->status->label(),
            ]);

        $recentGrades = Grade::where('student_id', $student->id)
            ->with('curriculumSubject')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn ($g) => [
                'id' => $g->id,
                'subject' => $g->curriculumSubject->title ?? 'N/A',
                'grade' => $g->grade,
                'status' => $g->status->label(),
            ]);

        $clearance = GraduationClearance::where('student_id', $student->id)->first();

        return Inertia::render('student/dashboard', [
            'progressPercentage' => $progressPercentage,
            'totalSubjects' => $totalSubjects,
            'completedSubjects' => $completedSubjects,
            'upcomingDeadlines' => $upcomingDeadlines,
            'recentGrades' => $recentGrades,
            'clearance' => $clearance ? [
                'id' => $clearance->id,
                'status' => $clearance->status->label(),
                'registry_number' => $clearance->registry_number,
            ] : null,
        ]);
    }
}
