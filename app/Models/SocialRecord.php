<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class SocialRecord extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'cde_project_id',
        'record_number',
        'title',
        'category',
        'priority',
        'status',
        'description',
        'affected_party',
        'location',
        'record_date',
        'resolution_date',
        'resolution_notes',
        'follow_up_actions',
        'reported_by',
        'assigned_to',
    ];

    protected $casts = [
        'record_date' => 'date',
        'resolution_date' => 'date',
    ];

    public static array $categories = [
        'grievance' => 'Grievance / Complaint',
        'stakeholder_engagement' => 'Stakeholder Engagement',
        'labour_welfare' => 'Labour Welfare',
        'training' => 'Training & Awareness',
        'csr_activity' => 'CSR Activity',
        'community_impact' => 'Community Impact',
        'land_resettlement' => 'Land / Resettlement',
        'gender_diversity' => 'Gender & Diversity',
    ];

    public static array $priorities = [
        'low' => 'Low',
        'normal' => 'Normal',
        'high' => 'High',
        'urgent' => 'Urgent',
    ];

    public static array $statuses = [
        'open' => 'Open',
        'in_progress' => 'In Progress',
        'resolved' => 'Resolved',
        'closed' => 'Closed',
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
