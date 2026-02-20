<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;

class ServerMonitorWidget extends Widget
{
    protected string $view = 'filament.admin.widgets.server-monitor';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 7;

    // Poll every 5 seconds for live data
    protected static ?string $pollingInterval = '5s';

    /**
     * Get CPU usage percentage.
     */
    public function getCpuUsage(): array
    {
        $cpuPercent = 0;
        $cpuCores = 1;
        $loadAvg = [0, 0, 0];

        try {
            // Get number of CPU cores
            if (is_readable('/proc/cpuinfo')) {
                $cpuinfo = file_get_contents('/proc/cpuinfo');
                $cpuCores = max(1, substr_count($cpuinfo, 'processor'));
            }

            // Get load averages
            if (function_exists('sys_getloadavg')) {
                $loadAvg = sys_getloadavg();
            }

            // Get CPU usage from /proc/stat
            if (is_readable('/proc/stat')) {
                $stat1 = file_get_contents('/proc/stat');
                usleep(100000); // 100ms sample
                $stat2 = file_get_contents('/proc/stat');

                $info1 = $this->parseCpuStat($stat1);
                $info2 = $this->parseCpuStat($stat2);

                $diff_idle = $info2['idle'] - $info1['idle'];
                $diff_total = $info2['total'] - $info1['total'];

                if ($diff_total > 0) {
                    $cpuPercent = round((1 - $diff_idle / $diff_total) * 100, 1);
                }
            } else {
                // Fallback: estimate from load average
                $cpuPercent = round(min(100, ($loadAvg[0] / $cpuCores) * 100), 1);
            }
        } catch (\Throwable $e) {
            // Silently fail
        }

        return [
            'percent' => max(0, min(100, $cpuPercent)),
            'cores' => $cpuCores,
            'load_avg' => array_map(fn($v) => round($v, 2), $loadAvg),
        ];
    }

    /**
     * Get memory usage.
     */
    public function getMemoryUsage(): array
    {
        $total = 0;
        $used = 0;
        $free = 0;
        $cached = 0;
        $buffers = 0;
        $available = 0;
        $swapTotal = 0;
        $swapUsed = 0;

        try {
            if (is_readable('/proc/meminfo')) {
                $meminfo = file_get_contents('/proc/meminfo');
                $lines = explode("\n", $meminfo);

                foreach ($lines as $line) {
                    if (preg_match('/^(\w+):\s+(\d+)/', $line, $matches)) {
                        $key = $matches[1];
                        $valueKb = (int) $matches[2];

                        match ($key) {
                            'MemTotal' => $total = $valueKb * 1024,
                            'MemFree' => $free = $valueKb * 1024,
                            'MemAvailable' => $available = $valueKb * 1024,
                            'Buffers' => $buffers = $valueKb * 1024,
                            'Cached' => $cached = $valueKb * 1024,
                            'SwapTotal' => $swapTotal = $valueKb * 1024,
                            'SwapFree' => $swapUsed = $swapTotal - ($valueKb * 1024),
                            default => null,
                        };
                    }
                }

                // Actual used memory (excluding buffers/cache)
                if ($available > 0) {
                    $used = $total - $available;
                } else {
                    $used = $total - $free - $buffers - $cached;
                }
            }
        } catch (\Throwable $e) {
            // Silently fail
        }

        $percent = $total > 0 ? round(($used / $total) * 100, 1) : 0;

        return [
            'percent' => $percent,
            'total' => $total,
            'used' => $used,
            'free' => $total - $used,
            'cached' => $cached + $buffers,
            'swap_total' => $swapTotal,
            'swap_used' => max(0, $swapUsed),
            'total_formatted' => $this->formatBytes($total),
            'used_formatted' => $this->formatBytes($used),
            'free_formatted' => $this->formatBytes($total - $used),
            'cached_formatted' => $this->formatBytes($cached + $buffers),
            'swap_total_formatted' => $this->formatBytes($swapTotal),
            'swap_used_formatted' => $this->formatBytes(max(0, $swapUsed)),
        ];
    }

    /**
     * Get disk usage for all mounted partitions.
     */
    public function getDiskUsage(): array
    {
        $disks = [];

        try {
            // Get mounted filesystems
            $output = shell_exec("df -B1 --output=source,size,used,avail,pcent,target 2>/dev/null | tail -n +2");

            if ($output) {
                $lines = array_filter(explode("\n", trim($output)));

                foreach ($lines as $line) {
                    $parts = preg_split('/\s+/', trim($line));

                    if (count($parts) >= 6) {
                        $source = $parts[0];
                        $mount = end($parts);

                        // Skip virtual and snap filesystems
                        if (
                            str_starts_with($source, '/dev/') &&
                            !str_starts_with($mount, '/snap/') &&
                            !str_starts_with($mount, '/boot/efi')
                        ) {
                            $total = (int) $parts[1];
                            $used = (int) $parts[2];
                            $available = (int) $parts[3];
                            $percentStr = rtrim($parts[4], '%');

                            $disks[] = [
                                'device' => $source,
                                'mount' => $mount,
                                'total' => $total,
                                'used' => $used,
                                'available' => $available,
                                'percent' => (float) $percentStr,
                                'total_formatted' => $this->formatBytes($total),
                                'used_formatted' => $this->formatBytes($used),
                                'available_formatted' => $this->formatBytes($available),
                            ];
                        }
                    }
                }
            }

            // Fallback if df didn't work
            if (empty($disks)) {
                $total = disk_total_space('/');
                $free = disk_free_space('/');
                $used = $total - $free;

                $disks[] = [
                    'device' => '/',
                    'mount' => '/',
                    'total' => $total,
                    'used' => $used,
                    'available' => $free,
                    'percent' => $total > 0 ? round(($used / $total) * 100, 1) : 0,
                    'total_formatted' => $this->formatBytes($total),
                    'used_formatted' => $this->formatBytes($used),
                    'available_formatted' => $this->formatBytes($free),
                ];
            }
        } catch (\Throwable $e) {
            // Silently fail
        }

        return $disks;
    }

    /**
     * Get system uptime.
     */
    public function getUptime(): string
    {
        try {
            if (is_readable('/proc/uptime')) {
                $uptime = (float) explode(' ', file_get_contents('/proc/uptime'))[0];

                $days = floor($uptime / 86400);
                $hours = floor(($uptime % 86400) / 3600);
                $minutes = floor(($uptime % 3600) / 60);

                $parts = [];
                if ($days > 0)
                    $parts[] = $days . 'd';
                if ($hours > 0)
                    $parts[] = $hours . 'h';
                $parts[] = $minutes . 'm';

                return implode(' ', $parts);
            }
        } catch (\Throwable $e) {
            // Silently fail
        }

        return 'N/A';
    }

    /**
     * Get system hostname.
     */
    public function getHostname(): string
    {
        return gethostname() ?: 'Unknown';
    }

    /**
     * Get OS info.
     */
    public function getOsInfo(): string
    {
        try {
            if (is_readable('/etc/os-release')) {
                $content = file_get_contents('/etc/os-release');
                if (preg_match('/PRETTY_NAME="(.+)"/', $content, $matches)) {
                    return $matches[1];
                }
            }
        } catch (\Throwable $e) {
            // Silently fail
        }

        return php_uname('s') . ' ' . php_uname('r');
    }

    /**
     * Get PHP version.
     */
    public function getPhpVersion(): string
    {
        return PHP_VERSION;
    }

    /**
     * Get top processes by CPU.
     */
    public function getTopProcesses(): array
    {
        $processes = [];

        try {
            $output = shell_exec("ps aux --sort=-%cpu 2>/dev/null | head -n 8 | tail -n +2");

            if ($output) {
                $lines = array_filter(explode("\n", trim($output)));

                foreach ($lines as $line) {
                    $parts = preg_split('/\s+/', trim($line), 11);
                    if (count($parts) >= 11) {
                        $processes[] = [
                            'user' => $parts[0],
                            'pid' => $parts[1],
                            'cpu' => (float) $parts[2],
                            'mem' => (float) $parts[3],
                            'command' => mb_substr($parts[10], 0, 60),
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            // Silently fail
        }

        return $processes;
    }

    /**
     * Parse /proc/stat for CPU timing.
     */
    private function parseCpuStat(string $content): array
    {
        $line = strtok($content, "\n");
        $parts = preg_split('/\s+/', trim($line));

        // cpu user nice system idle iowait irq softirq steal
        $user = (int) ($parts[1] ?? 0);
        $nice = (int) ($parts[2] ?? 0);
        $system = (int) ($parts[3] ?? 0);
        $idle = (int) ($parts[4] ?? 0);
        $iowait = (int) ($parts[5] ?? 0);
        $irq = (int) ($parts[6] ?? 0);
        $softirq = (int) ($parts[7] ?? 0);
        $steal = (int) ($parts[8] ?? 0);

        $total = $user + $nice + $system + $idle + $iowait + $irq + $softirq + $steal;

        return [
            'idle' => $idle + $iowait,
            'total' => $total,
        ];
    }

    /**
     * Format bytes into human-readable format.
     */
    private function formatBytes(int|float $bytes, int $precision = 1): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
