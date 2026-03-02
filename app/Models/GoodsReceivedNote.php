<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsReceivedNote extends Model
{
    use SoftDeletes, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'cde_project_id',
        'purchase_order_id',
        'grn_number',
        'supplier_id',
        'warehouse_id',
        'status',
        'received_date',
        'delivery_note_ref',
        'notes',
        'received_by',
        'inspected_by',
    ];

    protected $casts = [
        'received_date' => 'date',
    ];

    public static array $statuses = [
        'draft' => 'Draft',
        'inspecting' => 'Inspecting',
        'accepted' => 'Accepted',
        'partial' => 'Partially Accepted',
        'rejected' => 'Rejected',
    ];

    // ─── Relationships ───
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
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
        return $this->hasMany(GrnItem::class);
    }
    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
    public function inspectedBy()
    {
        return $this->belongsTo(User::class, 'inspected_by');
    }
    public function cdeProject()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }

    // ─── Helpers ───
    public function getTotalReceivedAttribute(): float
    {
        return $this->items->sum('quantity_received');
    }

    public function getTotalRejectedAttribute(): float
    {
        return $this->items->sum('quantity_rejected');
    }

    public function getVarianceCountAttribute(): int
    {
        return $this->items->filter(fn($i) => $i->quantity_received != $i->quantity_expected)->count();
    }
}
