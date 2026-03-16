<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class UserInvitation extends Model
{
    protected $fillable = [
        'user_id',
        'company_id',
        'invited_by',
        'email',
        'token',
        'status',
        'expires_at',
        'accepted_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    // ─── Relationships ───────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    // ─── Scopes ──────────────────────────────────────────────

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeValid(Builder $query): Builder
    {
        return $query->where('status', 'pending')
            ->where('expires_at', '>', now());
    }

    // ─── Helpers ─────────────────────────────────────────────

    /**
     * Create a new invitation for a user.
     */
    public static function createForUser(User $user, ?int $invitedBy = null, int $expiryDays = 7): self
    {
        // Revoke any existing pending invitations for this user
        static::where('user_id', $user->id)
            ->where('status', 'pending')
            ->update(['status' => 'revoked']);

        return static::create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'invited_by' => $invitedBy ?? auth()->id(),
            'email' => $user->email,
            'token' => Str::random(64),
            'status' => 'pending',
            'expires_at' => now()->addDays($expiryDays),
        ]);
    }

    /**
     * Find a valid invitation by token.
     */
    public static function findByToken(string $token): ?self
    {
        return static::where('token', $token)
            ->valid()
            ->first();
    }

    /**
     * Check if the invitation is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if the invitation is still valid.
     */
    public function isValid(): bool
    {
        return $this->status === 'pending' && !$this->isExpired();
    }

    /**
     * Accept the invitation.
     */
    public function accept(): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        // Mark user's email as verified
        $this->user->update([
            'email_verified_at' => now(),
        ]);

        return true;
    }

    /**
     * Get the accept URL for this invitation.
     */
    public function getAcceptUrl(): string
    {
        return url("/invitation/accept/{$this->token}");
    }
}
