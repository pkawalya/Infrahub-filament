<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialRequisitionItem extends Model
{
    protected $guarded = [];

    public function requisition()
    {
        return $this->belongsTo(MaterialRequisition::class, 'material_requisition_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
