<?php

namespace App\Services;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class ApplicationService
{
    /**
     * Get the user's editable application (draft or revision requested),
     * creating a new draft if none exists.
     */
    public function currentDraftFor(User $user): Application
    {
        $application = $user->applications()
            ->whereIn('status', [ApplicationStatus::Draft, ApplicationStatus::RevisionRequested])
            ->latest()
            ->first();

        if ($application) {
            return $application;
        }

        return $user->applications()->create([
            'application_number' => $this->generateApplicationNumber(),
            'status' => ApplicationStatus::Draft,
        ]);
    }

    /**
     * Generate the next sequential application number for the current year.
     */
    public function generateApplicationNumber(): string
    {
        $year = now()->year;

        $count = Application::query()
            ->where('application_number', 'like', "APP-{$year}-%")
            ->count();

        return sprintf('APP-%d-%04d', $year, $count + 1);
    }

    /**
     * Determine the qualification issues (if any) preventing submission.
     *
     * @return array<int, string>
     */
    public function qualificationIssues(User $user): array
    {
        $issues = [];

        $profile = $user->applicantProfile;

        if (! $profile) {
            $issues[] = 'Applicant profile is incomplete.';

            return $issues;
        }

        $minAge = (int) config('eteeap.min_age');

        if ($profile->birth_date->age < $minAge) {
            $issues[] = "Applicant must be at least {$minAge} years old.";
        }

        $totalExperienceYears = $this->totalExperienceYears($user);
        $minExperience = (int) config('eteeap.min_experience_years');

        if ($totalExperienceYears < $minExperience) {
            $issues[] = "Applicant must have at least {$minExperience} years of relevant work experience.";
        }

        if (blank($profile->highest_education)) {
            $issues[] = 'Highest education attained is required.';
        }

        return $issues;
    }

    /**
     * Sum the (non-overlapping-agnostic) years of work experience on record.
     */
    public function totalExperienceYears(User $user): float
    {
        $totalDays = 0;

        foreach ($user->experienceRecords as $record) {
            $end = $record->is_current || ! $record->end_date ? now() : $record->end_date;

            $totalDays += $record->start_date->diffInDays($end);
        }

        return $totalDays / 365.25;
    }

    /**
     * Submit the application, enforcing qualification checks.
     *
     * @throws ValidationException
     */
    public function submit(Application $application): Application
    {
        abort_unless(
            in_array($application->status, [ApplicationStatus::Draft, ApplicationStatus::RevisionRequested], true),
            403,
        );

        $issues = $this->qualificationIssues($application->user);

        if ($issues !== []) {
            throw ValidationException::withMessages(['qualification' => $issues]);
        }

        $application->update([
            'status' => ApplicationStatus::Submitted,
            'submitted_at' => now(),
        ]);

        return $application->refresh();
    }

    /**
     * Approve the application.
     */
    public function approve(Application $application, User $reviewer, ?string $notes): void
    {
        $application->update([
            'status' => ApplicationStatus::Approved,
            'reviewed_by' => $reviewer->id,
            'review_notes' => $notes,
        ]);
    }

    /**
     * Reject the application.
     */
    public function reject(Application $application, User $reviewer, string $reason): void
    {
        $application->update([
            'status' => ApplicationStatus::Rejected,
            'reviewed_by' => $reviewer->id,
            'rejection_reason' => $reason,
        ]);
    }

    /**
     * Send the application back to the applicant for revisions.
     */
    public function requestRevision(Application $application, User $reviewer, string $notes): void
    {
        $application->update([
            'status' => ApplicationStatus::RevisionRequested,
            'reviewed_by' => $reviewer->id,
            'review_notes' => $notes,
        ]);
    }

    /**
     * Move a submitted application into the "under review" state.
     */
    public function markUnderReview(Application $application): void
    {
        if ($application->status === ApplicationStatus::Submitted) {
            $application->update(['status' => ApplicationStatus::UnderReview]);
        }
    }
}
