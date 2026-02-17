<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transmittal extends Model
{
    use SoftDeletes, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'cde_project_id',
        'transmittal_number',
        'subject',
        'description',
        'status',
        'from_user_id',
        'to_organization',
        'to_contact',
        'purpose',
        'sent_at',
        'acknowledged_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'acknowledged_at' => 'datetime',
    ];

    public static array $statuses = [
        'draft' => 'Draft',
        'sent' => 'Sent',
        'acknowledged' => 'Acknowledged',
    ];

    public static array $purposes = [
        'for_approval' => 'For Approval',
        'for_review' => 'For Review',
        'for_information' => 'For Information',
        'as_requested' => 'As Requested',
    ];

    public function project()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function items()
    {
        return $this->hasMany(TransmittalItem::class);
    }
}
