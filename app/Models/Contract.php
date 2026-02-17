<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use SoftDeletes, BelongsToCompany, LogsActivity;

    protected $fillable = [
        'company_id',
        'cde_project_id',
        'vendor_id',
        'contract_number',
        'title',
        'type',
        'status',
        'start_date',
        'end_date',
        'original_value',
        'revised_value',
        'description',
        'scope',
        'terms',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'original_value' => 'decimal:2',
        'revised_value' => 'decimal:2',
    ];

    public static array $statuses = [
        'draft' => 'Draft',
        'active' => 'Active',
        'completed' => 'Completed',
        'terminated' => 'Terminated',
        'suspended' => 'Suspended',
    ];

    public function project()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
