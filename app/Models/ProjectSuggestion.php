<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectSuggestion extends Model
{
    use BelongsToCompany, SoftDeletes, LogsActivity;
    protected $fillable = [
        'company_id',
        'cde_project_id',
        'author_id',
        'is_anonymous',
        'category',
        'content',
        'status',
        'admin_response',
        'responded_by',
        'responded_at',
        'upvotes',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'responded_at' => 'datetime',
    ];

    // ── Categories ──────────────────────────────────────────
    public static array $categories = [
        'general' => 'General',
        'safety' => 'Safety Concern',
        'process' => 'Process Improvement',
        'equipment' => 'Equipment / Tools',
        'communication' => 'Communication',
        'work_conditions' => 'Work Conditions',
        'other' => 'Other',
    ];

    // ── Statuses ────────────────────────────────────────────
    public static array $statuses = [
        'new' => 'New',
        'reviewed' => 'Reviewed',
        'in_progress' => 'In Progress',
        'implemented' => 'Implemented',
        'dismissed' => 'Dismissed',
    ];

    // ── Priorities ──────────────────────────────────────────
    public static array $priorities = [
        'low' => 'Low',
        'normal' => 'Normal',
        'high' => 'High',
        'urgent' => 'Urgent',
    ];

    // ── Relationships ───────────────────────────────────────
    public function project(): BelongsTo
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function responder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    // ── Helpers ─────────────────────────────────────────────

    /**
     * Get display name (anonymous or real name).
     */
    public function getAuthorDisplayAttribute(): string
    {
        if ($this->is_anonymous) {
            return 'Anonymous';
        }

        return $this->author?->name ?? 'Unknown';
    }

    /**
     * Get the status color for badges.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'new' => 'info',
            'reviewed' => 'warning',
            'in_progress' => 'primary',
            'implemented' => 'success',
            'dismissed' => 'gray',
            default => 'gray',
        };
    }
}
