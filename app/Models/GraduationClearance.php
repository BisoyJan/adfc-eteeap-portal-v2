<?php

namespace App\Models;

use App\Enums\GraduationClearanceStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $student_id
 * @property GraduationClearanceStatus $status
 * @property string|null $confirmation_letter_path
 * @property Carbon|null $vpaa_approved_at
 * @property string|null $registry_number
 * @property Carbon|null $diploma_printed_at
 * @property Carbon|null $tor_released_at
 * @property string|null $clearance_form_path
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['student_id', 'status', 'confirmation_letter_path', 'vpaa_approved_at', 'registry_number', 'diploma_printed_at', 'tor_released_at', 'clearance_form_path'])]
class GraduationClearance extends Model
{
    protected function casts(): array
    {
        return [
            'status' => GraduationClearanceStatus::class,
            'vpaa_approved_at' => 'datetime',
            'diploma_printed_at' => 'datetime',
            'tor_released_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * @return HasMany<ClearanceDocument, $this>
     */
    public function documents(): HasMany
    {
        return $this->hasMany(ClearanceDocument::class);
    }
}
