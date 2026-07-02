<?php

namespace App\Services;

use App\Enums\CompetencyLevel;
use App\Enums\EquivalencyRuleType;
use App\Enums\RecommendedAction;
use App\Models\EquivalencyRule;
use App\Models\User;
use Illuminate\Support\Collection;

class EquivalencyEngine
{
    /**
     * Match a user's profile against all active equivalency rules
     * and return suggested matrix items.
     *
     * @return Collection<int, array{
     *   subject_code: string,
     *   subject_title: string,
     *   units: float,
     *   competency_level: CompetencyLevel,
     *   evidence_source: string|null,
     *   equivalent_credits: float,
     *   recommended_action: RecommendedAction,
     * }>
     */
    public function suggestFor(User $user): Collection
    {
        $rules = EquivalencyRule::query()
            ->with('curriculumSubject')
            ->where('is_active', true)
            ->orderBy('priority')
            ->get();

        $matched = collect();
        $seenSubjectIds = [];

        foreach ($rules as $rule) {
            if (! $rule->curriculumSubject) {
                continue;
            }

            if (in_array($rule->curriculumSubject->id, $seenSubjectIds, true)) {
                continue;
            }

            if (! $this->matchesCondition($rule, $user)) {
                continue;
            }

            $subject = $rule->curriculumSubject;
            $matched->push([
                'subject_code' => $subject->code,
                'subject_title' => $subject->title,
                'units' => $subject->units,
                'competency_level' => CompetencyLevel::Full,
                'evidence_source' => 'Matched via rule: '.$rule->rule_type->label().' ('.$rule->condition_key.')',
                'equivalent_credits' => $rule->equivalent_credits ?: $subject->units,
                'recommended_action' => RecommendedAction::Credit,
            ]);

            $seenSubjectIds[] = $subject->id;
        }

        return $matched;
    }

    /**
     * Check if a user matches a given rule's condition.
     */
    protected function matchesCondition(EquivalencyRule $rule, User $user): bool
    {
        return match ($rule->rule_type) {
            EquivalencyRuleType::Direct => $this->matchDirect($rule, $user),
            EquivalencyRuleType::Experience => $this->matchExperience($rule, $user),
            EquivalencyRuleType::Certification => $this->matchCertification($rule, $user),
            EquivalencyRuleType::Training => $this->matchTraining($rule, $user),
            EquivalencyRuleType::PriorEducation => $this->matchPriorEducation($rule, $user),
        };
    }

    protected function matchDirect(EquivalencyRule $rule, User $user): bool
    {
        // Direct rules apply if the user has relevant experience aligned
        return $user->experienceRecords()
            ->where('bsit_aligned', true)
            ->exists();
    }

    protected function matchExperience(EquivalencyRule $rule, User $user): bool
    {
        $requiredYears = (int) $rule->condition_value;
        $totalYears = $this->totalExperienceYears($user);

        return $totalYears >= $requiredYears;
    }

    protected function matchCertification(EquivalencyRule $rule, User $user): bool
    {
        // Check if any document description matches the condition
        $keyword = strtolower($rule->condition_value);

        return $user->documents()
            ->where('document_type', 'training_certificates')
            ->get()
            ->contains(function ($doc) use ($keyword) {
                return str_contains(strtolower($doc->file_name), $keyword);
            });
    }

    protected function matchTraining(EquivalencyRule $rule, User $user): bool
    {
        // Similar to certification matching
        $keyword = strtolower($rule->condition_value);

        return $user->documents()
            ->where('document_type', 'training_certificates')
            ->get()
            ->contains(function ($doc) use ($keyword) {
                return str_contains(strtolower($doc->file_name), $keyword);
            });
    }

    protected function matchPriorEducation(EquivalencyRule $rule, User $user): bool
    {
        $educationLevel = strtolower($rule->condition_value);

        return $user->educationRecords()
            ->where('education_level', $educationLevel)
            ->where('is_graduated', true)
            ->exists();
    }

    protected function totalExperienceYears(User $user): float
    {
        $seconds = $user->experienceRecords->reduce(function ($carry, $record) {
            $end = $record->end_date ?? now();

            return $carry + $end->diffInSeconds($record->start_date, true);
        }, 0);

        return round($seconds / (365.25 * 24 * 60 * 60), 1);
    }
}
