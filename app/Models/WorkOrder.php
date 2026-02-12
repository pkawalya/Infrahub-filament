<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkOrder extends Model
{
    use SoftDeletes, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'cde_project_id',
        'wo_number',
        'title',
        'description',
        'work_order_type_id',
        'client_id',
        'asset_id',
        'work_order_request_id',
        'priority',
        'status',
        'assigned_to',
        'due_date',
        'preferred_date',
        'preferred_time',
        'preferred_notes',
        'started_at',
        'completed_at',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'due_date' => 'date',
        'preferred_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public static array $statuses = [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'in_progress' => 'In Progress',
        'on_hold' => 'On Hold',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    public static array $priorities = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'urgent' => 'Urgent',
    ];

    public function cdeProject()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }
    public function type()
    {
        return $this->belongsTo(WorkOrderType::class, 'work_order_type_id');
    }
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
    public function request()
    {
        return $this->belongsTo(WorkOrderRequest::class, 'work_order_request_id');
    }
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function items()
    {
        return $this->hasMany(WorkOrderItem::class);
    }
    public function tasks()
    {
        return $this->hasMany(WorkOrderTask::class);
    }
    public function appointments()
    {
        return $this->hasMany(WorkOrderAppointment::class);
    }
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }
}
