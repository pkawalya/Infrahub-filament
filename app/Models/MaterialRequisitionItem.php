<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialRequisitionItem extends Model
{
    protected $fillable = [
        'material_requisition_id',
        'product_id',
        'quantity_requested',
        'quantity_approved',
        'quantity_issued',
        'unit',
        'notes',
    ];

    public function requisition()
    {
        return $this->belongsTo(MaterialRequisition::class, 'material_requisition_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
