<?php

namespace App\Models;

use App\Concerns\HasAuditLog;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $graduation_clearance_id
 * @property string $document_type
 * @property string|null $file_path
 * @property bool $is_verified
 * @property int|null $verified_by
 * @property Carbon|null $verified_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['graduation_clearance_id', 'document_type', 'file_path', 'is_verified', 'verified_by', 'verified_at'])]
class ClearanceDocument extends Model
{
    use HasAuditLog;
    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<GraduationClearance, $this>
     */
    public function clearance(): BelongsTo
    {
        return $this->belongsTo(GraduationClearance::class, 'graduation_clearance_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
