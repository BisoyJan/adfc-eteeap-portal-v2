<?php

namespace App\Models;

use App\Concerns\HasAuditLog;
use App\Enums\EquivalencyMatrixStatus;
use App\Enums\EquivalencyMatrixType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $application_id
 * @property EquivalencyMatrixType $type
 * @property EquivalencyMatrixStatus $status
 * @property int $prepared_by
 * @property int|null $approved_by
 * @property Carbon|null $approved_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['application_id', 'type', 'status', 'prepared_by', 'approved_by', 'approved_at'])]
class EquivalencyMatrix extends Model
{
    use HasAuditLog;
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => EquivalencyMatrixType::class,
            'status' => EquivalencyMatrixStatus::class,
            'approved_at' => 'datetime',
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
    public function preparer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * @return HasMany<EquivalencyMatrixItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(EquivalencyMatrixItem::class);
    }
}
