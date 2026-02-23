<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certificate extends Model
{
    use SoftDeletes, BelongsToCompany;

    protected $table = 'compliance_certificates';

    protected $fillable = [
        'company_id',
        'cde_project_id',
        'contract_id',
        'vendor_id',
        'type',
        'name',
        'reference_number',
        'issuing_authority',
        'issue_date',
        'expiry_date',
        'status',
        'file_path',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    public static array $types = [
        'insurance' => 'Insurance Certificate',
        'bond' => 'Payment/Performance Bond',
        'license' => 'Professional License',
        'safety' => 'Safety Certification',
        'quality' => 'Quality Certification (ISO)',
        'environmental' => 'Environmental Permit',
        'other' => 'Other',
    ];

    public static array $statuses = [
        'active' => 'Active',
        'expired' => 'Expired',
        'revoked' => 'Revoked',
        'pending' => 'Pending',
    ];

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->expiry_date
            && $this->expiry_date->isFuture()
            && $this->expiry_date->diffInDays(now()) <= $days;
    }

    public function daysUntilExpiry(): ?int
    {
        return $this->expiry_date ? (int) now()->diffInDays($this->expiry_date, false) : null;
    }

    // ─── Relationships ───────────────────────────────────────
    public function project()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
