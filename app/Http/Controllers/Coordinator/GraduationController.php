<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Coordinator\StoreGraduationClearanceRequest;
use App\Models\ClearanceDocument;
use App\Models\GraduationClearance;
use App\Models\User;
use App\Services\GraduationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GraduationController extends Controller
{
    public function __construct(private readonly GraduationService $graduation) {}

    public function pipeline(): Response
    {
        $eligibleStudents = $this->graduation->eligibleStudents()
            ->map(fn (User $student) => [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
            ]);

        $clearances = GraduationClearance::with('student', 'documents')
            ->latest()
            ->get()
            ->map(function (GraduationClearance $c) {
                $docs = $c->documents;

                return [
                    'id' => $c->id,
                    'student_name' => $c->student->name,
                    'status' => $c->status->value,
                    'status_label' => $c->status->label(),
                    'verified_count' => $docs->where('is_verified', true)->count(),
                    'total_count' => $docs->count(),
                    'created_at' => $c->created_at->format('Y-m-d'),
                ];
            });

        return Inertia::render('coordinator/graduation/index', [
            'eligibleStudents' => $eligibleStudents,
            'clearances' => $clearances,
        ]);
    }

    public function show(GraduationClearance $clearance): Response
    {
        $clearance->load('documents', 'student');

        $documents = $clearance->documents->map(function (ClearanceDocument $doc) {
            return [
                'id' => $doc->id,
                'document_type' => $doc->document_type,
                'is_verified' => $doc->is_verified,
                'verified_by' => $doc->verifier?->name,
                'verified_at' => $doc->verified_at?->format('Y-m-d H:i'),
                'file_path' => $doc->file_path,
            ];
        });

        return Inertia::render('coordinator/graduation/show', [
            'clearance' => [
                'id' => $clearance->id,
                'student_name' => $clearance->student->name,
                'student_email' => $clearance->student->email,
                'status' => $clearance->status->value,
                'status_label' => $clearance->status->label(),
                'registry_number' => $clearance->registry_number,
                'verification_progress' => sprintf('%d / %d', $clearance->documents->where('is_verified', true)->count(), $clearance->documents->count()),
            ],
            'documents' => $documents,
        ]);
    }

    public function store(StoreGraduationClearanceRequest $request): RedirectResponse
    {
        /** @var User $student */
        $student = User::findOrFail($request->student_id);

        $this->graduation->initiate($student, $request->user());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Graduation clearance initiated successfully.']);

        return back();
    }

    public function verifyDocument(Request $request, GraduationClearance $clearance, ClearanceDocument $document): RedirectResponse
    {
        $this->graduation->verifyDocument(
            $document,
            $request->user(),
            $request->file('file'),
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Document verified successfully.']);

        return back();
    }

    public function confirm(Request $request, GraduationClearance $clearance): RedirectResponse
    {
        $this->graduation->confirm($clearance, $request->user());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Clearance confirmed and sent for VPAA review.']);

        return back();
    }
}
