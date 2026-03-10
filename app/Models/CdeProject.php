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
        'billing_status',
        'monthly_rate',
        'billing_started_at',
        'billing_paused_at',
        'billing_notes',
        'start_date',
        'end_date',
        'budget',
        'currency',
        'currency_symbol',
        'currency_position',
        'address',
        'city',
        'country',
        'image',
        // Energy project fields
        'project_type',
        'energy_sector',
        'capacity_mw',
        'voltage_level',
        'grid_connection_point',
        'commissioning_status',
        'commercial_operation_date',
        'regulatory_license',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
        'monthly_rate' => 'decimal:2',
        'billing_started_at' => 'datetime',
        'billing_paused_at' => 'datetime',
        'capacity_mw' => 'decimal:2',
        'commercial_operation_date' => 'date',
    ];

    // position: 'before' = $100, 'after' = 100 UGX
    public static array $currencies = [
        'USD' => ['name' => 'US Dollar', 'symbol' => '$', 'position' => 'before'],
        'UGX' => ['name' => 'Ugandan Shilling', 'symbol' => 'UGX', 'position' => 'after'],
        'KES' => ['name' => 'Kenyan Shilling', 'symbol' => 'KES', 'position' => 'after'],
        'TZS' => ['name' => 'Tanzanian Shilling', 'symbol' => 'TZS', 'position' => 'after'],
        'RWF' => ['name' => 'Rwandan Franc', 'symbol' => 'RWF', 'position' => 'after'],
        'GBP' => ['name' => 'British Pound', 'symbol' => '£', 'position' => 'before'],
        'EUR' => ['name' => 'Euro', 'symbol' => '€', 'position' => 'after'],
        'ZAR' => ['name' => 'South African Rand', 'symbol' => 'R', 'position' => 'before'],
        'NGN' => ['name' => 'Nigerian Naira', 'symbol' => '₦', 'position' => 'before'],
        'GHS' => ['name' => 'Ghanaian Cedi', 'symbol' => 'GH₵', 'position' => 'before'],
        'ETB' => ['name' => 'Ethiopian Birr', 'symbol' => 'Br', 'position' => 'after'],
        'AED' => ['name' => 'UAE Dirham', 'symbol' => 'AED', 'position' => 'after'],
        'SAR' => ['name' => 'Saudi Riyal', 'symbol' => 'SAR', 'position' => 'after'],
        'INR' => ['name' => 'Indian Rupee', 'symbol' => '₹', 'position' => 'before'],
        'CNY' => ['name' => 'Chinese Yuan', 'symbol' => '¥', 'position' => 'before'],
    ];

    public static array $statuses = [
        'planning' => 'Planning',
        'active' => 'Active',
        'on_hold' => 'On Hold',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    public static array $projectTypes = [
        'building' => 'Building',
        'road' => 'Road & Highway',
        'energy' => 'Energy & Power',
        'water' => 'Water & Sanitation',
        'telecom' => 'Telecommunications',
        'industrial' => 'Industrial',
        'oil_gas' => 'Oil & Gas',
        'mining' => 'Mining',
        'other' => 'Other',
    ];

    public static array $energySectors = [
        'solar' => 'Solar PV',
        'wind' => 'Wind',
        'hydro' => 'Hydroelectric',
        'thermal' => 'Thermal / Gas',
        'geothermal' => 'Geothermal',
        'biomass' => 'Biomass',
        'oil_gas' => 'Oil & Gas',
        'transmission' => 'Transmission Lines',
        'distribution' => 'Distribution Network',
        'substation' => 'Substation',
    ];

    public static array $commissioningStatuses = [
        'not_started' => 'Not Started',
        'pre_commissioning' => 'Pre-Commissioning',
        'mechanical_completion' => 'Mechanical Completion',
        'energization' => 'Energization',
        'hot_commissioning' => 'Hot Commissioning',
        'performance_test' => 'Performance Testing',
        'pac' => 'PAC (Provisional Acceptance)',
        'fac' => 'FAC (Final Acceptance)',
    ];

    public static array $billingStatuses = [
        'active' => 'Active (Billed)',
        'paused' => 'Paused (Not Billed)',
        'archived' => 'Archived',
    ];

    // ─── Billing Helpers ────────────────────────────────────────

    public function isBillable(): bool
    {
        return $this->billing_status === 'active';
    }

    public function pauseBilling(?string $reason = null): void
    {
        $this->update([
            'billing_status' => 'paused',
            'billing_paused_at' => now(),
            'billing_notes' => $reason,
        ]);
    }

    public function resumeBilling(): void
    {
        $this->update([
            'billing_status' => 'active',
            'billing_paused_at' => null,
        ]);
    }

    /**
     * Get the currency symbol for this project.
     * Falls back to company currency, then '$'.
     */
    public function getCurrencySymbol(): string
    {
        if ($this->currency_symbol)
            return $this->currency_symbol;
        if ($this->currency && isset(self::$currencies[$this->currency])) {
            return self::$currencies[$this->currency]['symbol'];
        }
        return $this->company?->currency_symbol ?? $this->company?->currency ?? '$';
    }

    /**
     * Get the currency position for this project ('before' or 'after').
     */
    public function getCurrencyPosition(): string
    {
        if ($this->currency_position)
            return $this->currency_position;
        if ($this->currency && isset(self::$currencies[$this->currency])) {
            return self::$currencies[$this->currency]['position'];
        }
        return $this->company?->currency_position ?? 'before';
    }

    /**
     * Format an amount with this project's currency.
     */
    public function formatCurrency(float|int|null $amount, int $decimals = 2): string
    {
        if (is_null($amount))
            return '—';
        $symbol = $this->getCurrencySymbol();
        $position = $this->getCurrencyPosition();
        $formatted = number_format($amount, $decimals);
        return $position === 'after'
            ? $formatted . ' ' . $symbol
            : $symbol . $formatted;
    }

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
    public function dailySiteLogs()
    {
        return $this->hasMany(DailySiteLog::class);
    }
    public function milestones()
    {
        return $this->hasMany(Milestone::class);
    }
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class, 'cde_project_id');
    }
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'cde_project_id');
    }
    public function submittals()
    {
        return $this->hasMany(Submittal::class);
    }
    public function transmittals()
    {
        return $this->hasMany(Transmittal::class);
    }
    public function safetyInspections()
    {
        return $this->hasMany(SafetyInspection::class);
    }
    public function snagItems()
    {
        return $this->hasMany(SnagItem::class);
    }
    public function activityLogs()
    {
        return $this->morphMany(CdeActivityLog::class, 'loggable');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'cde_project_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'cde_project_id');
    }

    public function invoicePayments()
    {
        return $this->hasMany(InvoicePayment::class, 'cde_project_id');
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
     * Result is cached per-request to avoid repeated queries.
     */
    public function getEnabledModules(): array
    {
        // Cache per-request using a property
        if (isset($this->attributes['_cached_enabled_modules'])) {
            return $this->attributes['_cached_enabled_modules'];
        }

        $modules = $this->moduleAccess()
            ->where('is_enabled', true)
            ->pluck('module_code')
            ->toArray();

        $this->attributes['_cached_enabled_modules'] = $modules;
        return $modules;
    }

    /**
     * Clear the cached enabled modules (call after enabling/disabling modules).
     */
    public function clearModuleCache(): void
    {
        unset($this->attributes['_cached_enabled_modules']);
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

        $this->clearModuleCache();
    }
}
