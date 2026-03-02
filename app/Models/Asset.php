<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use SoftDeletes, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'product_id',
        'cde_project_id',
        'asset_tag',
        'serial_number',
        'name',
        'status',
        'condition',
        'current_holder_id',
        'current_location',
        'warehouse_id',
        'purchase_date',
        'purchase_cost',
        'warranty_expiry',
        'depreciation_method',
        'useful_life_years',
        'salvage_value',
        'qr_code',
        'image',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'purchase_cost' => 'decimal:2',
        'salvage_value' => 'decimal:2',
        'useful_life_years' => 'integer',
    ];

    public static array $statuses = [
        'available' => 'Available',
        'assigned' => 'Assigned',
        'maintenance' => 'In Maintenance',
        'retired' => 'Retired',
        'lost' => 'Lost',
        'disposed' => 'Disposed',
    ];

    public static array $conditions = [
        'new' => 'New',
        'good' => 'Good',
        'fair' => 'Fair',
        'poor' => 'Poor',
        'damaged' => 'Damaged',
    ];

    // ─── Relationships ──────────────────────────────────────
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function currentHolder()
    {
        return $this->belongsTo(User::class, 'current_holder_id');
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function project()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function assignments()
    {
        return $this->hasMany(AssetAssignment::class)->orderByDesc('created_at');
    }
    public function maintenanceLogs()
    {
        return $this->hasMany(AssetMaintenanceLog::class)->orderByDesc('created_at');
    }

    // ─── Computed ───────────────────────────────────────────
    public function getDisplayNameAttribute(): string
    {
        return $this->name ?: $this->product?->name ?: $this->asset_tag;
    }

    public function getCurrentBookValueAttribute(): float
    {
        if (!$this->purchase_date || !$this->purchase_cost)
            return (float) $this->purchase_cost;

        $age = $this->purchase_date->diffInYears(now());
        if ($age >= $this->useful_life_years)
            return (float) $this->salvage_value;

        $depreciable = $this->purchase_cost - $this->salvage_value;
        $annualDep = $depreciable / max($this->useful_life_years, 1);
        return max((float) $this->purchase_cost - ($annualDep * $age), (float) $this->salvage_value);
    }

    public function getQrPayloadAttribute(): string
    {
        return json_encode([
            'asset_tag' => $this->asset_tag,
            'name' => $this->display_name,
            'serial' => $this->serial_number,
            'status' => $this->status,
            'holder' => $this->currentHolder?->name,
            'location' => $this->current_location,
            'condition' => $this->condition,
        ], JSON_UNESCAPED_UNICODE);
    }

    public function getQrCodeUrlAttribute(): string
    {
        return 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($this->qr_payload);
    }

    public function isWarrantyActive(): bool
    {
        return $this->warranty_expiry && $this->warranty_expiry->isFuture();
    }
}
