<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoqMaterialUsage extends Model
{
    protected $fillable = [
        'boq_item_id',
        'product_id',
        'quantity_used',
        'usage_date',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'quantity_used' => 'decimal:4',
        'usage_date' => 'date',
    ];

    public function boqItem()
    {
        return $this->belongsTo(BoqItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
