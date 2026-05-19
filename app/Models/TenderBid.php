<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use App\Models\Concerns\HasHashedRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenderBid extends Model
{
    use SoftDeletes, BelongsToCompany, HasHashedRouteKey;

    protected $fillable = [
        'company_id',
        'tender_id',
        'reference',
        'bidder_name',
        'bidder_email',
        'bidder_phone',
        'bid_amount',
        'technical_score',
        'financial_score',
        'total_score',
        'bid_stage_id',
        'stage_changed_at',
        'submitted_at',
        'evaluated_at',
        'evaluation_notes',
        'rejection_reason',
        'document_path',
        'attachments',
        'evaluated_by',
        'created_by',
    ];

    protected $casts = [
        'bid_amount'       => 'decimal:2',
        'technical_score'  => 'decimal:2',
        'financial_score'  => 'decimal:2',
        'total_score'      => 'decimal:2',
        'submitted_at'     => 'date',
        'evaluated_at'     => 'date',
        'stage_changed_at' => 'datetime',
        'attachments'      => 'array',
    ];

    // ─── Relationships ───────────────────────────────────────

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(BidStage::class, 'bid_stage_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function auditLogs(): MorphMany
    {
        return $this->morphMany(StageAuditLog::class, 'auditable')
            ->orderByDesc('transitioned_at');
    }
}
