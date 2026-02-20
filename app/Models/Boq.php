<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Boq extends Model
{
    use SoftDeletes, BelongsToCompany;

    public static array $statuses = [
        'draft' => 'Draft',
        'submitted' => 'Submitted',
        'approved' => 'Approved',
        'priced' => 'Priced',
        'final' => 'Final',
    ];

    protected $fillable = [
        'company_id',
        'cde_project_id',
        'contract_id',
        'name',
        'boq_number',
        'description',
        'status',
        'total_value',
        'currency',
        'notes',
        'approved_by',
        'approved_at',
        'created_by',
    ];

    protected $casts = ['total_value' => 'decimal:2', 'approved_at' => 'datetime'];

    public function project()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
    public function items()
    {
        return $this->hasMany(BoqItem::class);
    }
    public function revisions()
    {
        return $this->hasMany(BoqRevision::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
