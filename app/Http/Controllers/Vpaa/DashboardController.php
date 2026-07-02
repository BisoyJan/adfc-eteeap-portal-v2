<?php

namespace App\Http\Controllers\Vpaa;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\GraduationClearance;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController
{
    public function index(Request $request): Response
    {
        $totalApplicants = User::whereHas('roles', fn ($q) => $q->where('name', 'applicant'))->count();
        $activeStudents = User::whereHas('roles', fn ($q) => $q->where('name', 'student'))->count();
        $totalGraduates = GraduationClearance::where('status', 'completed')->count();

        $pendingApprovals = GraduationClearance::where('status', 'in_review')->count();

        $auditSummary = [
            'total' => GraduationClearance::count(),
            'pending' => GraduationClearance::where('status', 'pending')->count(),
            'in_review' => GraduationClearance::where('status', 'in_review')->count(),
            'approved' => GraduationClearance::where('status', 'approved')->count(),
            'completed' => $totalGraduates,
        ];

        $submittedApps = Application::where('status', ApplicationStatus::Submitted)->first()?->created_at;
        $approvedApps = Application::where('status', ApplicationStatus::Approved)->first()?->created_at;
        $avgDays = null;
        if ($submittedApps && $approvedApps) {
            $avgDays = $submittedApps->diffInDays($approvedApps);
        }

        return Inertia::render('vpaa/dashboard', [
            'programOverview' => [
                'totalApplicants' => $totalApplicants,
                'activeStudents' => $activeStudents,
                'totalGraduates' => $totalGraduates,
            ],
            'pendingApprovals' => $pendingApprovals,
            'auditSummary' => $auditSummary,
            'avgProcessingDays' => $avgDays,
        ]);
    }
}
