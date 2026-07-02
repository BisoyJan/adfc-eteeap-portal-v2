<?php

namespace App\Models;

use App\Enums\EnrollmentSubjectStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $enrollment_id
 * @property int $curriculum_subject_id
 * @property EnrollmentSubjectStatus $status
 */
#[Fillable(['enrollment_id', 'curriculum_subject_id', 'status'])]
class EnrollmentSubject extends Model
{
    protected function casts(): array
    {
        return [
            'status' => EnrollmentSubjectStatus::class,
        ];
    }

    /**
     * @return BelongsTo<Enrollment, $this>
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * @return BelongsTo<CurriculumSubject, $this>
     */
    public function curriculumSubject(): BelongsTo
    {
        return $this->belongsTo(CurriculumSubject::class);
    }
}
