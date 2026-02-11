<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstimationItem extends Model
{
    protected $fillable = [
        'estimation_id',
        'service_part_id',
        'type',
        'description',
        'quantity',
        'unit_price',
        'amount',
    ];

    protected $casts = ['unit_price' => 'decimal:2', 'amount' => 'decimal:2'];

    public function estimation()
    {
        return $this->belongsTo(Estimation::class);
    }
    public function servicePart()
    {
        return $this->belongsTo(ServicePart::class);
    }
}
