<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class DailySiteDiary extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'cde_project_id',
        'diary_date',
        'weather',
        'temperature',
        'workers_on_site',
        'subcontractor_workers',
        'workforce_breakdown',
        'equipment_on_site',
        'equipment_list',
        'work_performed',
        'work_planned_tomorrow',
        'delays',
        'safety_observations',
        'quality_observations',
        'visitor_log',
        'deliveries',
        'photos',
        'prepared_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'diary_date' => 'date',
        'temperature' => 'decimal:1',
        'workers_on_site' => 'integer',
        'subcontractor_workers' => 'integer',
        'equipment_on_site' => 'integer',
        'workforce_breakdown' => 'array',
        'equipment_list' => 'array',
        'photos' => 'array',
        'approved_at' => 'datetime',
    ];

    public static array $weatherOptions = [
        'sunny' => '☀️ Sunny',
        'cloudy' => '☁️ Cloudy',
        'rainy' => '🌧️ Rainy',
        'windy' => '💨 Windy',
        'stormy' => '⛈️ Stormy',
        'foggy' => '🌫️ Foggy',
        'hot' => '🔥 Hot',
        'cold' => '❄️ Cold',
    ];

    public function project()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }

    public function preparer()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isApproved(): bool
    {
        return $this->approved_by !== null;
    }

    public function getTotalWorkforceAttribute(): int
    {
        return $this->workers_on_site + $this->subcontractor_workers;
    }
}
