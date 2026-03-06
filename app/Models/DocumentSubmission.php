<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class DocumentSubmission extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'cde_project_id',
        'title',
        'description',
        'discipline',
        'stage',
        'due_date',
        'status',
        'submitted_at',
        'submitted_by',
        'reviewed_at',
        'reviewed_by',
        'file_path',
        'file_name',
        'file_size',
        'review_notes',
        'rejection_reason',
    ];

    protected $casts = [
        'due_date' => 'date',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public static array $statuses = [
        'pending' => 'Pending',
        'submitted' => 'Submitted',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'overdue' => 'Overdue',
        'waived' => 'Waived',
    ];

    public static array $stages = [
        'inception' => 'Inception / Briefing',
        'design' => 'Design',
        'procurement' => 'Procurement',
        'construction' => 'Construction',
        'testing' => 'Testing & Commissioning',
        'handover' => 'Handover / Close-out',
    ];

    public static array $disciplines = [
        'general' => 'General',
        'structural' => 'Structural',
        'architectural' => 'Architectural',
        'mep' => 'MEP',
        'electrical' => 'Electrical',
        'mechanical' => 'Mechanical',
        'civil' => 'Civil',
        'geotechnical' => 'Geotechnical',
        'environmental' => 'Environmental',
        'safety' => 'Health & Safety',
        'quality' => 'Quality',
    ];

    public function project()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isOverdue(): bool
    {
        return $this->due_date
            && $this->due_date->isPast()
            && !in_array($this->status, ['submitted', 'approved', 'waived']);
    }

    public function formattedSize(): string
    {
        if (!$this->file_size)
            return '—';
        if ($this->file_size < 1024)
            return $this->file_size . ' B';
        if ($this->file_size < 1048576)
            return round($this->file_size / 1024, 1) . ' KB';
        return round($this->file_size / 1048576, 1) . ' MB';
    }
}
