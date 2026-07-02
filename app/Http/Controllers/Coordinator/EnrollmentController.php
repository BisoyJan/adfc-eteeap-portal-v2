<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Coordinator\StoreEnrollmentRequest;
use App\Models\Application;
use App\Models\Enrollment;
use App\Services\EnrollmentService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class EnrollmentController extends Controller
{
    public function __construct(private readonly EnrollmentService $enrollments) {}

    public function index(): Response
    {
        $eligibleApplications = $this->enrollments->eligibleApplications()
            ->map(fn ($app) => [
                'id' => $app->id,
                'application_number' => $app->application_number,
                'applicant_name' => $app->user->name,
                'applicant_email' => $app->user->email,
            ]);

        $enrollments = Enrollment::with('student', 'subjects')
            ->latest()
            ->get()
            ->map(function (Enrollment $e) {
                $subjects = $e->subjects;

                return [
                    'id' => $e->id,
                    'student_id_number' => $e->student_id_number,
                    'student_name' => $e->student->name,
                    'academic_term' => $e->academic_term,
                    'enrollment_date' => $e->enrollment_date->format('Y-m-d'),
                    'status' => $e->status->value,
                    'credited_count' => $subjects->where('status', 'credited')->count(),
                    'enrolled_count' => $subjects->where('status', 'enrolled')->count(),
                    'pending_count' => $subjects->where('status', 'pending')->count(),
                    'completed_count' => $subjects->where('status', 'completed')->count(),
                ];
            });

        return Inertia::render('coordinator/enrollments/index', [
            'eligibleApplications' => $eligibleApplications,
            'enrollments' => $enrollments,
        ]);
    }

    public function store(StoreEnrollmentRequest $request): RedirectResponse
    {
        /** @var Application $application */
        $application = Application::findOrFail($request->application_id);

        $this->enrollments->createFromApplication(
            $application,
            $request->user(),
            $request->academic_term,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Student enrolled successfully.']);

        return back();
    }
}
