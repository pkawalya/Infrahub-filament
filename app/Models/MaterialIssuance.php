<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class MaterialIssuance extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'cde_project_id',
        'issuance_number',
        'warehouse_id',
        'issued_to',
        'issued_to_name',
        'purpose',
        'status',
        'issue_date',
        'expected_return_date',
        'actual_return_date',
        'notes',
        'created_by',
        'approved_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expected_return_date' => 'date',
        'actual_return_date' => 'date',
    ];

    public static array $statuses = [
        'draft' => 'Draft',
        'issued' => 'Issued',
        'partial_return' => 'Partial Return',
        'returned' => 'Returned',
    ];

    public static array $purposes = [
        'site_use' => 'Site Use',
        'tool_checkout' => 'Tool Checkout',
        'maintenance' => 'Maintenance',
        'office_use' => 'Office Use',
        'return' => 'Return',
        'other' => 'Other',
    ];

    public function items()
    {
        return $this->hasMany(MaterialIssuanceItem::class);
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function issuedTo()
    {
        return $this->belongsTo(User::class, 'issued_to');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    public function project()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }
}
