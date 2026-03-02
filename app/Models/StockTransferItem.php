<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransferItem extends Model
{
    protected $fillable = [
        'stock_transfer_id',
        'product_id',
        'quantity_requested',
        'quantity_shipped',
        'quantity_received',
        'notes',
    ];

    protected $casts = [
        'quantity_requested' => 'decimal:2',
        'quantity_shipped' => 'decimal:2',
        'quantity_received' => 'decimal:2',
    ];

    public function stockTransfer()
    {
        return $this->belongsTo(StockTransfer::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
