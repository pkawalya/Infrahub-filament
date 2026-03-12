<?php

namespace App\Models;

use App\Observers\UserObserver;
use Database\Factories\UserFactory;
use Filament\Auth\MultiFactor\Email\Contracts\HasEmailAuthentication;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;

#[ObservedBy(UserObserver::class)]
class User extends Authenticatable implements FilamentUser, HasEmailAuthentication
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable, HasPanelShield, HasApiTokens;

    /**
     * Temporarily holds the plain-text password so the Observer
     * can include it in the welcome email. Never persisted.
     */
    public ?string $plainPassword = null;

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'must_change_password',
        'google_id',
        'company_id',
        'user_type',
        'job_title',
        'department',
        'phone',
        'avatar',
        'timezone',
        'is_active',
        'has_email_authentication',
        'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'has_email_authentication' => 'boolean',
            'must_change_password' => 'boolean',
        ];
    }

    // ─── Relationships ───────────────────────────────────────
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // ─── User Type Checks ────────────────────────────────────
    public function isSuperAdmin(): bool
    {
        return $this->user_type === 'super_admin';
    }

    public function isCompanyAdmin(): bool
    {
        return $this->user_type === 'company_admin';
    }

    public function canManageCompany(): bool
    {
        return in_array($this->user_type, ['super_admin', 'company_admin']);
    }

    public function hasModuleAccess(string $code): bool
    {
        if ($this->isSuperAdmin())
            return true;
        if (!$this->company_id || !$this->company)
            return false;
        return $this->company->hasModule($code);
    }

    /**
     * Check if user has a specific module permission.
     * Combines company-level module access with user-level granular permission.
     * Super admins and company admins bypass permission checks.
     *
     * @param string $permission e.g. 'projects.view', 'financials.approve'
     */
    public function hasModulePermission(string $permission): bool
    {
        if ($this->isSuperAdmin() || $this->isCompanyAdmin()) {
            return true;
        }

        // Check if permission exists and user has it via Spatie HasRoles
        return $this->hasPermissionTo($permission);
    }

    /**
     * Check if user can perform an action on a module.
     * Syntactic sugar: canModule('projects', 'view') === hasModulePermission('projects.view')
     */
    public function canModule(string $module, string $action = 'view'): bool
    {
        return $this->hasModulePermission("{$module}.{$action}");
    }

    // ─── Panel Access ────────────────────────────────────────
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->is_active && $this->isSuperAdmin();
        }

        if ($panel->getId() === 'client') {
            return $this->is_active && $this->user_type === 'client';
        }

        return $this->is_active;
    }

    // ─── Scopes ──────────────────────────────────────────────
    public function scopeInCompany($query, $companyId = null)
    {
        return $query->where('company_id', $companyId ?? auth()->user()?->company_id);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ─── Email 2FA (Filament Native) — MANDATORY ─────────────
    public function hasEmailAuthentication(): bool
    {
        // Enforced for all users — every login requires an email OTP code.
        return true;
    }

    public function toggleEmailAuthentication(bool $condition): void
    {
        $this->update(['has_email_authentication' => $condition]);
    }

    public static array $userTypes = [
        'super_admin' => 'Super Admin',
        'company_admin' => 'Company Admin',
        'manager' => 'Manager',
        'member' => 'Team Member',
        'technician' => 'Technician',
        'client' => 'Client',
    ];

    // ─── Password Reset ─────────────────────────────────────
    /**
     * Send a branded password reset notification using the
     * EmailTemplate system instead of Laravel's default.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }
}
