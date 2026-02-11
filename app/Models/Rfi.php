<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Rfi extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'cde_project_id',
        'rfi_number',
        'subject',
        'question',
        'answer',
        'status',
        'priority',
        'due_date',
        'submitted_by',
        'assigned_to',
        'answered_at',
    ];

    protected $casts = ['due_date' => 'date', 'answered_at' => 'datetime'];

    public static array $statuses = [
        'draft' => 'Draft',
        'open' => 'Open',
        'answered' => 'Answered',
        'closed' => 'Closed',
        'void' => 'Void',
    ];

    public function project()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }
    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
