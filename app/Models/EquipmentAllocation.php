<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EquipmentAllocation extends Model
{
    use BelongsToCompany, SoftDeletes, LogsActivity;

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

    public static array $statuses = [
        'active' => 'Active',
        'returned' => 'Returned',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
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
