<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class SubcontractorPackage extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'subcontractor_id',
        'cde_project_id',
        'title',
        'scope_of_work',
        'status',
        'contract_value',
        'paid_to_date',
        'start_date',
        'end_date',
        'progress_percent',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'contract_value' => 'decimal:2',
        'paid_to_date' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'progress_percent' => 'integer',
    ];

    public static array $statuses = [
        'draft' => 'Draft',
        'awarded' => 'Awarded',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'terminated' => 'Terminated',
    ];

    public function subcontractor()
    {
        return $this->belongsTo(Subcontractor::class);
    }

    public function project()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getBalanceDueAttribute(): float
    {
        return max(0, (float) $this->contract_value - (float) $this->paid_to_date);
    }
}
