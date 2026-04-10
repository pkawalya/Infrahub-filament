<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use App\Models\Concerns\HasHashedRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailySiteDiary extends Model
{
    use BelongsToCompany, HasHashedRouteKey, SoftDeletes;

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
        // Environmental monitoring
        'humidity_percent',
        'wind_speed_kmh',
        'wind_direction',
        'noise_level_db',
        'dust_level_pm10',
        'water_ph',
        'environmental_notes',
        'solar_irradiance',
        // Road construction
        'chainage_from',
        'chainage_to',
        'road_layer',
        'layer_thickness_mm',
        'material_source',
        'truck_loads',
        'compaction_achieved',
        'compaction_required',
        'moisture_content',
        'survey_data',
        'traffic_management_notes',
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
        'humidity_percent' => 'decimal:1',
        'wind_speed_kmh' => 'decimal:1',
        'noise_level_db' => 'decimal:1',
        'dust_level_pm10' => 'decimal:2',
        'water_ph' => 'decimal:2',
        'solar_irradiance' => 'decimal:1',
        'layer_thickness_mm' => 'decimal:1',
        'truck_loads' => 'integer',
        'compaction_achieved' => 'decimal:1',
        'compaction_required' => 'decimal:1',
        'moisture_content' => 'decimal:1',
    ];

    public static array $roadLayers = [
        'subgrade' => 'Subgrade',
        'improved_subgrade' => 'Improved Subgrade',
        'subbase' => 'Sub-base',
        'base' => 'Base Course',
        'primer' => 'Prime Coat',
        'tack' => 'Tack Coat',
        'binder' => 'Binder Course',
        'wearing' => 'Wearing Course',
        'shoulder' => 'Shoulder',
        'drain' => 'Drainage Layer',
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
