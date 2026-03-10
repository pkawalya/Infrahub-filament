<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Drawing extends Model
{
    use SoftDeletes, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'cde_project_id',
        'drawing_number',
        'title',
        'description',
        'discipline',
        'drawing_type',
        'current_revision',
        'status',
        'scale',
        'sheet_size',
        'suitability_code',
        'originator',
        'zone',
        'level',
        'drawn_by',
        'checked_by',
        'approved_by',
        'drawn_date',
        'checked_date',
        'approved_date',
        'tags',
        'notes',
    ];

    protected $casts = [
        'drawn_date' => 'date',
        'checked_date' => 'date',
        'approved_date' => 'date',
        'tags' => 'array',
    ];

    public static array $disciplines = [
        'architectural' => 'Architectural',
        'structural' => 'Structural',
        'mechanical' => 'Mechanical',
        'electrical' => 'Electrical',
        'civil' => 'Civil',
        'plumbing' => 'Plumbing',
        'landscape' => 'Landscape',
        'fire' => 'Fire Protection',
    ];

    public static array $drawingTypes = [
        'plan' => 'Plan',
        'elevation' => 'Elevation',
        'section' => 'Section',
        'detail' => 'Detail',
        'schedule' => 'Schedule',
        'diagram' => 'Diagram',
        'as_built' => 'As-Built',
    ];

    public static array $statuses = [
        'wip' => 'Work in Progress',
        'for_review' => 'For Review',
        'approved' => 'Approved',
        'ifc' => 'Issued for Construction',
        'as_built' => 'As-Built',
        'superseded' => 'Superseded',
    ];

    public static array $suitabilityCodes = [
        'S0' => 'S0 - Work in Progress',
        'S1' => 'S1 - For Coordination',
        'S2' => 'S2 - For Information',
        'S3' => 'S3 - For Review/Comment',
        'S4' => 'S4 - For Stage Approval',
    ];

    public function project()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }
    public function drawnByUser()
    {
        return $this->belongsTo(User::class, 'drawn_by');
    }
    public function checkedByUser()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }
    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    public function revisions()
    {
        return $this->hasMany(DrawingRevision::class)->orderByDesc('revision_date');
    }
    public function latestRevision()
    {
        return $this->hasOne(DrawingRevision::class)->where('status', 'current');
    }
}
