<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $enhancement_activity_id
 * @property string|null $file_path
 * @property string|null $notes
 * @property Carbon $submitted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['enhancement_activity_id', 'file_path', 'notes', 'submitted_at'])]
class ActivitySubmission extends Model
{
    protected $table = 'activity_submissions';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<EnhancementActivity, $this>
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(EnhancementActivity::class, 'enhancement_activity_id');
    }
}
