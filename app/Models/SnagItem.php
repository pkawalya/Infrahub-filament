<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class SnagItem extends Model
{
    use BelongsToCompany, LogsActivity;

    protected $fillable = [
        'company_id',
        'cde_project_id',
        'snag_number',
        'title',
        'description',
        'category',
        'severity',
        'status',
        'location',
        'trade',
        'due_date',
        'reported_by',
        'assigned_to',
        'resolved_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'resolved_at' => 'datetime',
    ];

    public static array $severities = [
        'minor' => 'Minor',
        'major' => 'Major',
        'critical' => 'Critical',
    ];

    public static array $statuses = [
        'open' => 'Open',
        'in_progress' => 'In Progress',
        'resolved' => 'Resolved',
        'verified' => 'Verified',
        'closed' => 'Closed',
    ];

    public static array $categories = [
        'structural' => 'Structural',
        'finishing' => 'Finishing',
        'mep' => 'MEP',
        'electrical' => 'Electrical',
        'plumbing' => 'Plumbing',
        'painting' => 'Painting',
        'waterproofing' => 'Waterproofing',
        'landscaping' => 'Landscaping',
        'other' => 'Other',
    ];

    public function project()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
