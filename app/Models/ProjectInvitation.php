<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ProjectInvitation extends Model
{
    protected $fillable = [
        'company_id',
        'cde_project_id',
        'invited_by',
        'email',
        'name',
        'role',
        'token',
        'status',
        'accepted_at',
        'expires_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public static array $roles = [
        'member' => 'Team Member',
        'engineer' => 'Engineer',
        'supervisor' => 'Site Supervisor',
        'viewer' => 'Viewer (Read Only)',
    ];

    public static array $statuses = [
        'pending' => 'Pending',
        'accepted' => 'Accepted',
        'expired' => 'Expired',
        'revoked' => 'Revoked',
    ];

    // ── Relationships ───────────────────────────────────────

    public function project(): BelongsTo
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    // ── Factory ─────────────────────────────────────────────

    /**
     * Create a new invitation for an email to join a project.
     */
    public static function createInvitation(
        int $companyId,
        int $projectId,
        string $email,
        string $role = 'member',
        ?string $name = null,
        ?int $invitedBy = null,
    ): self {
        // Revoke any existing pending invite for same email + project
        static::where('cde_project_id', $projectId)
            ->where('email', $email)
            ->where('status', 'pending')
            ->update(['status' => 'revoked']);

        return static::create([
            'company_id' => $companyId,
            'cde_project_id' => $projectId,
            'email' => strtolower(trim($email)),
            'name' => $name,
            'role' => $role,
            'invited_by' => $invitedBy,
            'token' => Str::random(64),
            'status' => 'pending',
            'expires_at' => now()->addDays(14),
        ]);
    }

    // ── Helpers ──────────────────────────────────────────────

    /**
     * Check if this invitation is still valid to accept.
     */
    public function isValid(): bool
    {
        return $this->status === 'pending'
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    /**
     * Accept this invitation and add the user to the project.
     */
    public function accept(User $user): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        // Add user to the project (if not already a member)
        \DB::table('cde_project_members')->updateOrInsert(
            [
                'cde_project_id' => $this->cde_project_id,
                'user_id' => $user->id,
            ],
            [
                'role' => $this->role,
                'invited_by' => $this->invited_by,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        // Mark invitation as accepted
        $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        return true;
    }

    /**
     * Find a valid invitation by token.
     */
    public static function findByToken(string $token): ?self
    {
        return static::where('token', $token)
            ->where('status', 'pending')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->first();
    }

    /**
     * Get the accept URL for this invitation.
     */
    public function getAcceptUrl(): string
    {
        return url("/project-invite/{$this->token}");
    }
}
