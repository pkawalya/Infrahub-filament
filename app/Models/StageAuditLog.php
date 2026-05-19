<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StageAuditLog extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'auditable_type',
        'auditable_id',
        'from_stage',
        'from_stage_id',
        'to_stage',
        'to_stage_id',
        'comment',
        'metadata',
        'user_id',
        'transitioned_at',
    ];

    protected $casts = [
        'metadata'        => 'array',
        'transitioned_at' => 'datetime',
    ];

    // ─── Relationships ───────────────────────────────────────

    public function auditable()
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Factory Method ──────────────────────────────────────

    /**
     * Record a stage transition.
     */
    public static function record(
        Model   $model,
        ?string $fromStageName,
        ?int    $fromStageId,
        string  $toStageName,
        ?int    $toStageId,
        ?string $comment = null,
        ?array  $metadata = null,
    ): static {
        return static::create([
            'company_id'      => $model->company_id ?? auth()->user()?->company_id,
            'auditable_type'  => $model->getMorphClass(),
            'auditable_id'    => $model->getKey(),
            'from_stage'      => $fromStageName,
            'from_stage_id'   => $fromStageId,
            'to_stage'        => $toStageName,
            'to_stage_id'     => $toStageId,
            'comment'         => $comment,
            'metadata'        => $metadata,
            'user_id'         => auth()->id(),
            'transitioned_at' => now(),
        ]);
    }
}
