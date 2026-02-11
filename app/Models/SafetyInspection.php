<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class SafetyInspection extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'cde_project_id',
        'inspection_template_id',
        'inspection_number',
        'title',
        'type',
        'status',
        'scheduled_date',
        'completed_date',
        'location',
        'score',
        'findings',
        'notes',
        'inspector_id',
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'completed_date' => 'datetime',
        'findings' => 'array',
    ];

    public function project()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }
    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }
}
