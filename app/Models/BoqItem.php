<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoqItem extends Model
{
    protected $fillable = [
        'boq_id',
        'item_code',
        'description',
        'unit',
        'quantity',
        'unit_rate',
        'amount',
        'category',
        'sort_order',
    ];

    protected $casts = ['quantity' => 'decimal:4', 'unit_rate' => 'decimal:2', 'amount' => 'decimal:2'];

    public function boq()
    {
        return $this->belongsTo(Boq::class);
    }
}
