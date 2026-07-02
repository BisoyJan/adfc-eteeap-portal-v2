<?php

namespace App\Http\Controllers\Vpaa;

use App\Http\Controllers\Controller;
use App\Models\GraduationClearance;
use App\Services\GraduationService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class GraduationController extends Controller
{
    public function __construct(private readonly GraduationService $graduation) {}

    public function pendingApprovals(): Response
    {
        $clearances = GraduationClearance::with('student')
            ->where('status', 'in_review')
            ->latest()
            ->get()
            ->map(function (GraduationClearance $c) {
                return [
                    'id' => $c->id,
                    'student_name' => $c->student->name,
                    'student_email' => $c->student->email,
                    'status' => $c->status->value,
                    'status_label' => $c->status->label(),
                    'created_at' => $c->created_at->format('Y-m-d'),
                    'confirmation_letter' => $c->confirmation_letter_path ? true : false,
                ];
            });

        return Inertia::render('vpaa/approvals/index', [
            'clearances' => $clearances,
        ]);
    }

    public function approve(GraduationClearance $clearance): RedirectResponse
    {
        $this->graduation->approve($clearance, request()->user());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Clearance approved. Diploma and TOR have been generated.']);

        return back();
    }

    public function audit(): Response
    {
        $clearances = GraduationClearance::with('student')
            ->latest()
            ->get()
            ->map(function (GraduationClearance $c) {
                return [
                    'id' => $c->id,
                    'student_name' => $c->student->name,
                    'student_email' => $c->student->email,
                    'status' => $c->status->value,
                    'status_label' => $c->status->label(),
                    'registry_number' => $c->registry_number,
                    'vpaa_approved_at' => $c->vpaa_approved_at?->format('Y-m-d H:i'),
                    'diploma_printed_at' => $c->diploma_printed_at?->format('Y-m-d H:i'),
                    'tor_released_at' => $c->tor_released_at?->format('Y-m-d H:i'),
                    'created_at' => $c->created_at->format('Y-m-d'),
                ];
            });

        return Inertia::render('vpaa/graduation-audit/index', [
            'clearances' => $clearances,
        ]);
    }
}
