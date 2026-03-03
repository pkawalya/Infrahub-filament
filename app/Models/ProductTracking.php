<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class ProductTracking extends Model
{
    use BelongsToCompany;

    protected $table = 'product_tracking';

    protected $fillable = [
        'company_id',
        'cde_project_id',
        'product_id',
        'stage',
        'milestone_id',
        'task_id',
        'purchase_order_id',
        'delivery_note_id',
        'material_issuance_id',
        'quantity',
        'location',
        'notes',
        'recorded_by',
    ];

    public static array $stages = [
        'ordered' => 'Ordered',
        'received' => 'Received',
        'stored' => 'Stored',
        'issued' => 'Issued',
        'in_transit' => 'In Transit',
        'delivered' => 'Delivered',
        'installed' => 'Installed',
        'returned' => 'Returned',
    ];

    public static array $stageColors = [
        'ordered' => '#6366f1',
        'received' => '#3b82f6',
        'stored' => '#8b5cf6',
        'issued' => '#f59e0b',
        'in_transit' => '#d97706',
        'delivered' => '#10b981',
        'installed' => '#059669',
        'returned' => '#ef4444',
    ];

    // ─── Relationships ───
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function project()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }
    public function milestone()
    {
        return $this->belongsTo(Milestone::class);
    }
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
    public function deliveryNote()
    {
        return $this->belongsTo(DeliveryNote::class);
    }
    public function issuance()
    {
        return $this->belongsTo(MaterialIssuance::class, 'material_issuance_id');
    }
    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
