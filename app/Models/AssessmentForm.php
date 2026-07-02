<?php

namespace App\Models;

use App\Enums\AssessmentStage;
use App\Enums\AssessmentVerdict;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $application_id
 * @property AssessmentStage $stage
 * @property int $assessor_id
 * @property array<string, mixed> $criteria
 * @property float|null $total_score
 * @property float|null $max_score
 * @property AssessmentVerdict|null $verdict
 * @property string|null $digital_signature
 * @property Carbon|null $submitted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['application_id', 'stage', 'assessor_id', 'criteria', 'total_score', 'max_score', 'verdict', 'digital_signature', 'submitted_at'])]
class AssessmentForm extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'stage' => AssessmentStage::class,
            'verdict' => AssessmentVerdict::class,
            'criteria' => 'array',
            'submitted_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Application, $this>
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessor_id');
    }
}
