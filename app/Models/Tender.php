<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use App\Models\Concerns\HasHashedRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tender extends Model
{
    use SoftDeletes, BelongsToCompany, HasHashedRouteKey;

    protected $fillable = [
        'company_id',
        'reference',
        'title',
        'client_name',
        'source',
        'status',
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
        'estimated_value' => 'decimal:2',
        'bid_amount' => 'decimal:2',
        'submission_deadline' => 'date',
        'submitted_at' => 'date',
        'decision_date' => 'date',
        'attachments' => 'array',
        'win_probability' => 'integer',
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

    public static array $categories = [
        'construction' => 'Construction',
        'renovation' => 'Renovation',
        'maintenance' => 'Maintenance',
        'supply' => 'Supply & Install',
        'design_build' => 'Design & Build',
        'civil' => 'Civil Works',
        'infrastructure' => 'Infrastructure',
        'other' => 'Other',
    ];

    public static array $sources = [
        'public' => 'Public Tender',
        'private' => 'Private Invitation',
        'referral' => 'Referral',
        'portal' => 'Online Portal',
        'newspaper' => 'Newspaper',
        'other' => 'Other',
    ];

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

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
}
