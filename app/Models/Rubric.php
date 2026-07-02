<?php

namespace App\Models;

use App\Concerns\HasAuditLog;
use Database\Factories\RubricFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $user_id
 * @property int|null $curriculum_subject_id
 * @property string $title
 * @property array<int, array{name: string, weight: float, levels: array<int, array{score: float, description: string}>}> $criteria
 * @property float|null $max_score
 * @property bool $is_template
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['user_id', 'curriculum_subject_id', 'title', 'criteria', 'max_score', 'is_template'])]
class Rubric extends Model
{
    /** @use HasFactory<RubricFactory> */
    use HasAuditLog, HasFactory;

    protected $table = 'rubrics';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'criteria' => 'array',
            'is_template' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<CurriculumSubject, $this>
     */
    public function curriculumSubject(): BelongsTo
    {
        return $this->belongsTo(CurriculumSubject::class);
    }
}
