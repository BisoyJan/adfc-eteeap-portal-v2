<?php

namespace App\Http\Controllers\Coordinator;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\Interview;
use App\Models\Notification;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController
{
    public function index(Request $request): Response
    {
        $pipeline = collect(ApplicationStatus::cases())->map(fn ($status) => [
            'name' => $status->label(),
            'value' => Application::where('status', $status)->count(),
        ]);

        $pendingReviews = Application::whereIn('status', [
            ApplicationStatus::Submitted,
            ApplicationStatus::UnderReview,
        ])->count();

        $upcomingInterviews = Interview::where('status', 'scheduled')
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get()
            ->map(fn ($i) => [
                'id' => $i->id,
                'applicant' => $i->application->user->name ?? 'N/A',
                'scheduled_at' => $i->scheduled_at->format('M j, Y g:i A'),
                'location' => $i->location,
            ]);

        $totalApplicants = User::whereHas('roles', fn ($q) => $q->where('name', 'applicant'))->count();
        $totalStudents = User::whereHas('roles', fn ($q) => $q->where('name', 'student'))->count();
        $totalEnrolled = Application::where('status', ApplicationStatus::Approved)->count();

        $coordinatorIds = Role::where('name', 'coordinator')->first()?->users()->pluck('users.id');
        $recentNotifications = Notification::whereIn('user_id', $coordinatorIds ?? [])
            ->latest()
            ->limit(10)
            ->get(['id', 'title', 'body', 'created_at']);

        return Inertia::render('coordinator/dashboard', [
            'pipeline' => $pipeline,
            'pendingReviews' => $pendingReviews,
            'upcomingInterviews' => $upcomingInterviews,
            'totalApplicants' => $totalApplicants,
            'totalStudents' => $totalStudents,
            'totalEnrolled' => $totalEnrolled,
            'recentNotifications' => $recentNotifications,
        ]);
    }
}
