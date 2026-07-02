<?php

namespace App\Models;

use App\Enums\EquivalencyRuleType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property EquivalencyRuleType $rule_type
 * @property string $condition_key
 * @property string $condition_value
 * @property int $curriculum_subject_id
 * @property float $equivalent_credits
 * @property int $priority
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['rule_type', 'condition_key', 'condition_value', 'curriculum_subject_id', 'equivalent_credits', 'priority', 'is_active'])]
class EquivalencyRule extends Model
{
    protected $table = 'equivalency_rules';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rule_type' => EquivalencyRuleType::class,
            'is_active' => 'boolean',
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
