<?php

namespace App\Models;

use App\Concerns\HasAuditLog;
use App\Enums\SubjectCategory;
use Database\Factories\CurriculumSubjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $code
 * @property string $title
 * @property float $units
 * @property array<int, string>|null $prerequisite_codes
 * @property SubjectCategory $category
 * @property int|null $year_level
 * @property int|null $semester
 * @property array<int, string>|null $learning_outcomes
 * @property bool $is_active
 * @property string|null $curriculum_version
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['code', 'title', 'units', 'prerequisite_codes', 'category', 'year_level', 'semester', 'learning_outcomes', 'is_active', 'curriculum_version'])]
class CurriculumSubject extends Model
{
    /** @use HasFactory<CurriculumSubjectFactory> */
    use HasAuditLog, HasFactory;

    protected $table = 'curriculum_subjects';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'prerequisite_codes' => 'array',
            'learning_outcomes' => 'array',
            'category' => SubjectCategory::class,
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return HasMany<EquivalencyRule, $this>
     */
    public function equivalencyRules(): HasMany
    {
        return $this->hasMany(EquivalencyRule::class);
    }

    /**
     * @return HasMany<Rubric, $this>
     */
    public function rubrics(): HasMany
    {
        return $this->hasMany(Rubric::class);
    }

    /**
     * @return HasMany<EnrollmentSubject, $this>
     */
    public function enrollmentSubjects(): HasMany
    {
        return $this->hasMany(EnrollmentSubject::class);
    }

    /**
     * @return HasMany<Grade, $this>
     */
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }
}
