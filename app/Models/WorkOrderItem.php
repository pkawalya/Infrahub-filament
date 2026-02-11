<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrderItem extends Model
{
    protected $fillable = [
        'work_order_id',
        'service_part_id',
        'type',
        'description',
        'quantity',
        'unit_price',
        'amount',
    ];

    protected $casts = ['unit_price' => 'decimal:2', 'amount' => 'decimal:2'];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }
    public function servicePart()
    {
        return $this->belongsTo(ServicePart::class);
    }
}
