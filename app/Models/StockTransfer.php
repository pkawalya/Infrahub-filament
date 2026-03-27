<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockTransfer extends Model
{
    use SoftDeletes, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'cde_project_id',
        'transfer_number',
        'delivery_note_number',
        'from_warehouse_id',
        'to_warehouse_id',
        'status',
        'priority',
        'transfer_date',
        'requested_date',
        'shipped_date',
        'received_date',
        'reason',
        'notes',
        'created_by',
        'requested_by',
        'approved_by',
        'shipped_by',
        'received_by',
        'approval_level',
        'level1_approved_by',
        'level1_approved_at',
        'level2_approved_by',
        'level2_approved_at',
    ];

    protected $casts = [
        'transfer_date'      => 'date',
        'requested_date'     => 'date',
        'shipped_date'       => 'date',
        'received_date'      => 'date',
        'level1_approved_at' => 'datetime',
        'level2_approved_at' => 'datetime',
    ];

    public static array $statuses = [
        'draft' => 'Draft',
        'pending_approval' => 'Pending Approval',
        'approved' => 'Approved',
        'in_transit' => 'In Transit',
        'received' => 'Received',
        'cancelled' => 'Cancelled',
    ];

    public static array $priorities = [
        'low' => 'Low',
        'normal' => 'Normal',
        'high' => 'High',
        'urgent' => 'Urgent',
    ];

    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }
    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }
    public function items()
    {
        return $this->hasMany(StockTransferItem::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    public function cdeProject()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }
    public function deliveryNotes()
    {
        return $this->hasMany(DeliveryNote::class);
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
    /** Is L1 approval still pending? */
    public function needsLevel1Approval(): bool
    {
        return $this->status === 'pending_approval' && is_null($this->level1_approved_at);
    }
    /** Is L2 approval still pending (dual-level only)? */
    public function needsLevel2Approval(): bool
    {
        return $this->approval_level >= 2
            && !is_null($this->level1_approved_at)
            && is_null($this->level2_approved_at);
    }
    /** Fully approved (all required levels passed)? */
    public function isFullyApproved(): bool
    {
        if ($this->approval_level >= 2) {
            return !is_null($this->level1_approved_at) && !is_null($this->level2_approved_at);
        }
        // Single-level: approved_at timestamp must be set (not approved_by user ID which is always truthy)
        return !is_null($this->level1_approved_at) || !is_null($this->approved_at);
    }
}
