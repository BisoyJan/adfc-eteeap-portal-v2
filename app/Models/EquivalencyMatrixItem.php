<?php

namespace App\Models;

use App\Enums\CompetencyLevel;
use App\Enums\RecommendedAction;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $equivalency_matrix_id
 * @property int|null $curriculum_subject_id
 * @property string $subject_code
 * @property string $subject_title
 * @property float $units
 * @property CompetencyLevel $competency_level
 * @property string|null $evidence_source
 * @property float $equivalent_credits
 * @property RecommendedAction|null $recommended_action
 * @property string|null $notes
 */
#[Fillable(['equivalency_matrix_id', 'curriculum_subject_id', 'subject_code', 'subject_title', 'units', 'competency_level', 'evidence_source', 'equivalent_credits', 'recommended_action', 'notes'])]
class EquivalencyMatrixItem extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'competency_level' => CompetencyLevel::class,
            'recommended_action' => RecommendedAction::class,
        ];
    }

    /**
     * @return BelongsTo<EquivalencyMatrix, $this>
     */
    public function equivalencyMatrix(): BelongsTo
    {
        return $this->belongsTo(EquivalencyMatrix::class);
    }

    /**
     * @return BelongsTo<CurriculumSubject, $this>
     */
    public function curriculumSubject(): BelongsTo
    {
        return $this->belongsTo(CurriculumSubject::class);
    }
}
