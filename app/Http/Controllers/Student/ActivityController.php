<?php

namespace App\Http\Controllers\Student;

use App\Enums\ActivityStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Student\SubmitActivityRequest;
use App\Models\EnhancementActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityController extends Controller
{
    /**
     * List the student's assigned enhancement activities.
     */
    public function index(Request $request): Response
    {
        $activities = EnhancementActivity::query()
            ->with(['adviser:id,name,email', 'curriculumSubject:id,code,title', 'submissions'])
            ->where('student_id', $request->user()->id)
            ->latest()
            ->get();

        return Inertia::render('student/activities/index', [
            'activities' => $activities,
        ]);
    }

    /**
     * Submit evidence for an activity.
     */
    public function submit(SubmitActivityRequest $request, EnhancementActivity $activity): RedirectResponse
    {
        abort_if($activity->student_id !== $request->user()->id, 403);
        abort_if($activity->status === ActivityStatus::Approved, 422);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('enhancement-submissions/'.$request->user()->id);
        }

        $activity->submissions()->create([
            'file_path' => $filePath,
            'notes' => $request->input('notes'),
            'submitted_at' => now(),
        ]);

        $activity->update(['status' => ActivityStatus::Submitted]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Activity submitted.')]);

        return to_route('student.activities.index');
    }
}
