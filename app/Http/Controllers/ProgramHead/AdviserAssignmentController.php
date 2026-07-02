<?php

namespace App\Http\Controllers\ProgramHead;

use App\Http\Controllers\Controller;
use App\Http\Requests\Adviser\StoreAdviserAssignmentRequest;
use App\Models\AdviserAssignment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AdviserAssignmentController extends Controller
{
    /**
     * List current adviser assignments.
     */
    public function index(): Response
    {
        $assignments = AdviserAssignment::query()
            ->with(['student:id,name,email', 'adviser:id,name,email', 'assigner:id,name'])
            ->where('is_active', true)
            ->latest()
            ->get();

        return Inertia::render('program-head/adviser-assignments/index', [
            'assignments' => $assignments,
        ]);
    }

    /**
     * Show the assignment form.
     */
    public function create(): Response
    {
        $advisers = User::whereHas('roles', fn ($q) => $q->where('name', 'adviser'))->get(['id', 'name', 'email']);
        $students = User::whereHas('roles', fn ($q) => $q->where('name', 'student'))->get(['id', 'name', 'email']);

        return Inertia::render('program-head/adviser-assignments/create', [
            'advisers' => $advisers,
            'students' => $students,
        ]);
    }

    /**
     * Assign an adviser to a student.
     */
    public function store(StoreAdviserAssignmentRequest $request): RedirectResponse
    {
        $this->enforceAdviserLoad($request->input('adviser_id'));

        AdviserAssignment::create([
            'student_id' => $request->input('student_id'),
            'adviser_id' => $request->input('adviser_id'),
            'term' => $request->input('term'),
            'assigned_by' => $request->user()->id,
            'assigned_at' => now(),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Adviser assigned.')]);

        return to_route('program-head.adviser-assignments.index');
    }

    /**
     * Show adviser workload report.
     */
    public function workload(): Response
    {
        $advisers = User::whereHas('roles', fn ($q) => $q->where('name', 'adviser'))
            ->withCount(['adviserAssignments' => fn ($q) => $q->where('is_active', true)])
            ->get(['id', 'name', 'email']);

        $max = (int) config('eteeap.adviser_max_students', 3);

        return Inertia::render('program-head/adviser-assignments/workload', [
            'advisers' => $advisers,
            'maxStudents' => $max,
        ]);
    }

    /**
     * Enforce the max-students-per-adviser constraint.
     */
    protected function enforceAdviserLoad(int $adviserId): void
    {
        $max = (int) config('eteeap.adviser_max_students', 3);

        $current = AdviserAssignment::query()
            ->where('adviser_id', $adviserId)
            ->where('is_active', true)
            ->count();

        abort_if($current >= $max, 422, __('Adviser already has '.$current.' active students (max '.$max.').'));
    }
}
