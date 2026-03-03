<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryNote extends Model
{
    use SoftDeletes, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'cde_project_id',
        'dn_number',
        'material_issuance_id',
        'purchase_order_id',
        'destination',
        'destination_contact',
        'destination_phone',
        'vehicle_number',
        'driver_name',
        'driver_phone',
        'warehouse_id',
        'status',
        'dispatch_date',
        'delivery_date',
        'notes',
        'delivery_proof',
        'dispatched_by',
        'received_by_user',
        'received_by_name',
        'received_by_signature',
        'milestone_id',
    ];

    protected $casts = [
        'dispatch_date' => 'date',
        'delivery_date' => 'date',
    ];

    public static array $statuses = [
        'draft' => 'Draft',
        'dispatched' => 'Dispatched',
        'in_transit' => 'In Transit',
        'delivered' => 'Delivered',
        'partial' => 'Partially Delivered',
    ];

    // ─── Relationships ───
    public function items()
    {
        return $this->hasMany(DeliveryNoteItem::class);
    }
    public function project()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }
    public function issuance()
    {
        return $this->belongsTo(MaterialIssuance::class, 'material_issuance_id');
    }
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function milestone()
    {
        return $this->belongsTo(Milestone::class);
    }
    public function dispatcher()
    {
        return $this->belongsTo(User::class, 'dispatched_by');
    }
    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by_user');
    }

    // ─── Helpers ───
    public function getTotalDispatchedAttribute(): float
    {
        return $this->items->sum('quantity_dispatched');
    }
    public function getTotalReceivedAttribute(): float
    {
        return $this->items->sum('quantity_received');
    }
}
