<?php

namespace App\Services;

use App\Enums\AssessmentStage;
use App\Enums\AssessmentVerdict;
use App\Models\Application;
use App\Models\AssessmentForm;
use App\Models\User;

class AssessmentService
{
    /**
     * Submit (create or replace) an assessment form for the given application, stage, and assessor.
     *
     * @param  array<int, array{name: string, score: float, max_score: float, comments?: string|null}>  $criteria
     */
    public function submit(
        Application $application,
        AssessmentStage $stage,
        User $assessor,
        array $criteria,
        AssessmentVerdict $verdict,
    ): AssessmentForm {
        $totalScore = 0.0;
        $maxScore = 0.0;

        foreach ($criteria as $criterion) {
            $totalScore += (float) $criterion['score'];
            $maxScore += (float) $criterion['max_score'];
        }

        return AssessmentForm::updateOrCreate(
            [
                'application_id' => $application->id,
                'stage' => $stage,
                'assessor_id' => $assessor->id,
            ],
            [
                'criteria' => $criteria,
                'total_score' => $totalScore,
                'max_score' => $maxScore,
                'verdict' => $verdict,
                'digital_signature' => "{$assessor->name} - ".now()->toDateTimeString(),
                'submitted_at' => now(),
            ],
        );
    }
}
