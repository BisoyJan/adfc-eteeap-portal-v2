<?php

namespace App\Http\Controllers\QaOfficer;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EnhancementPlanController extends Controller
{
    /**
     * Overview of all active enhancement plans.
     */
    public function overview(Request $request): Response
    {
        $students = User::whereHas('roles', fn ($q) => $q->where('name', 'student'))
            ->withCount(['enhancementActivities as pending_activities' => fn ($q) => $q->whereIn('status', ['assigned', 'in_progress'])]);

        return Inertia::render('qa-officer/enhancement-plan/index', [
            'students' => $students->get(['id', 'name', 'email']),
        ]);
    }
}
