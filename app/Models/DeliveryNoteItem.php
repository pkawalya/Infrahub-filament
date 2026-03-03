<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryNoteItem extends Model
{
    protected $fillable = [
        'delivery_note_id',
        'product_id',
        'description',
        'unit',
        'quantity_dispatched',
        'quantity_received',
        'condition',
        'remarks',
    ];

    public static array $conditions = [
        'good' => 'Good',
        'damaged' => 'Damaged',
        'missing' => 'Missing',
    ];

    public function deliveryNote()
    {
        return $this->belongsTo(DeliveryNote::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
