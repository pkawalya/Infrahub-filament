<?php

namespace App\Console\Commands;

use App\Models\BlockedIp;
use Illuminate\Console\Command;

class ManageBlockedIps extends Command
{
    protected $signature = 'ip:manage
        {action : block|unblock|list|flush-expired}
        {ip? : IP address or CIDR range (required for block/unblock)}
        {--reason= : Reason for blocking}
        {--expires= : Expiry in hours (default: permanent)}';

    protected $description = 'Manage blocked IP addresses (block, unblock, list, flush-expired)';

    public function handle(): int
    {
        $action = $this->argument('action');

        return match ($action) {
            'block' => $this->blockIp(),
            'unblock' => $this->unblockIp(),
            'list' => $this->listBlocked(),
            'flush-expired' => $this->flushExpired(),
            default => $this->error("Unknown action: {$action}. Use: block, unblock, list, flush-expired") ?? 1,
        };
    }

    protected function blockIp(): int
    {
        $ip = $this->argument('ip');
        if (!$ip) {
            $this->error('IP address is required for blocking.');
            return 1;
        }

        $cidr = str_contains($ip, '/') ? $ip : null;
        $ipAddress = str_contains($ip, '/') ? explode('/', $ip)[0] : $ip;

        $expiresAt = $this->option('expires')
            ? now()->addHours((int) $this->option('expires'))
            : null;

        $record = BlockedIp::updateOrCreate(
            ['ip_address' => $ipAddress, 'cidr_range' => $cidr],
            [
                'reason' => $this->option('reason') ?? 'Blocked via CLI',
                'blocked_by' => 'artisan',
                'expires_at' => $expiresAt,
                'is_active' => true,
            ]
        );

        BlockedIp::clearCache($ipAddress);

        $expiry = $expiresAt ? $expiresAt->diffForHumans() : 'permanent';
        $this->info("✅ Blocked: {$ip} ({$expiry})");

        return 0;
    }

    protected function unblockIp(): int
    {
        $ip = $this->argument('ip');
        if (!$ip) {
            $this->error('IP address is required for unblocking.');
            return 1;
        }

        $count = BlockedIp::where('ip_address', $ip)
            ->orWhere('cidr_range', $ip)
            ->update(['is_active' => false]);

        BlockedIp::clearCache($ip);

        if ($count) {
            $this->info("✅ Unblocked: {$ip}");
        } else {
            $this->warn("No matching block found for: {$ip}");
        }

        return 0;
    }

    protected function listBlocked(): int
    {
        $blocks = BlockedIp::active()->orderByDesc('created_at')->get();

        if ($blocks->isEmpty()) {
            $this->info('No active IP blocks.');

            // Also show config-based blocks
            $configBlocks = config('security.ip_blocking.blocked_ips', []);
            if (!empty($configBlocks)) {
                $this->newLine();
                $this->info('Config-based blocks (.env BLOCKED_IPS):');
                foreach ($configBlocks as $ip) {
                    $this->line("  • {$ip}");
                }
            }

            return 0;
        }

        $this->table(
            ['ID', 'IP', 'CIDR', 'Reason', 'Blocked By', 'Expires', 'Hits', 'Created'],
            $blocks->map(fn($b) => [
                $b->id,
                $b->ip_address,
                $b->cidr_range ?? '—',
                substr($b->reason ?? '', 0, 30),
                $b->blocked_by ?? '—',
                $b->expires_at?->diffForHumans() ?? 'Never',
                $b->hit_count,
                $b->created_at->diffForHumans(),
            ])
        );

        $configBlocks = config('security.ip_blocking.blocked_ips', []);
        if (!empty($configBlocks)) {
            $this->newLine();
            $this->info('Config-based blocks (.env):');
            foreach ($configBlocks as $ip) {
                $this->line("  • {$ip}");
            }
        }

        return 0;
    }

    protected function flushExpired(): int
    {
        $count = BlockedIp::expired()->update(['is_active' => false]);
        BlockedIp::clearAllCaches();

        $this->info("✅ Deactivated {$count} expired block(s).");

        return 0;
    }
}
