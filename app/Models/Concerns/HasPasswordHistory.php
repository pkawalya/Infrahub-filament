<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

/**
 * Prevents password reuse and tracks password change timestamps.
 *
 * Uses the password_history table to remember the last N hashes
 * (configured in config/security.php as password.prevent_reuse).
 */
trait HasPasswordHistory
{
    /**
     * Boot: When password changes, record it in history and update password_changed_at.
     */
    public static function bootHasPasswordHistory(): void
    {
        static::updating(function ($user) {
            if ($user->isDirty('password') && Schema::hasTable('password_history')) {
                $oldPassword = $user->getOriginal('password');

                if ($oldPassword) {
                    // Store old hash in history
                    DB::table('password_history')->insert([
                        'user_id' => $user->id,
                        'password_hash' => $oldPassword,
                        'created_at' => now(),
                    ]);

                    // Prune history beyond the configured limit
                    $keepCount = config('security.password.prevent_reuse', 5);
                    $oldIds = DB::table('password_history')
                        ->where('user_id', $user->id)
                        ->orderByDesc('created_at')
                        ->skip($keepCount)
                        ->pluck('id');

                    if ($oldIds->isNotEmpty()) {
                        DB::table('password_history')
                            ->whereIn('id', $oldIds)
                            ->delete();
                    }
                }

                // Update password_changed_at timestamp
                $user->password_changed_at = now();

                // Clear the force-change flag
                if ($user->must_change_password) {
                    $user->must_change_password = false;
                }
            }
        });
    }

    /**
     * Check whether a plaintext password has been used before by this user.
     *
     * @param string $newPassword Plaintext password to check
     * @return bool True if the password was previously used
     */
    public function wasPasswordUsedBefore(string $newPassword): bool
    {
        if (!Schema::hasTable('password_history')) {
            return false;
        }

        $recentHashes = DB::table('password_history')
            ->where('user_id', $this->id)
            ->orderByDesc('created_at')
            ->limit(config('security.password.prevent_reuse', 5))
            ->pluck('password_hash');

        // Also check the current password
        if (Hash::check($newPassword, $this->password)) {
            return true;
        }

        foreach ($recentHashes as $hash) {
            if (Hash::check($newPassword, $hash)) {
                return true;
            }
        }

        return false;
    }
}
