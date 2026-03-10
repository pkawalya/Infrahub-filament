<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subcontractor extends Model
{
    use SoftDeletes, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'contact_person',
        'email',
        'phone',
        'specialty',
        'registration_number',
        'tax_id',
        'status',
        'rating',
        'insurance_expiry',
        'license_expiry',
        'safety_certified',
        'certifications',
        'address',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'insurance_expiry' => 'date',
        'license_expiry' => 'date',
        'safety_certified' => 'boolean',
        'certifications' => 'array',
        'rating' => 'integer',
    ];

    public static array $statuses = [
        'active' => 'Active',
        'suspended' => 'Suspended',
        'blacklisted' => 'Blacklisted',
    ];

    public static array $specialties = [
        'electrical' => 'Electrical',
        'plumbing' => 'Plumbing',
        'hvac' => 'HVAC',
        'steelwork' => 'Steelwork',
        'concrete' => 'Concrete',
        'roofing' => 'Roofing',
        'painting' => 'Painting',
        'landscaping' => 'Landscaping',
        'demolition' => 'Demolition',
        'earthworks' => 'Earthworks',
        'piling' => 'Piling',
        'waterproofing' => 'Waterproofing',
        'tiling' => 'Tiling & Flooring',
        'glazing' => 'Glazing',
        'fire_protection' => 'Fire Protection',
        'other' => 'Other',
    ];

    public function packages()
    {
        return $this->hasMany(SubcontractorPackage::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isComplianceCurrent(): bool
    {
        $insOk = !$this->insurance_expiry || $this->insurance_expiry->isFuture();
        $licOk = !$this->license_expiry || $this->license_expiry->isFuture();
        return $insOk && $licOk;
    }
}
