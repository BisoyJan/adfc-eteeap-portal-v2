<?php

namespace App\Http\Controllers\Adviser;

use App\Enums\ActivityStatus;
use App\Models\EnhancementActivity;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController
{
    public function index(Request $request): Response
    {
        $adviser = $request->user();

        $myAdviseesCount = $adviser->adviserAssignments()
            ->where('is_active', true)
            ->count();

        $pendingSubmissions = EnhancementActivity::where('adviser_id', $adviser->id)
            ->where('status', ActivityStatus::Submitted)
            ->count();

        $upcomingDeadlines = EnhancementActivity::where('adviser_id', $adviser->id)
            ->whereIn('status', [ActivityStatus::Assigned, ActivityStatus::InProgress])
            ->whereNotNull('due_date')
            ->orderBy('due_date')
            ->limit(5)
            ->get()
            ->map(fn ($a) => [
                'id' => $a->id,
                'title' => $a->title,
                'student' => $a->student->name ?? 'N/A',
                'due_date' => $a->due_date?->format('M j, Y'),
                'status' => $a->status->label(),
            ]);

        $students = $adviser->adviserAssignments()
            ->where('is_active', true)
            ->with('student')
            ->get()
            ->pluck('student');

        $progress = $students->map(fn ($student) => [
            'student_id' => $student->id,
            'name' => $student->name,
            'total' => EnhancementActivity::where('student_id', $student->id)
                ->where('adviser_id', $adviser->id)
                ->count(),
            'completed' => EnhancementActivity::where('student_id', $student->id)
                ->where('adviser_id', $adviser->id)
                ->whereIn('status', [ActivityStatus::Approved])
                ->count(),
        ])->map(fn ($p) => [
            ...$p,
            'percentage' => $p['total'] > 0 ? round(($p['completed'] / $p['total']) * 100) : 0,
        ]);

        return Inertia::render('adviser/dashboard', [
            'myAdviseesCount' => $myAdviseesCount,
            'pendingSubmissions' => $pendingSubmissions,
            'upcomingDeadlines' => $upcomingDeadlines,
            'studentProgress' => $progress,
        ]);
    }
}
