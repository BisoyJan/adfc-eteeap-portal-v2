<?php

namespace App\Http\Controllers\ProgramHead;

use App\Enums\GradeStatus;
use App\Models\CurriculumSubject;
use App\Models\EnrollmentSubject;
use App\Models\EquivalencyMatrix;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController
{
    public function index(Request $request): Response
    {
        $pendingEquivalencies = EquivalencyMatrix::where('type', 'initial')
            ->where('status', 'submitted')
            ->count();

        $advisers = User::whereHas('roles', fn ($q) => $q->where('name', 'adviser'))
            ->withCount(['adviserAssignments' => fn ($q) => $q->where('is_active', true)])
            ->get(['id', 'name', 'email']);

        $maxLoad = (int) config('eteeap.adviser_max_students', 3);
        $overloadedAdvisers = $advisers->filter(fn ($a) => $a->adviser_assignments_count > $maxLoad)->count();

        $pendingGradeApprovals = Grade::where('status', GradeStatus::Submitted)->count();

        $totalSubjects = CurriculumSubject::where('is_active', true)->count();
        $creditedSubjects = EnrollmentSubject::where('status', 'credited')->count();
        $enrolledSubjects = EnrollmentSubject::where('status', 'enrolled')->count();
        $completedSubjects = EnrollmentSubject::where('status', 'completed')->count();

        return Inertia::render('program-head/dashboard', [
            'pendingEquivalencies' => $pendingEquivalencies,
            'advisers' => $advisers,
            'maxLoad' => $maxLoad,
            'overloadedAdvisers' => $overloadedAdvisers,
            'pendingGradeApprovals' => $pendingGradeApprovals,
            'curriculumStats' => [
                'totalSubjects' => $totalSubjects,
                'creditedSubjects' => $creditedSubjects,
                'enrolledSubjects' => $enrolledSubjects,
                'completedSubjects' => $completedSubjects,
            ],
        ]);
    }
}
