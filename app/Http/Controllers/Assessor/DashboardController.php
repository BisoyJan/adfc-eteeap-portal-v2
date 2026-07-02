<?php

namespace App\Http\Controllers\Assessor;

use App\Models\Interview;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController
{
    public function index(Request $request): Response
    {
        $assessor = $request->user();

        $assignedInterviewsCount = $assessor->panelInterviews()
            ->where('status', 'scheduled')
            ->count();

        $upcomingInterviews = $assessor->panelInterviews()
            ->where('status', 'scheduled')
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->limit(3)
            ->get()
            ->map(fn ($i) => [
                'id' => $i->id,
                'scheduled_at' => $i->scheduled_at->format('M j, Y g:i A'),
                'location' => $i->location,
                'applicant' => $i->application->user->name ?? 'N/A',
            ]);

        return Inertia::render('assessor/dashboard', [
            'assignedInterviewsCount' => $assignedInterviewsCount,
            'upcomingInterviews' => $upcomingInterviews,
        ]);
    }
}
