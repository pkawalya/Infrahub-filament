<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rfi extends Model
{
    use SoftDeletes, BelongsToCompany, LogsActivity;

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
        'cost_impact',
        'schedule_impact',
        'raised_by',
        'assigned_to',
        'answered_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'answered_at' => 'datetime',
    ];

    public static array $statuses = [
        'draft' => 'Draft',
        'open' => 'Open',
        'under_review' => 'Under Review',
        'answered' => 'Answered',
        'closed' => 'Closed',
        'void' => 'Void',
    ];

    public static array $priorities = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'urgent' => 'Urgent',
    ];

    public function project()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'raised_by');
    }

    public function raisedBy()
    {
        return $this->belongsTo(User::class, 'raised_by');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
