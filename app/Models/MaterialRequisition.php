<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialRequisition extends Model
{
    protected $fillable = [
        'company_id',
        'cde_project_id',
        'warehouse_id',
        'requisition_number',
        'requester_id',
        'status',
        'priority',
        'required_date',
        'purpose',
        'notes',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'required_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function cdeProject()
    {
        return $this->belongsTo(CdeProject::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items()
    {
        return $this->hasMany(MaterialRequisitionItem::class);
    }

    public function issuances()
    {
        return $this->hasMany(MaterialIssuance::class);
    }
}
