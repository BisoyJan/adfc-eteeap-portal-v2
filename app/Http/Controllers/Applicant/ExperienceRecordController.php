<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Applicant\ExperienceRecordRequest;
use App\Models\ExperienceRecord;
use Illuminate\Http\RedirectResponse;

class ExperienceRecordController extends Controller
{
    /**
     * Store a new work experience record for the authenticated applicant.
     */
    public function store(ExperienceRecordRequest $request): RedirectResponse
    {
        $request->user()->experienceRecords()->create($request->validated());

        return back();
    }

    /**
     * Update an existing work experience record.
     */
    public function update(ExperienceRecordRequest $request, ExperienceRecord $experienceRecord): RedirectResponse
    {
        abort_unless($experienceRecord->user_id === $request->user()->id, 403);

        $experienceRecord->update($request->validated());

        return back();
    }

    /**
     * Delete a work experience record.
     */
    public function destroy(ExperienceRecord $experienceRecord): RedirectResponse
    {
        abort_unless($experienceRecord->user_id === request()->user()->id, 403);

        $experienceRecord->delete();

        return back();
    }
}
