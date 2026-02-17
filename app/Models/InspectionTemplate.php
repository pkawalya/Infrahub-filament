<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class InspectionTemplate extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static array $types = [
        'safety' => 'Safety Inspection',
        'quality' => 'Quality Inspection',
        'environmental' => 'Environmental',
        'housekeeping' => 'Housekeeping',
        'fire_safety' => 'Fire Safety',
        'scaffold' => 'Scaffold Inspection',
        'electrical' => 'Electrical Safety',
        'ppe' => 'PPE Compliance',
        'custom' => 'Custom',
    ];

    public function checklistItems()
    {
        return $this->hasMany(InspectionChecklistItem::class)->orderBy('sort_order');
    }

    public function inspections()
    {
        return $this->hasMany(SafetyInspection::class);
    }
}
