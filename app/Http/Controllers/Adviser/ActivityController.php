<?php

namespace App\Http\Controllers\Adviser;

use App\Enums\ActivityStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Adviser\ScoreActivityRequest;
use App\Http\Requests\Adviser\StoreEnhancementActivityRequest;
use App\Models\EnhancementActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityController extends Controller
{
    /**
     * List enhancement activities assigned by this adviser.
     */
    public function index(Request $request): Response
    {
        $activities = EnhancementActivity::query()
            ->with(['student:id,name,email', 'curriculumSubject:id,code,title'])
            ->where('adviser_id', $request->user()->id)
            ->latest()
            ->get();

        return Inertia::render('adviser/activities/index', [
            'activities' => $activities,
        ]);
    }

    /**
     * Show the activity creation form.
     */
    public function create(Request $request): Response
    {
        $students = $request->user()->activeStudents();

        return Inertia::render('adviser/activities/create', [
            'students' => $students,
        ]);
    }

    /**
     * Assign a new enhancement activity.
     */
    public function store(StoreEnhancementActivityRequest $request): RedirectResponse
    {
        EnhancementActivity::create([
            'student_id' => $request->input('student_id'),
            'adviser_id' => $request->user()->id,
            'curriculum_subject_id' => $request->input('curriculum_subject_id'),
            'activity_type' => $request->input('activity_type'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'due_date' => $request->input('due_date'),
            'status' => ActivityStatus::Assigned,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Activity assigned.')]);

        return to_route('adviser.activities.index');
    }

    /**
     * Show activity detail with submissions.
     */
    public function show(EnhancementActivity $activity): Response
    {
        $activity->load(['student:id,name,email', 'curriculumSubject:id,code,title', 'submissions']);

        return Inertia::render('adviser/activities/show', [
            'activity' => $activity,
        ]);
    }

    /**
     * Update an existing activity (edit).
     */
    public function update(StoreEnhancementActivityRequest $request, EnhancementActivity $activity): RedirectResponse
    {
        $activity->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Activity updated.')]);

        return to_route('adviser.activities.index');
    }

    /**
     * Score a submitted activity.
     */
    public function score(ScoreActivityRequest $request, EnhancementActivity $activity): RedirectResponse
    {
        $activity->update([
            'score' => $request->input('score'),
            'feedback' => $request->input('feedback'),
            'status' => ActivityStatus::Approved,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Activity scored.')]);

        return to_route('adviser.activities.index');
    }

    /**
     * List submitted activities pending review.
     */
    public function pendingSubmissions(Request $request): Response
    {
        $activities = EnhancementActivity::query()
            ->with(['student:id,name,email', 'curriculumSubject:id,code,title', 'submissions'])
            ->where('adviser_id', $request->user()->id)
            ->where('status', ActivityStatus::Submitted)
            ->latest()
            ->get();

        return Inertia::render('adviser/activities/submissions', [
            'activities' => $activities,
        ]);
    }
}
