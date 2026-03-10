<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Company extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'website',
        'logo',
        'favicon',
        'primary_color',
        'secondary_color',
        'timezone',
        'date_format',
        'time_format',
        'currency',
        'currency_symbol',
        'currency_position',
        'currency_space',
        'subscription_id',
        'billing_cycle',
        'subscription_starts_at',
        'subscription_expires_at',
        'max_users',
        'max_projects',
        'max_storage_gb',
        'extra_users',
        'extra_projects',
        'extra_storage_gb',
        'current_storage_bytes',
        'is_active',
        'is_trial',
        'trial_ends_at',
        'activated_at',
        'suspended_at',
        'suspension_reason',
        'settings',
        'configurable_options',
        'invoice_config',
        'notes',
    ];

    protected $casts = [
        'settings' => 'array',
        'configurable_options' => 'array',
        'invoice_config' => 'array',
        'subscription_starts_at' => 'datetime',
        'subscription_expires_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'activated_at' => 'datetime',
        'suspended_at' => 'datetime',
        'is_active' => 'boolean',
        'is_trial' => 'boolean',
        'currency_space' => 'boolean',
        'current_storage_bytes' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($c) => $c->slug = $c->slug ?? Str::slug($c->name));
    }

    // ─── Configurable Options ────────────────────────────────────

    /**
     * System-wide defaults for configurable dropdowns.
     * Companies can override any of these in their `configurable_options` JSON column.
     */
    public static array $defaultOptions = [
        'weather_types' => [
            'sunny' => '☀️ Sunny',
            'cloudy' => '☁️ Cloudy',
            'rainy' => '🌧️ Rainy',
            'windy' => '💨 Windy',
            'stormy' => '⛈️ Stormy',
            'foggy' => '🌫️ Foggy',
            'hot' => '🔥 Hot',
            'cold' => '❄️ Cold',
        ],
        'subcontractor_specialties' => [
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
        ],
        'tender_categories' => [
            'construction' => 'Construction',
            'renovation' => 'Renovation',
            'maintenance' => 'Maintenance',
            'supply' => 'Supply & Install',
            'design_build' => 'Design & Build',
            'civil' => 'Civil Works',
            'infrastructure' => 'Infrastructure',
            'other' => 'Other',
        ],
        'tender_sources' => [
            'public' => 'Public Tender',
            'private' => 'Private Invitation',
            'referral' => 'Referral',
            'portal' => 'Online Portal',
            'newspaper' => 'Newspaper',
            'other' => 'Other',
        ],
        'attendance_statuses' => [
            'present' => 'Present',
            'absent' => 'Absent',
            'late' => 'Late',
            'half_day' => 'Half Day',
            'leave' => 'On Leave',
        ],
        'asset_categories' => [
            'hvac' => 'HVAC',
            'electrical' => 'Electrical',
            'plumbing' => 'Plumbing',
            'mechanical' => 'Mechanical',
            'it_equipment' => 'IT Equipment',
            'vehicle' => 'Vehicle',
            'heavy_equipment' => 'Heavy Equipment',
            'generator' => 'Generator',
            'scaffolding' => 'Scaffolding',
            'other' => 'Other',
        ],
    ];

    /**
     * Get configurable options for a given key, merging company overrides with defaults.
     * Usage: $company->getOptions('weather_types')
     */
    public function getOptions(string $key): array
    {
        $companyOverrides = $this->configurable_options[$key] ?? null;
        $defaults = static::$defaultOptions[$key] ?? [];

        return $companyOverrides ?: $defaults;
    }

    /**
     * Static helper to get options for the current authenticated user's company.
     * Usage: Company::options('weather_types')
     */
    public static function options(string $key): array
    {
        $company = auth()->user()?->company;
        if ($company) {
            return $company->getOptions($key);
        }
        return static::$defaultOptions[$key] ?? [];
    }

    // ─── Relationships ───────────────────────────────────────────
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function moduleAccess()
    {
        return $this->hasMany(CompanyModuleAccess::class);
    }
    public function projects()
    {
        return $this->hasMany(CdeProject::class);
    }
    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }
    public function billingRecords()
    {
        return $this->hasMany(BillingRecord::class)->orderByDesc('period');
    }
    public function activeProjects()
    {
        return $this->projects()->where('billing_status', 'active');
    }

    // ─── Branding Helpers ────────────────────────────────────────

    public function getLogoUrl(): ?string
    {
        if (!$this->logo) {
            return null;
        }
        return \Illuminate\Support\Str::startsWith($this->logo, 'http')
            ? $this->logo
            : asset('storage/' . $this->logo);
    }

    public function getFaviconUrl(): ?string
    {
        if (!$this->favicon) {
            return null;
        }
        return \Illuminate\Support\Str::startsWith($this->favicon, 'http')
            ? $this->favicon
            : asset('storage/' . $this->favicon);
    }

    public function getBrandName(): string
    {
        return $this->name ?: config('app.name', 'InfraHub');
    }

    /**
     * Get all branding properties as an array.
     * Used by middleware, emails, printed documents, etc.
     */
    public function getBranding(): array
    {
        return [
            'name' => $this->getBrandName(),
            'logo_url' => $this->getLogoUrl(),
            'favicon_url' => $this->getFaviconUrl(),
            'primary_color' => $this->primary_color,
            'secondary_color' => $this->secondary_color,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
            'address' => $this->address,
            'city' => $this->city,
            'country' => $this->country,
        ];
    }

    /**
     * Get invoice configuration with defaults.
     */
    public function getInvoiceConfig(): array
    {
        $defaults = [
            'default_tax_rate' => 18,
            'tax_label' => 'VAT',
            'payment_terms_days' => 30,
            'default_payment_terms' => "Payment is due within 30 days of invoice date.\nLate payments may attract interest at 2% per month.",
            'default_notes' => '',
            'bank_name' => '',
            'bank_account_name' => '',
            'bank_account_number' => '',
            'bank_branch' => '',
            'bank_swift' => '',
            'show_bank_details' => true,
            'quotation_validity_days' => 30,
            'quotation_prefix' => 'QTN-',
            'invoice_prefix' => 'INV-',
            'show_logo' => true,
            'footer_text' => '',
        ];

        return array_merge($defaults, $this->invoice_config ?? []);
    }

    // ─── Module Management ───────────────────────────────────────
    public function hasModule(string $code): bool
    {
        return $this->moduleAccess()->where('module_code', $code)->where('is_enabled', true)->exists();
    }

    public function enableModule(string $code, $by = null): void
    {
        $this->moduleAccess()->updateOrCreate(
            ['module_code' => $code],
            ['is_enabled' => true, 'enabled_at' => now(), 'enabled_by' => $by, 'disabled_at' => null]
        );
    }

    public function disableModule(string $code, $by = null): void
    {
        $this->moduleAccess()->where('module_code', $code)->update([
            'is_enabled' => false,
            'disabled_at' => now(),
            'disabled_by' => $by,
        ]);
    }

    public function getEnabledModules(): array
    {
        return $this->moduleAccess()->where('is_enabled', true)->pluck('module_code')->toArray();
    }

    public function syncModulesFromSubscription(): void
    {
        foreach (($this->subscription?->included_modules ?? []) as $code) {
            $this->enableModule($code);
        }
    }

    // ─── Subscription / Billing ──────────────────────────────────
    public function isSubscriptionActive(): bool
    {
        if (!$this->is_active)
            return false;
        if ($this->billing_cycle === 'unlimited')
            return true;
        if (!$this->subscription_expires_at)
            return true;
        return $this->subscription_expires_at->isFuture();
    }

    public function isInTrial(): bool
    {
        return $this->is_trial && $this->trial_ends_at?->isFuture();
    }

    // ─── Effective Limits (plan base + extras) ────────────────────
    public function getEffectiveMaxUsers(): int
    {
        return ($this->max_users ?? 0) + ($this->extra_users ?? 0);
    }

    public function getEffectiveMaxProjects(): int
    {
        return ($this->max_projects ?? 0) + ($this->extra_projects ?? 0);
    }

    public function getEffectiveMaxStorageGb(): int
    {
        return ($this->max_storage_gb ?? 0) + ($this->extra_storage_gb ?? 0);
    }

    public function canAddUser(): bool
    {
        $limit = $this->getEffectiveMaxUsers();
        return !$limit || $this->users()->count() < $limit;
    }

    public function canAddProject(): bool
    {
        $limit = $this->getEffectiveMaxProjects();
        return !$limit || $this->projects()->count() < $limit;
    }

    public function canAddStorage(int $additionalBytes = 0): bool
    {
        $limit = $this->getEffectiveMaxStorageGb();
        if (!$limit)
            return true;

        $maxBytes = $limit * 1024 * 1024 * 1024;
        return ($this->current_storage_bytes + $additionalBytes) < $maxBytes;
    }

    public function getRemainingUsers(): int
    {
        return max(0, $this->getEffectiveMaxUsers() - $this->users()->count());
    }

    public function getRemainingProjects(): int
    {
        return max(0, $this->getEffectiveMaxProjects() - $this->projects()->count());
    }

    /**
     * Apply a subscription plan, updating base limits from the plan.
     * Keeps extra addons intact. Works with both legacy and per-project billing.
     */
    public function applyPlan(Subscription $plan, string $billingCycle = 'monthly'): void
    {
        $this->update([
            'subscription_id' => $plan->id,
            'billing_cycle' => $billingCycle,
            'max_users' => $plan->max_users,
            'max_projects' => $plan->max_projects,
            'max_storage_gb' => $plan->max_storage_gb,
            'subscription_starts_at' => now(),
            'subscription_expires_at' => $billingCycle === 'yearly' ? now()->addYear() : now()->addMonth(),
            'is_trial' => false,
        ]);

        $this->syncModulesFromSubscription();
    }

    /**
     * Get estimated monthly bill using the BillingService.
     */
    public function getEstimatedMonthlyBill(): array
    {
        return app(\App\Services\BillingService::class)->getCompanyCostSummary($this);
    }

    // ─── Actions ─────────────────────────────────────────────────
    public function suspend(string $reason = null): void
    {
        $this->update(['is_active' => false, 'suspended_at' => now(), 'suspension_reason' => $reason]);
    }

    public function activate(): void
    {
        $this->update([
            'is_active' => true,
            'suspended_at' => null,
            'suspension_reason' => null,
            'activated_at' => $this->activated_at ?? now(),
        ]);
    }

    // ─── Scopes ──────────────────────────────────────────────────
    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    // ─── Storage Usage ──────────────────────────────────────────

    /**
     * Calculate total storage usage (in bytes) from GCS for this company.
     * Results are cached for 1 hour.
     */
    public function calculateStorageUsage(): int
    {
        return cache()->remember(
            "company:{$this->id}:storage_bytes",
            now()->addHour(),
            function () {
                try {
                    $disk = \Illuminate\Support\Facades\Storage::disk('gcs');
                    $prefix = \App\Support\StoragePath::company($this->id);
                    $files = $disk->allFiles($prefix);

                    $totalBytes = 0;
                    foreach ($files as $file) {
                        $totalBytes += $disk->size($file);
                    }

                    // Also update the stored value for quick access
                    $this->updateQuietly(['current_storage_bytes' => $totalBytes]);

                    return $totalBytes;
                } catch (\Throwable $e) {
                    report($e);
                    return (int) ($this->current_storage_bytes ?? 0);
                }
            }
        );
    }

    /**
     * Get human-readable storage usage string, e.g. "12.5 MB / 5 GB".
     */
    public function getFormattedStorageUsage(): string
    {
        $bytes = $this->current_storage_bytes ?? 0;
        $maxGb = $this->getEffectiveMaxStorageGb();

        $used = $this->formatBytes($bytes);
        $limit = $maxGb > 0 ? "{$maxGb} GB" : '∞';

        return "{$used} / {$limit}";
    }

    /**
     * Get storage usage as a percentage (0-100).
     */
    public function getStorageUsagePercent(): float
    {
        $maxGb = $this->getEffectiveMaxStorageGb();
        if ($maxGb <= 0)
            return 0;

        $maxBytes = $maxGb * 1024 * 1024 * 1024;
        $used = $this->current_storage_bytes ?? 0;

        return min(100, round(($used / $maxBytes) * 100, 1));
    }

    /**
     * Format bytes into human-readable string.
     */
    public static function formatBytes(int $bytes, int $precision = 1): string
    {
        if ($bytes <= 0)
            return '0 B';

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = floor(log($bytes, 1024));
        $pow = min($pow, count($units) - 1);

        return round($bytes / (1024 ** $pow), $precision) . ' ' . $units[$pow];
    }

    // ─── Currency Formatting ─────────────────────────────────────
    /**
     * Format an amount using the company's currency settings.
     *
     * Examples: "$100.00", "$ 100.00", "100.00 UGX", "100.00UGX"
     */
    public function formatCurrency(float|int $amount, int $decimals = 2): string
    {
        $symbol = $this->currency_symbol ?? $this->currency ?? '$';
        $position = $this->currency_position ?? 'before';
        $space = $this->currency_space ?? false;
        $separator = $space ? ' ' : '';

        $formatted = number_format($amount, $decimals);

        return $position === 'before'
            ? $symbol . $separator . $formatted
            : $formatted . $separator . $symbol;
    }
}
