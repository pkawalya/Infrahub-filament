<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class DeactivateDormantUsers extends Command
{
    protected $signature = 'users:deactivate-dormant
                            {--days=90 : Number of days of inactivity before deactivation}
                            {--dry-run : Show what would be deactivated without making changes}';

    protected $description = 'Deactivate users who have not logged in within the configured number of days.';

    public function handle(): int
    {
        $days  = (int) $this->option('days');
        $isDry = $this->option('dry-run');

        $query = User::where('is_active', true)
            ->whereNotIn('user_type', ['super_admin'])
            ->where(function ($q) use ($days) {
                $q->where('last_login_at', '<', now()->subDays($days))
                  ->orWhereNull('last_login_at')
                    ->where('created_at', '<', now()->subDays($days));
            });

        $count = $query->count();

        if ($count === 0) {
            $this->info('No dormant users found.');
            return self::SUCCESS;
        }

        if ($isDry) {
            $this->warn("[DRY RUN] Would deactivate {$count} dormant user(s) (inactive for {$days}+ days):");
            $query->each(fn (User $u) => $this->line("  - {$u->email} (last login: " . ($u->last_login_at?->toDateString() ?? 'never') . ')'));
            return self::SUCCESS;
        }

        $emails = $query->pluck('email')->toArray();
        $query->update(['is_active' => false]);

        Log::channel('security')->info('DORMANT_USERS_DEACTIVATED', [
            'count'  => $count,
            'emails' => $emails,
            'days'   => $days,
        ]);

        // Alert super admins
        try {
            $admins = User::where('user_type', 'super_admin')->where('is_active', true)->pluck('email')->toArray();
            if (!empty($admins)) {
                $from = config('mail.from.address', 'noreply@infrahub.click');
                $name = config('mail.from.name', 'InfraHub System');
                Mail::send([], [], function ($mail) use ($admins, $count, $days, $from, $name) {
                    $mail->to($admins)->from($from, $name)
                        ->subject("🔒 {$count} Dormant User(s) Deactivated")
                        ->html(
                            '<div style="font-family:sans-serif;padding:24px;max-width:600px;margin:auto;">'
                            . '<h2 style="color:#1f2937;">Dormant Account Deactivation Report</h2>'
                            . "<p style=\"color:#4b5563;\">The scheduled job deactivated <strong>{$count}</strong> user account(s) that had been inactive for more than <strong>{$days} days</strong>.</p>"
                            . '<p style="color:#6b7280;font-size:13px;">These accounts can be re-activated from the Admin Users panel if needed.</p>'
                            . '<p style="color:#9ca3af;font-size:12px;">Timestamp: ' . now()->toDateTimeString() . '</p>'
                            . '</div>'
                        );
                });
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send dormant-user alert: ' . $e->getMessage());
        }

        $this->info("Deactivated {$count} dormant user(s) (inactive for {$days}+ days).");
        return self::SUCCESS;
    }
}
