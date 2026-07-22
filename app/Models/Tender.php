<?php

namespace App\Models;

use App\Models\CdeActivityLog;
use App\Models\Concerns\BelongsToCompany;
use App\Models\Concerns\HasHashedRouteKey;
use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tender extends Model
{
    use SoftDeletes, BelongsToCompany, HasHashedRouteKey, LogsActivity;

    protected $fillable = [
        'company_id',
        'reference',
        'title',
        'client_name',
        'source',
        'status',
        'tender_stage_id',
        'stage_changed_at',
        'estimated_value',
        'bid_amount',
        'submission_deadline',
        'submitted_at',
        'decision_date',
        'category',
        'region',
        'win_probability',
        'competitors',
        'strategy_notes',
        'loss_reason',
        'document_path',
        'attachments',
        'assigned_to',
        'created_by',
    ];

    protected $casts = [
        'estimated_value'     => 'decimal:2',
        'bid_amount'          => 'decimal:2',
        'submission_deadline' => 'date',
        'submitted_at'        => 'date',
        'decision_date'       => 'date',
        'stage_changed_at'    => 'datetime',
        'attachments'         => 'array',
        'win_probability'     => 'integer',
    ];

    public static array $statuses = [
        'identified' => 'Identified',
        'preparing' => 'Preparing',
        'submitted' => 'Submitted',
        'shortlisted' => 'Shortlisted',
        'awarded' => 'Awarded',
        'lost' => 'Lost',
        'withdrawn' => 'Withdrawn',
    ];

    public static array $validTransitions = [
        'identified' => ['preparing'],
        'preparing' => ['submitted', 'identified'],
        'submitted' => ['shortlisted', 'awarded', 'lost'],
        'shortlisted' => ['awarded', 'lost'],
        'awarded' => [],
        'lost' => [],
        'withdrawn' => [],
    ];

    public function canTransitionTo(string $newStatus): bool
    {
        if ($this->status === $newStatus) {
            return true;
        }

        $allowed = static::$validTransitions[$this->status] ?? [];

        return in_array($newStatus, $allowed, true);
    }

    public function transitionTo(string $newStatus): bool
    {
        if (!$this->canTransitionTo($newStatus)) {
            $allowed = static::$validTransitions[$this->status] ?? [];
            throw new \InvalidArgumentException(
                "Cannot transition tender '{$this->reference}' from '{$this->status}' to '{$newStatus}'. " .
                "Allowed transitions: " . implode(', ', $allowed)
            );
        }

        $fromStatus = $this->status;
        $result = $this->update(['status' => $newStatus]);

        if ($result) {
            CdeActivityLog::record(
                $this,
                'status_changed',
                "Tender '{$this->reference}' status changed from '{$fromStatus}' to '{$newStatus}'",
                ['from' => $fromStatus, 'to' => $newStatus],
            );
        }

        return $result;
    }

    public static array $categories = [
        'construction'  => 'Construction',
        'renovation'    => 'Renovation',
        'maintenance'   => 'Maintenance',
        'supply'        => 'Supply & Install',
        'design_build'  => 'Design & Build',
        'civil'         => 'Civil Works',
        'infrastructure' => 'Infrastructure',
        'other'         => 'Other',
    ];

    public static array $sources = [
        'public'   => 'Public Tender',
        'private'  => 'Private Invitation',
        'referral' => 'Referral',
        'portal'   => 'Online Portal',
        'newspaper' => 'Newspaper',
        'other'    => 'Other',
    ];

    // ─── Relationships ───────────────────────────────────────

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(TenderStage::class, 'tender_stage_id');
    }

    public function bids(): HasMany
    {
        return $this->hasMany(TenderBid::class);
    }

    public function auditLogs(): MorphMany
    {
        return $this->morphMany(StageAuditLog::class, 'auditable')
            ->orderByDesc('transitioned_at');
    }

    // ─── Computed ────────────────────────────────────────────

    public function isOverdue(): bool
    {
        return $this->submission_deadline
            && $this->submission_deadline->isPast()
            && !in_array($this->status, ['submitted', 'awarded', 'lost', 'withdrawn']);
    }

    public function getDaysUntilDeadlineAttribute(): ?int
    {
        if (!$this->submission_deadline)
            return null;
        return now()->startOfDay()->diffInDays($this->submission_deadline->startOfDay(), false);
    }

    public function getActiveBidsCountAttribute(): int
    {
        return $this->bids()->count();
    }
}
