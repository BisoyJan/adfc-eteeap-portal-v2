<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\EnrollmentService;
use Inertia\Inertia;
use Inertia\Response;

class EnrollmentController extends Controller
{
    public function __construct(private readonly EnrollmentService $enrollments) {}

    public function checklist(): Response
    {
        $checklist = $this->enrollments->checklistFor(request()->user());

        return Inertia::render('student/curriculum-checklist', ['subjects' => $checklist]);
    }
}
