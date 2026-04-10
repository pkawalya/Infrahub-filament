<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use App\Models\Concerns\HasHashedRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrewAttendance extends Model
{
    use BelongsToCompany, HasHashedRouteKey, SoftDeletes;

    protected $table = 'crew_attendance';

    protected $fillable = [
        'company_id',
        'user_id',
        'cde_project_id',
        'attendance_date',
        'clock_in',
        'clock_out',
        'hours_worked',
        'overtime_hours',
        'status',
        'site_location',
        'notes',
        'approved_by',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'hours_worked' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
    ];

    public static array $statuses = [
        'present' => 'Present',
        'absent' => 'Absent',
        'late' => 'Late',
        'half_day' => 'Half Day',
        'leave' => 'On Leave',
    ];

    public function worker()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function project()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
