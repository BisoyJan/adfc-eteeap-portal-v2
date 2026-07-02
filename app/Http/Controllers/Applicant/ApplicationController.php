<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Applicant\ApplicantProfileRequest;
use App\Models\ApplicantProfile;
use App\Services\ApplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ApplicationController extends Controller
{
    public function __construct(private readonly ApplicationService $applications) {}

    /**
     * Show the applicant's editable application.
     */
    public function edit(Request $request): Response
    {
        $user = $request->user();
        $application = $this->applications->currentDraftFor($user);

        return Inertia::render('applicant/application/edit', [
            'application' => $application,
            'profile' => $user->applicantProfile,
            'experienceRecords' => $user->experienceRecords()->latest('start_date')->get(),
            'educationRecords' => $user->educationRecords()->latest('start_year')->get(),
            'qualificationIssues' => $this->applications->qualificationIssues($user),
        ]);
    }

    /**
     * Create or update the applicant's profile information.
     */
    public function update(ApplicantProfileRequest $request): RedirectResponse
    {
        ApplicantProfile::updateOrCreate(
            ['user_id' => $request->user()->id],
            $request->validated(),
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Profile information saved.')]);

        return to_route('applicant.application.edit');
    }

    /**
     * Submit the current application for review.
     */
    public function submit(Request $request): RedirectResponse
    {
        $application = $this->applications->currentDraftFor($request->user());

        try {
            $this->applications->submit($application);
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Application submitted.')]);

        return to_route('applicant.application.edit');
    }
}
