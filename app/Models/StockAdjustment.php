<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'cde_project_id',
        'adjustment_number',
        'warehouse_id',
        'product_id',
        'type',
        'quantity_before',
        'quantity_after',
        'quantity_change',
        'reason',
        'notes',
        'performed_by',
        'approved_by',
    ];

    protected $casts = [
        'quantity_before' => 'decimal:2',
        'quantity_after' => 'decimal:2',
        'quantity_change' => 'decimal:2',
    ];

    public static array $types = [
        'count' => 'Physical Count',
        'damage' => 'Damage',
        'theft' => 'Theft / Loss',
        'write_off' => 'Write Off',
        'correction' => 'Correction',
        'return' => 'Return to Stock',
        'other' => 'Other',
    ];

    // ─── Relationships ───
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getIsNegativeAttribute(): bool
    {
        return $this->quantity_change < 0;
    }
}
