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
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_date' => 'date',
        'received_date' => 'date',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'total_amount' => 'decimal:2',
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

    // ─── Approval helpers ───
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
}
