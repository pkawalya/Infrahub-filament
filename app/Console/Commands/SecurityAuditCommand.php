<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\PersonalAccessToken;

class SecurityAuditCommand extends Command
{
    protected $signature = 'security:audit
        {--clean : Clean expired tokens and old login records}
        {--report : Show security summary report}';

    protected $description = 'Security audit: review login activities, expired tokens, and suspicious access patterns.';

    public function handle(): int
    {
        $this->info('🔒 InfraHub Security Audit');
        $this->newLine();

        if ($this->option('report') || !$this->option('clean')) {
            $this->showReport();
        }

        if ($this->option('clean')) {
            $this->cleanExpired();
        }

        return self::SUCCESS;
    }

    protected function showReport(): void
    {
        // ── Expired Tokens ─────────────────────────────────
        $expiredTokens = PersonalAccessToken::where('expires_at', '<', now())->count();
        $activeTokens = PersonalAccessToken::where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        })->count();

        $this->components->twoColumnDetail('Active API Tokens', (string) $activeTokens);
        $this->components->twoColumnDetail('Expired API Tokens', "<fg=yellow>{$expiredTokens}</>");

        // ── Login Activities ───────────────────────────────
        if (Schema::hasTable('login_activities')) {
            $today = now()->toDateString();

            $successToday = DB::table('login_activities')
                ->whereDate('created_at', $today)->where('status', 'success')->count();
            $failedToday = DB::table('login_activities')
                ->whereDate('created_at', $today)->where('status', 'failed')->count();
            $blockedToday = DB::table('login_activities')
                ->whereDate('created_at', $today)->whereIn('status', ['locked', 'blocked'])->count();

            $this->newLine();
            $this->components->twoColumnDetail('Successful Logins Today', (string) $successToday);
            $this->components->twoColumnDetail('Failed Logins Today', $failedToday > 0 ? "<fg=yellow>{$failedToday}</>" : '0');
            $this->components->twoColumnDetail('Blocked Attempts Today', $blockedToday > 0 ? "<fg=red>{$blockedToday}</>" : '0');

            // Top failed IPs
            $topFailedIps = DB::table('login_activities')
                ->where('status', 'failed')
                ->whereDate('created_at', '>=', now()->subDays(7))
                ->groupBy('ip_address')
                ->orderByRaw('count(*) desc')
                ->limit(5)
                ->select('ip_address', DB::raw('count(*) as attempts'))
                ->get();

            if ($topFailedIps->isNotEmpty()) {
                $this->newLine();
                $this->warn('⚠ Top suspicious IPs (last 7 days):');
                foreach ($topFailedIps as $ip) {
                    $flag = $ip->attempts >= 10 ? '🔴' : ($ip->attempts >= 5 ? '🟡' : '⚪');
                    $this->line("  {$flag} {$ip->ip_address}: {$ip->attempts} failed attempts");
                }
            }
        } else {
            $this->line('  Login activity table not yet created. Run: php artisan migrate');
        }

        // ── Users with many tokens ─────────────────────────
        $heavyTokenUsers = DB::table('personal_access_tokens')
            ->groupBy('tokenable_id')
            ->havingRaw('count(*) > 5')
            ->select('tokenable_id', DB::raw('count(*) as token_count'))
            ->get();

        if ($heavyTokenUsers->isNotEmpty()) {
            $this->newLine();
            $this->warn('⚠ Users with excessive tokens:');
            foreach ($heavyTokenUsers as $row) {
                $this->line("  User #{$row->tokenable_id}: {$row->token_count} tokens");
            }
        }

        $this->newLine();
        $this->info('✅ Audit complete. Run with --clean to purge expired tokens.');
    }

    protected function cleanExpired(): void
    {
        // Clean expired tokens
        $deleted = PersonalAccessToken::where('expires_at', '<', now())->delete();
        $this->components->twoColumnDetail('Expired tokens purged', (string) $deleted);

        // Clean login records older than retention period
        if (Schema::hasTable('login_activities')) {
            $retentionDays = config('security.audit.log_retention_days', 90);
            $oldRecords = DB::table('login_activities')
                ->where('created_at', '<', now()->subDays($retentionDays))
                ->delete();
            $this->components->twoColumnDetail("Login records purged (>{$retentionDays} days)", (string) $oldRecords);
        }

        // Clean old API audit logs
        if (Schema::hasTable('api_audit_logs')) {
            $retentionDays = config('security.audit.log_retention_days', 90);
            $oldApiLogs = DB::table('api_audit_logs')
                ->where('created_at', '<', now()->subDays($retentionDays))
                ->delete();
            $this->components->twoColumnDetail("API audit logs purged (>{$retentionDays} days)", (string) $oldApiLogs);
        }

        $this->newLine();
        $this->info('🧹 Cleanup complete.');
    }
}
