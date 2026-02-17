<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'job_title',
        'company_size',
        'preferred_date',
        'preferred_time',
        'timezone',
        'message',
        'status',
    ];

    protected $casts = [
        'preferred_date' => 'date',
    ];
}
