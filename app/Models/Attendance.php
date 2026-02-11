<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id',
        'date',
        'clock_in',
        'clock_out',
        'total_hours',
        'status',
        'notes',
    ];

    protected $casts = ['date' => 'date', 'clock_in' => 'datetime', 'clock_out' => 'datetime'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
