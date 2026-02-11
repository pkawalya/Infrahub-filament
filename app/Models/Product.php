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
        'sku',
        'barcode',
        'description',
        'unit_of_measure',
        'cost_price',
        'selling_price',
        'reorder_level',
        'reorder_quantity',
        'image',
        'is_active',
        'track_inventory',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'is_active' => 'boolean',
        'track_inventory' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }
    public function stockLevels()
    {
        return $this->hasMany(StockLevel::class);
    }

    public function getTotalStockAttribute(): int
    {
        return $this->stockLevels()->sum('quantity_on_hand');
    }
}
