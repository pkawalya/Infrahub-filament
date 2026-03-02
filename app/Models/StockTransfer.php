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
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'requested_date' => 'date',
        'shipped_date' => 'date',
        'received_date' => 'date',
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
}
