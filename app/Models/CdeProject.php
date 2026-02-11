<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CdeProject extends Model
{
    use SoftDeletes, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'description',
        'client_id',
        'manager_id',
        'status',
        'start_date',
        'end_date',
        'budget',
        'address',
        'city',
        'country',
        'image',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
    ];

    public static array $statuses = [
        'planning' => 'Planning',
        'active' => 'Active',
        'on_hold' => 'On Hold',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
    public function folders()
    {
        return $this->hasMany(CdeFolder::class);
    }
    public function documents()
    {
        return $this->hasMany(CdeDocument::class);
    }
    public function rfis()
    {
        return $this->hasMany(Rfi::class);
    }
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
    public function boqs()
    {
        return $this->hasMany(Boq::class);
    }
    public function safetyIncidents()
    {
        return $this->hasMany(SafetyIncident::class);
    }

    // ─── Module Access ───────────────────────────────────────────

    public function moduleAccess()
    {
        return $this->hasMany(ProjectModuleAccess::class);
    }

    /**
     * Check if a specific module is enabled for this project.
     */
    public function hasModule(string $code): bool
    {
        return $this->moduleAccess()
            ->where('module_code', $code)
            ->where('is_enabled', true)
            ->exists();
    }

    /**
     * Enable a module for this project (only if the parent company has it).
     */
    public function enableModule(string $code, $by = null): bool
    {
        // Guard: only allow modules the parent company has enabled
        if (!$this->company->hasModule($code)) {
            return false;
        }

        $this->moduleAccess()->updateOrCreate(
            ['module_code' => $code],
            [
                'company_id' => $this->company_id,
                'is_enabled' => true,
                'enabled_at' => now(),
                'enabled_by' => $by,
                'disabled_at' => null,
            ]
        );

        return true;
    }

    /**
     * Disable a module for this project.
     */
    public function disableModule(string $code, $by = null): void
    {
        $this->moduleAccess()->where('module_code', $code)->update([
            'is_enabled' => false,
            'disabled_at' => now(),
            'disabled_by' => $by,
        ]);
    }

    /**
     * Get all enabled module codes for this project.
     */
    public function getEnabledModules(): array
    {
        return $this->moduleAccess()
            ->where('is_enabled', true)
            ->pluck('module_code')
            ->toArray();
    }

    /**
     * Get modules that the parent company has enabled (available for this project to use).
     */
    public function getAvailableModules(): array
    {
        $companyModules = $this->company->getEnabledModules();
        $allModules = Module::$availableModules;

        return collect($allModules)
            ->filter(fn($def, $code) => in_array($code, $companyModules))
            ->toArray();
    }

    /**
     * Sync selected modules for this project (bulk enable/disable).
     */
    public function syncModules(array $moduleCodes, $by = null): void
    {
        $companyModules = $this->company->getEnabledModules();

        // Enable selected modules (only those the company has)
        foreach ($moduleCodes as $code) {
            if (in_array($code, $companyModules)) {
                $this->enableModule($code, $by);
            }
        }

        // Disable any modules not in the selected list
        $this->moduleAccess()
            ->whereNotIn('module_code', $moduleCodes)
            ->update([
                'is_enabled' => false,
                'disabled_at' => now(),
                'disabled_by' => $by,
            ]);
    }
}
