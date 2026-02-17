<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Submittal extends Model
{
    use SoftDeletes, BelongsToCompany, LogsActivity;

    protected $fillable = [
        'company_id',
        'cde_project_id',
        'submittal_number',
        'title',
        'description',
        'type',
        'status',
        'current_revision',
        'submitted_by',
        'reviewer_id',
        'due_date',
        'reviewed_at',
        'review_comments',
    ];

    protected $casts = [
        'due_date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public static array $statuses = [
        'pending' => 'Pending',
        'under_review' => 'Under Review',
        'approved' => 'Approved',
        'approved_as_noted' => 'Approved as Noted',
        'revise_resubmit' => 'Revise & Resubmit',
        'rejected' => 'Rejected',
    ];

    public static array $types = [
        'shop_drawing' => 'Shop Drawing',
        'product_data' => 'Product Data',
        'sample' => 'Sample',
        'design_data' => 'Design Data',
        'test_report' => 'Test Report',
        'certificate' => 'Certificate',
        'o_and_m_manual' => 'O&M Manual',
        'other' => 'Other',
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
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
