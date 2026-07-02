<?php

namespace App\Services;

use App\Enums\EquivalencyMatrixStatus;
use App\Enums\EquivalencyMatrixType;
use App\Models\Application;
use App\Models\EquivalencyMatrix;
use App\Models\User;

class EquivalencyService
{
    /**
     * Get or create the matrix of the given type for an application.
     */
    public function forApplication(Application $application, EquivalencyMatrixType $type, User $preparer): EquivalencyMatrix
    {
        return $application->equivalencyMatrices()->firstOrCreate(
            ['type' => $type->value],
            ['status' => EquivalencyMatrixStatus::Draft, 'prepared_by' => $preparer->id],
        );
    }

    /**
     * Replace all line items on a matrix.
     *
     * @param  array<int, array{subject_code: string, subject_title: string, units: float, competency_level: string, evidence_source?: string|null, equivalent_credits: float, recommended_action?: string|null, notes?: string|null}>  $items
     */
    public function saveItems(EquivalencyMatrix $matrix, array $items): EquivalencyMatrix
    {
        $matrix->items()->delete();
        $matrix->items()->createMany($items);

        return $matrix->refresh()->load('items');
    }

    /**
     * Mark a matrix as submitted for approval.
     */
    public function submit(EquivalencyMatrix $matrix): EquivalencyMatrix
    {
        $matrix->update(['status' => EquivalencyMatrixStatus::Submitted]);

        return $matrix->refresh();
    }

    /**
     * Approve a (typically final) matrix.
     */
    public function approve(EquivalencyMatrix $matrix, User $approver): EquivalencyMatrix
    {
        $matrix->update([
            'status' => EquivalencyMatrixStatus::Approved,
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);

        return $matrix->refresh();
    }
}
