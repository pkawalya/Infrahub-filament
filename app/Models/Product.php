<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'product_category_id',
        'name',
        'brand',
        'model_number',
        'serial_number',
        'sku',
        'barcode',
        'qr_code',
        'description',
        'unit_of_measure',
        'cost_price',
        'selling_price',
        'reorder_level',
        'reorder_quantity',
        'image',
        'is_active',
        'track_inventory',
        'is_asset',
        'location',
        'condition',
        'warranty_period',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'is_active' => 'boolean',
        'track_inventory' => 'boolean',
    ];

    public static array $conditions = [
        'new' => 'New',
        'good' => 'Good',
        'fair' => 'Fair',
        'poor' => 'Poor',
        'damaged' => 'Damaged',
    ];

    public static array $units = [
        'each' => 'Each',
        'kg' => 'Kg',
        'ton' => 'Ton',
        'liter' => 'Liter',
        'meter' => 'Meter',
        'sqm' => 'Sq. Meter',
        'cum' => 'Cu. Meter',
        'bag' => 'Bag',
        'roll' => 'Roll',
        'sheet' => 'Sheet',
        'box' => 'Box',
        'set' => 'Set',
        'pair' => 'Pair',
        'piece' => 'Piece',
        'bundle' => 'Bundle',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function stockLevels()
    {
        return $this->hasMany(StockLevel::class);
    }

    public function issuanceItems()
    {
        return $this->hasMany(MaterialIssuanceItem::class);
    }

    public function tracking()
    {
        return $this->hasMany(ProductTracking::class);
    }

    public function deliveryNoteItems()
    {
        return $this->hasMany(DeliveryNoteItem::class);
    }

    public function getTotalStockAttribute(): int
    {
        return $this->stockLevels()->sum('quantity_on_hand');
    }

    /**
     * Generate QR code data payload (JSON with product info).
     */
    public function getQrPayloadAttribute(): string
    {
        return json_encode([
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'brand' => $this->brand,
            'model' => $this->model_number,
            'serial' => $this->serial_number,
            'unit' => $this->unit_of_measure,
            'condition' => $this->condition,
            'location' => $this->location,
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Generate QR code inline SVG using a simple QR library or Google Charts API URL.
     */
    public function getQrCodeUrlAttribute(): string
    {
        $data = urlencode($this->qr_payload);
        return "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={$data}";
    }
}
