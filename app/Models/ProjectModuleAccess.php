<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectModuleAccess extends Model
{
    protected $table = 'project_module_access';

    protected $fillable = [
        'company_id',
        'cde_project_id',
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

    // ─── Relationships ───────────────────────────────────────────

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function project()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }

    public function enabledByUser()
    {
        return $this->belongsTo(User::class, 'enabled_by');
    }

    public function disabledByUser()
    {
        return $this->belongsTo(User::class, 'disabled_by');
    }

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    public function scopeForModule($query, string $code)
    {
        return $query->where('module_code', $code);
    }

    // ─── Helpers ─────────────────────────────────────────────────

    /**
     * Get the module definition from the Module model registry.
     */
    public function getModuleDefinition(): ?array
    {
        return Module::$availableModules[$this->module_code] ?? null;
    }

    /**
     * Get the display name of this module.
     */
    public function getModuleNameAttribute(): string
    {
        return $this->getModuleDefinition()['name'] ?? ucfirst(str_replace('_', ' ', $this->module_code));
    }

    /**
     * Get the icon of this module.
     */
    public function getModuleIconAttribute(): ?string
    {
        return $this->getModuleDefinition()['icon'] ?? null;
    }
}
