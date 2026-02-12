<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class DailySiteLog extends Model
{
    use BelongsToCompany;

    public $timestamps = true;

    protected $fillable = [
        'company_id',
        'cde_project_id',
        'log_date',
        'weather',
        'temperature_high',
        'temperature_low',
        'workers_on_site',
        'visitors_on_site',
        'work_performed',
        'materials_received',
        'equipment_used',
        'delays',
        'safety_incidents',
        'notes',
        'status',
        'created_by',
        'approved_by',
    ];

    protected $casts = [
        'log_date' => 'date',
        'temperature_high' => 'decimal:1',
        'temperature_low' => 'decimal:1',
        'workers_on_site' => 'integer',
        'visitors_on_site' => 'integer',
    ];

    public static array $statuses = [
        'draft' => 'Draft',
        'submitted' => 'Submitted',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ];

    public static array $weatherOptions = [
        'sunny' => 'Sunny',
        'partly_cloudy' => 'Partly Cloudy',
        'cloudy' => 'Cloudy',
        'rainy' => 'Rainy',
        'stormy' => 'Stormy',
        'windy' => 'Windy',
        'foggy' => 'Foggy',
        'hot' => 'Hot',
        'cold' => 'Cold',
    ];

    public function project()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
