<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use SoftDeletes, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'cde_project_id',
        'po_number',
        'supplier_id',
        'warehouse_id',
        'status',
        'order_date',
        'expected_date',
        'received_date',
        'subtotal',
        'tax_amount',
        'shipping_cost',
        'total_amount',
        'notes',
        'created_by',
        'approved_by',
        'submitted_at',
        'approved_at',
        'rejection_reason',
        'approval_level',
        'level1_approved_by',
        'level1_approved_at',
        'level2_approved_by',
        'level2_approved_at',
        'level2_rejection_reason',
        'approval_threshold',
    ];

    protected $casts = [
        'order_date'         => 'date',
        'expected_date'      => 'date',
        'received_date'      => 'date',
        'submitted_at'       => 'datetime',
        'approved_at'        => 'datetime',
        'level1_approved_at' => 'datetime',
        'level2_approved_at' => 'datetime',
        'subtotal'           => 'decimal:2',
        'total_amount'       => 'decimal:2',
        'approval_threshold' => 'decimal:2',
    ];

    public static array $statuses = [
        'draft' => 'Draft',
        'submitted' => 'Submitted',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'ordered' => 'Ordered',
        'partially_received' => 'Partially Received',
        'received' => 'Received',
        'cancelled' => 'Cancelled',
    ];

    public function cdeProject()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    public function level1Approver()
    {
        return $this->belongsTo(User::class, 'level1_approved_by');
    }
    public function level2Approver()
    {
        return $this->belongsTo(User::class, 'level2_approved_by');
    }

    // ─── Approval helpers ───────────────────────────────────
    public function canBeSubmitted(): bool
    {
        return $this->status === 'draft' || $this->status === 'rejected';
    }
    public function canBeApproved(): bool
    {
        return $this->status === 'submitted';
    }
    public function canBeRejected(): bool
    {
        return $this->status === 'submitted';
    }
    /** Requires L2 approval if total exceeds threshold */
    public function requiresDualApproval(): bool
    {
        return $this->approval_level >= 2
            || ($this->approval_threshold && $this->total_amount >= $this->approval_threshold);
    }
    public function needsLevel1Approval(): bool
    {
        return $this->status === 'submitted' && is_null($this->level1_approved_at);
    }
    public function needsLevel2Approval(): bool
    {
        return $this->requiresDualApproval()
            && !is_null($this->level1_approved_at)
            && is_null($this->level2_approved_at)
            && $this->status === 'submitted';
    }
    public function isFullyApproved(): bool
    {
        if ($this->requiresDualApproval()) {
            return !is_null($this->level1_approved_at) && !is_null($this->level2_approved_at);
        }
        return $this->status === 'approved';
    }
}
