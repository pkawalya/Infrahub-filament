<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class EquipmentAllocation extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'asset_id',
        'cde_project_id',
        'operator_id',
        'start_date',
        'end_date',
        'status',
        'daily_rate',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'daily_rate' => 'decimal:2',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function project()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

