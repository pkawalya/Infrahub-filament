<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable, HasPanelShield;

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'google_id',
        'company_id',
        'user_type',
        'job_title',
        'department',
        'phone',
        'avatar',
        'timezone',
        'is_active',
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

    // ─── Panel Access ────────────────────────────────────────
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->isSuperAdmin();
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

    public static array $userTypes = [
        'super_admin' => 'Super Admin',
        'company_admin' => 'Company Admin',
        'manager' => 'Manager',
        'member' => 'Team Member',
        'technician' => 'Technician',
        'client' => 'Client',
    ];
}
