<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrderTask extends Model
{
    protected $fillable = [
        'work_order_id',
        'title',
        'description',
        'is_completed',
        'completed_by',
        'completed_at',
        'sort_order',
    ];

    protected $casts = ['is_completed' => 'boolean', 'completed_at' => 'datetime'];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }
    public function completedByUser()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
