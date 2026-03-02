<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrnItem extends Model
{
    protected $fillable = [
        'goods_received_note_id',
        'purchase_order_item_id',
        'product_id',
        'description',
        'quantity_expected',
        'quantity_received',
        'quantity_accepted',
        'quantity_rejected',
        'condition',
        'rejection_reason',
    ];

    protected $casts = [
        'quantity_expected' => 'decimal:2',
        'quantity_received' => 'decimal:2',
        'quantity_accepted' => 'decimal:2',
        'quantity_rejected' => 'decimal:2',
    ];

    public static array $conditions = [
        'good' => 'Good',
        'damaged' => 'Damaged',
        'defective' => 'Defective',
    ];

    public function goodsReceivedNote()
    {
        return $this->belongsTo(GoodsReceivedNote::class);
    }
    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getHasVarianceAttribute(): bool
    {
        return $this->quantity_received != $this->quantity_expected;
    }
}
