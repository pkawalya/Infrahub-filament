<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EquipmentFuelLog extends Model
{
    use BelongsToCompany, SoftDeletes;

    protected $fillable = [
        'company_id',
        'asset_id',
        'cde_project_id',
        'log_date',
        'liters',
        'cost_per_liter',
        'total_cost',
        'meter_reading',
        'filled_by',
        'supplier',
        'receipt_path',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'log_date' => 'date',
        'liters' => 'decimal:2',
        'cost_per_liter' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'meter_reading' => 'decimal:1',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function project()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
