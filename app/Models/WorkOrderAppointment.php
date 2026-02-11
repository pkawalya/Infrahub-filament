<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrderAppointment extends Model
{
    protected $fillable = [
        'work_order_id',
        'technician_id',
        'scheduled_date',
        'start_time',
        'end_time',
        'status',
        'notes',
    ];

    protected $casts = ['scheduled_date' => 'date'];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }
    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }
}
