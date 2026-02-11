<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyModuleAccess extends Model
{
    protected $table = 'company_module_access';

    protected $fillable = [
        'company_id',
        'module_code',
        'is_enabled',
        'enabled_at',
        'enabled_by',
        'disabled_at',
        'disabled_by',
        'notes',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'enabled_at' => 'datetime',
        'disabled_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
