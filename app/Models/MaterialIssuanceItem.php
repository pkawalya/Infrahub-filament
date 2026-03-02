<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialIssuanceItem extends Model
{
    protected $fillable = [
        'material_issuance_id',
        'product_id',
        'quantity_issued',
        'quantity_returned',
        'condition_on_issue',
        'condition_on_return',
        'notes',
    ];

    public function issuance()
    {
        return $this->belongsTo(MaterialIssuance::class, 'material_issuance_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
