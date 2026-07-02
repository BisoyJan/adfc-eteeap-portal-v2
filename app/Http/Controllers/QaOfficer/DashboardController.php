<?php

namespace App\Http\Controllers\QaOfficer;

use App\Enums\AssessmentStage;
use App\Models\AssessmentForm;
use App\Models\EnhancementActivity;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController
{
    public function index(Request $request): Response
    {
        $pendingF3Validations = AssessmentForm::whereIn('stage', [
            AssessmentStage::ThirdValidation,
            AssessmentStage::ThirdOral,
        ])->count();

        $enhancementOverview = [
            'total' => EnhancementActivity::count(),
            'assigned' => EnhancementActivity::where('status', 'assigned')->count(),
            'in_progress' => EnhancementActivity::where('status', 'in_progress')->count(),
            'submitted' => EnhancementActivity::where('status', 'submitted')->count(),
            'approved' => EnhancementActivity::where('status', 'approved')->count(),
        ];

        $recentSubmissions = EnhancementActivity::where('status', 'submitted')
            ->with('student', 'adviser')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn ($a) => [
                'id' => $a->id,
                'title' => $a->title,
                'student' => $a->student->name ?? 'N/A',
                'adviser' => $a->adviser->name ?? 'N/A',
                'updated_at' => $a->updated_at->format('M j, Y'),
            ]);

        return Inertia::render('qa-officer/dashboard', [
            'pendingF3Validations' => $pendingF3Validations,
            'enhancementOverview' => $enhancementOverview,
            'recentSubmissions' => $recentSubmissions,
        ]);
    }
}
