<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class BillingRecord extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'period',
        'period_start',
        'period_end',
        'base_platform_fee',
        'project_fees',
        'module_fees',
        'addon_fees',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'status',
        'active_projects_count',
        'active_users_count',
        'storage_used_gb',
        'line_items',
        'finalized_at',
        'paid_at',
        'payment_reference',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'base_platform_fee' => 'decimal:2',
        'project_fees' => 'decimal:2',
        'module_fees' => 'decimal:2',
        'addon_fees' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'storage_used_gb' => 'decimal:2',
        'line_items' => 'array',
        'finalized_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public static array $statuses = [
        'draft' => 'Draft',
        'finalized' => 'Finalized',
        'paid' => 'Paid',
        'overdue' => 'Overdue',
        'void' => 'Void',
    ];

    // ─── Relationships ──────────────────────────────────────────

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // ─── Helpers ────────────────────────────────────────────────

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isOverdue(): bool
    {
        return $this->status === 'finalized' && $this->period_end->isPast();
    }

    public function markPaid(string $reference = null): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_reference' => $reference,
        ]);
    }

    /**
     * Get subtotal before tax/discounts.
     */
    public function getSubtotalAttribute(): float
    {
        return (float) $this->base_platform_fee
            + (float) $this->project_fees
            + (float) $this->module_fees
            + (float) $this->addon_fees;
    }

    // ─── Scopes ─────────────────────────────────────────────────

    public function scopeForPeriod($query, string $period)
    {
        return $query->where('period', $period);
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['finalized', 'overdue']);
    }
}
