<?php

namespace App\Services;

use App\Enums\InterviewStatus;
use App\Models\Application;
use App\Models\Interview;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class InterviewService
{
    /**
     * Assemble (or replace) the assessment panel for an application's F.3 interview.
     *
     * @param  array<int, int>  $panelistIds
     *
     * @throws ValidationException
     */
    public function assignPanel(Application $application, User $creator, array $panelistIds): Interview
    {
        $panelists = User::query()->whereIn('id', $panelistIds)->get();

        $this->validatePanel($panelists);

        $interview = $application->interviews()->first() ?? new Interview(['application_id' => $application->id]);
        $interview->created_by = $creator->id;
        $interview->status = $interview->status ?? InterviewStatus::Pending;
        $interview->save();

        $interview->panelists()->sync($panelists->pluck('id'));

        return $interview->refresh()->load('panelists');
    }

    /**
     * @param  Collection<int, User>  $panelists
     *
     * @throws ValidationException
     */
    protected function validatePanel(Collection $panelists): void
    {
        $minAssessors = (int) config('eteeap.panel_min_assessors');

        if ($panelists->count() < $minAssessors) {
            throw ValidationException::withMessages([
                'panelist_ids' => "The assessment panel must include at least {$minAssessors} assessors.",
            ]);
        }

        $hasInternal = $panelists->contains(fn (User $user) => $user->hasRole('assessor_internal'));
        $hasExternal = $panelists->contains(fn (User $user) => $user->hasRole('assessor_external'));

        if (! $hasInternal || ! $hasExternal) {
            throw ValidationException::withMessages([
                'panelist_ids' => 'The panel must include at least one internal and one external assessor.',
            ]);
        }
    }

    /**
     * Schedule the interview date/time, auto-computing the orientation date two days prior.
     */
    public function schedule(Interview $interview, string $scheduledAt, ?string $location): Interview
    {
        $scheduledAtCarbon = Carbon::parse($scheduledAt);

        $interview->update([
            'scheduled_at' => $scheduledAtCarbon,
            'orientation_at' => $scheduledAtCarbon->copy()->subDays(2),
            'location' => $location,
            'status' => InterviewStatus::Scheduled,
        ]);

        return $interview->refresh();
    }

    /**
     * Mark an interview as completed.
     */
    public function complete(Interview $interview): void
    {
        $interview->update(['status' => InterviewStatus::Completed]);
    }
}
