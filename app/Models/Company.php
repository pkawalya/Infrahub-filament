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
        'subscription_id',
        'billing_cycle',
        'subscription_starts_at',
        'subscription_expires_at',
        'max_users',
        'max_projects',
        'max_storage_gb',
        'current_storage_bytes',
        'is_active',
        'is_trial',
        'trial_ends_at',
        'activated_at',
        'suspended_at',
        'suspension_reason',
        'settings',
        'notes',
    ];

    protected $casts = [
        'settings' => 'array',
        'subscription_starts_at' => 'datetime',
        'subscription_expires_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'activated_at' => 'datetime',
        'suspended_at' => 'datetime',
        'is_active' => 'boolean',
        'is_trial' => 'boolean',
        'current_storage_bytes' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($c) => $c->slug = $c->slug ?? Str::slug($c->name));
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

    public function canAddUser(): bool
    {
        return $this->users()->count() < $this->max_users;
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
}
