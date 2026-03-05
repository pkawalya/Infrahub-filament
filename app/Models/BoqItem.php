<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property float $quantity
 * @property float $quantity_completed
 * @property float $actual_quantity
 * @property float $actual_cost
 * @property float $unit_rate
 * @property float $amount
 * @property float $variance_amount
 * @property float $variance_percent
 * @property bool $is_variation
 * @property int|null $product_id
 * @property \Carbon\Carbon|null $last_synced_at
 */
class BoqItem extends Model
{
    public static array $categories = [
        'material' => 'Material',
        'labor' => 'Labor',
        'equipment' => 'Equipment',
        'subcontract' => 'Subcontract',
        'overhead' => 'Overhead',
    ];

    public static array $units = [
        'pcs',
        'nos',
        'kg',
        'ton',
        'm',
        'm2',
        'm3',
        'l',
        'hrs',
        'days',
        'bags',
        'trips',
        'ls',
    ];

    protected $fillable = [
        'boq_id',
        'product_id',
        'item_code',
        'description',
        'unit',
        'quantity',
        'quantity_completed',
        'actual_quantity',
        'actual_cost',
        'variance_amount',
        'variance_percent',
        'last_synced_at',
        'unit_rate',
        'amount',
        'category',
        'sort_order',
        'remarks',
        'is_variation',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'quantity_completed' => 'decimal:4',
        'actual_quantity' => 'decimal:4',
        'actual_cost' => 'decimal:2',
        'variance_amount' => 'decimal:2',
        'variance_percent' => 'decimal:2',
        'unit_rate' => 'decimal:2',
        'amount' => 'decimal:2',
        'is_variation' => 'boolean',
        'last_synced_at' => 'datetime',
    ];

    // ── Relationships ──

    public function boq()
    {
        return $this->belongsTo(Boq::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function usages()
    {
        return $this->hasMany(BoqMaterialUsage::class);
    }

    public function varianceAlerts()
    {
        return $this->hasMany(BoqVarianceAlert::class);
    }

    // ── Computed Properties ──

    /**
     * Budgeted total = quantity × unit_rate.
     */
    public function getBudgetedAmountAttribute(): float
    {
        return (float) $this->quantity * (float) $this->unit_rate;
    }

    /**
     * Variance status label.
     */
    public function getVarianceStatusAttribute(): string
    {
        $pct = (float) $this->variance_percent;
        if (abs($pct) < 5)
            return 'on_budget';
        if ($pct > 0)
            return 'overrun';
        return 'underrun';
    }

    /**
     * Variance Filament color.
     */
    public function getVarianceColorAttribute(): string
    {
        $pct = abs((float) $this->variance_percent);
        if ($pct < 5)
            return 'success';
        if ($pct < 10)
            return 'info';
        if ($pct < 20)
            return 'warning';
        return 'danger';
    }

    // ── Actions ──

    /**
     * Recalculate variance from actual vs budgeted.
     */
    public function recalculateVariance(): void
    {
        $budgeted = $this->budgeted_amount;
        $actual = (float) $this->actual_cost;

        $this->variance_amount = $actual - $budgeted;
        $this->variance_percent = $budgeted > 0
            ? round((($actual - $budgeted) / $budgeted) * 100, 2)
            : 0;
        $this->last_synced_at = now();
        $this->save();
    }
}
