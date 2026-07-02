<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Applicant\EducationRecordRequest;
use App\Models\EducationRecord;
use Illuminate\Http\RedirectResponse;

class EducationRecordController extends Controller
{
    /**
     * Store a new education record for the authenticated applicant.
     */
    public function store(EducationRecordRequest $request): RedirectResponse
    {
        $request->user()->educationRecords()->create($request->validated());

        return back();
    }

    /**
     * Update an existing education record.
     */
    public function update(EducationRecordRequest $request, EducationRecord $educationRecord): RedirectResponse
    {
        abort_unless($educationRecord->user_id === $request->user()->id, 403);

        $educationRecord->update($request->validated());

        return back();
    }

    /**
     * Delete an education record.
     */
    public function destroy(EducationRecord $educationRecord): RedirectResponse
    {
        abort_unless($educationRecord->user_id === request()->user()->id, 403);

        $educationRecord->delete();

        return back();
    }
}
