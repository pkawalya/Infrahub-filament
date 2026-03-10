<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;

class ServerMonitorWidget extends Widget
{
    protected string $view = 'filament.admin.widgets.server-monitor';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 7;

    protected static ?string $pollingInterval = '5s';

    // ── CPU ──────────────────────────────────────────────────────
    public function getCpuUsage(): array
    {
        $cpuPercent = 0;
        $cpuCores = 1;
        $loadAvg = [0, 0, 0];

        try {
            if (is_readable('/proc/cpuinfo')) {
                $cpuinfo = file_get_contents('/proc/cpuinfo');
                $cpuCores = max(1, substr_count($cpuinfo, 'processor'));
            }

            if (function_exists('sys_getloadavg')) {
                $loadAvg = sys_getloadavg();
            }

            if (is_readable('/proc/stat')) {
                $stat1 = file_get_contents('/proc/stat');
                usleep(100000);
                $stat2 = file_get_contents('/proc/stat');

                $info1 = $this->parseCpuStat($stat1);
                $info2 = $this->parseCpuStat($stat2);

                $diff_idle = $info2['idle'] - $info1['idle'];
                $diff_total = $info2['total'] - $info1['total'];

                if ($diff_total > 0) {
                    $cpuPercent = round((1 - $diff_idle / $diff_total) * 100, 1);
                }
            } else {
                $cpuPercent = round(min(100, ($loadAvg[0] / $cpuCores) * 100), 1);
            }
        } catch (\Throwable $e) {
        }

        return [
            'percent' => max(0, min(100, $cpuPercent)),
            'cores' => $cpuCores,
            'load_avg' => array_map(fn($v) => round($v, 2), $loadAvg),
        ];
    }

    // ── Memory ──────────────────────────────────────────────────
    public function getMemoryUsage(): array
    {
        $total = $used = $free = $cached = $buffers = $available = 0;
        $swapTotal = $swapUsed = 0;

        try {
            if (is_readable('/proc/meminfo')) {
                $meminfo = file_get_contents('/proc/meminfo');
                foreach (explode("\n", $meminfo) as $line) {
                    if (preg_match('/^(\w+):\s+(\d+)/', $line, $m)) {
                        $kb = (int) $m[2];
                        match ($m[1]) {
                            'MemTotal' => $total = $kb * 1024,
                            'MemFree' => $free = $kb * 1024,
                            'MemAvailable' => $available = $kb * 1024,
                            'Buffers' => $buffers = $kb * 1024,
                            'Cached' => $cached = $kb * 1024,
                            'SwapTotal' => $swapTotal = $kb * 1024,
                            'SwapFree' => $swapUsed = $swapTotal - ($kb * 1024),
                            default => null,
                        };
                    }
                }
                $used = $available > 0 ? $total - $available : $total - $free - $buffers - $cached;
            }
        } catch (\Throwable $e) {
        }

        return [
            'percent' => $total > 0 ? round(($used / $total) * 100, 1) : 0,
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

    // ── Disk ────────────────────────────────────────────────────
    public function getDiskUsage(): array
    {
        $disks = [];
        try {
            $output = shell_exec("df -B1 --output=source,size,used,avail,pcent,target 2>/dev/null | tail -n +2");
            if ($output) {
                foreach (array_filter(explode("\n", trim($output))) as $line) {
                    $p = preg_split('/\s+/', trim($line));
                    if (count($p) >= 6) {
                        $src = $p[0];
                        $mnt = end($p);
                        if (str_starts_with($src, '/dev/') && !str_starts_with($mnt, '/snap/') && !str_starts_with($mnt, '/boot/efi')) {
                            $t = (int) $p[1];
                            $u = (int) $p[2];
                            $a = (int) $p[3];
                            $pct = (float) rtrim($p[4], '%');
                            $disks[] = [
                                'device' => $src,
                                'mount' => $mnt,
                                'total' => $t,
                                'used' => $u,
                                'available' => $a,
                                'percent' => $pct,
                                'total_formatted' => $this->formatBytes($t),
                                'used_formatted' => $this->formatBytes($u),
                                'available_formatted' => $this->formatBytes($a),
                            ];
                        }
                    }
                }
            }
            if (empty($disks)) {
                $t = disk_total_space('/');
                $f = disk_free_space('/');
                $u = $t - $f;
                $disks[] = [
                    'device' => '/',
                    'mount' => '/',
                    'total' => $t,
                    'used' => $u,
                    'available' => $f,
                    'percent' => $t > 0 ? round(($u / $t) * 100, 1) : 0,
                    'total_formatted' => $this->formatBytes($t),
                    'used_formatted' => $this->formatBytes($u),
                    'available_formatted' => $this->formatBytes($f)
                ];
            }
        } catch (\Throwable $e) {
        }
        return $disks;
    }

    // ── Network I/O ─────────────────────────────────────────────
    public function getNetworkStats(): array
    {
        $interfaces = [];
        try {
            if (is_readable('/proc/net/dev')) {
                $lines = explode("\n", trim(file_get_contents('/proc/net/dev')));
                foreach (array_slice($lines, 2) as $line) {
                    if (preg_match('/^\s*(\w+):\s+(\d+)\s+(\d+)\s+\d+\s+\d+\s+\d+\s+\d+\s+\d+\s+\d+\s+(\d+)\s+(\d+)/', $line, $m)) {
                        $iface = $m[1];
                        if ($iface === 'lo')
                            continue;
                        $rxBytes = (int) $m[2];
                        $txBytes = (int) $m[4];
                        if ($rxBytes + $txBytes > 0) {
                            $interfaces[] = [
                                'name' => $iface,
                                'rx' => $this->formatBytes($rxBytes),
                                'tx' => $this->formatBytes($txBytes),
                                'rx_raw' => $rxBytes,
                                'tx_raw' => $txBytes,
                            ];
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
        }
        return $interfaces;
    }

    // ── Service Status ──────────────────────────────────────────
    public function getServiceStatus(): array
    {
        $services = [];

        // MySQL
        try {
            \DB::connection()->getPdo();
            $services[] = ['name' => 'MySQL', 'icon' => 'database', 'status' => 'running', 'detail' => config('database.default')];
        } catch (\Throwable $e) {
            $services[] = ['name' => 'MySQL', 'icon' => 'database', 'status' => 'down', 'detail' => 'Connection failed'];
        }

        // Redis
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection();
            $redis->ping();
            $services[] = ['name' => 'Redis', 'icon' => 'bolt', 'status' => 'running', 'detail' => config('database.redis.default.host', '127.0.0.1')];
        } catch (\Throwable $e) {
            $services[] = ['name' => 'Redis', 'icon' => 'bolt', 'status' => 'down', 'detail' => 'Not available'];
        }

        // Queue (count pending jobs)
        try {
            $pending = \DB::table('jobs')->count();
            $failed = \DB::table('failed_jobs')->count();
            $services[] = [
                'name' => 'Queue',
                'icon' => 'queue',
                'status' => $failed > 0 ? 'warning' : 'running',
                'detail' => "{$pending} pending" . ($failed > 0 ? ", {$failed} failed" : '')
            ];
        } catch (\Throwable $e) {
            $services[] = ['name' => 'Queue', 'icon' => 'queue', 'status' => 'unknown', 'detail' => 'N/A'];
        }

        // Nginx/Apache
        $webserver = 'Unknown';
        try {
            $server = $_SERVER['SERVER_SOFTWARE'] ?? '';
            if (stripos($server, 'nginx') !== false)
                $webserver = 'Nginx';
            elseif (stripos($server, 'apache') !== false)
                $webserver = 'Apache';
            $services[] = ['name' => 'Web Server', 'icon' => 'globe', 'status' => 'running', 'detail' => $webserver];
        } catch (\Throwable $e) {
            $services[] = ['name' => 'Web Server', 'icon' => 'globe', 'status' => 'unknown', 'detail' => 'N/A'];
        }

        return $services;
    }

    // ── Application Metrics ─────────────────────────────────────
    public function getAppMetrics(): array
    {
        $metrics = [];

        // PHP Memory usage
        $phpMem = memory_get_usage(true);
        $phpMemPeak = memory_get_peak_usage(true);
        $metrics['php_memory'] = $this->formatBytes($phpMem);
        $metrics['php_memory_peak'] = $this->formatBytes($phpMemPeak);

        // Storage
        try {
            $storageFree = disk_free_space(storage_path());
            $metrics['storage_free'] = $this->formatBytes($storageFree);
        } catch (\Throwable $e) {
            $metrics['storage_free'] = 'N/A';
        }

        // DB connection info
        try {
            $dbSize = \DB::selectOne("SELECT ROUND(SUM(data_length + index_length), 0) as size FROM information_schema.tables WHERE table_schema = ?", [config('database.connections.mysql.database')]);
            $metrics['db_size'] = $this->formatBytes((int) ($dbSize->size ?? 0));
        } catch (\Throwable $e) {
            $metrics['db_size'] = 'N/A';
        }

        // Cache driver
        $metrics['cache_driver'] = config('cache.default');
        $metrics['session_driver'] = config('session.driver');
        $metrics['queue_driver'] = config('queue.default');

        // Laravel version
        $metrics['laravel'] = app()->version();

        return $metrics;
    }

    // ── System Info ─────────────────────────────────────────────
    public function getUptime(): string
    {
        try {
            if (is_readable('/proc/uptime')) {
                $up = (float) explode(' ', file_get_contents('/proc/uptime'))[0];
                $d = floor($up / 86400);
                $h = floor(($up % 86400) / 3600);
                $m = floor(($up % 3600) / 60);
                $parts = [];
                if ($d > 0)
                    $parts[] = $d . 'd';
                if ($h > 0)
                    $parts[] = $h . 'h';
                $parts[] = $m . 'm';
                return implode(' ', $parts);
            }
        } catch (\Throwable $e) {
        }
        return 'N/A';
    }

    public function getHostname(): string
    {
        return gethostname() ?: 'Unknown';
    }

    public function getOsInfo(): string
    {
        try {
            if (is_readable('/etc/os-release')) {
                $c = file_get_contents('/etc/os-release');
                if (preg_match('/PRETTY_NAME="(.+)"/', $c, $m))
                    return $m[1];
            }
        } catch (\Throwable $e) {
        }
        return php_uname('s') . ' ' . php_uname('r');
    }

    public function getPhpVersion(): string
    {
        return PHP_VERSION;
    }

    public function getKernelVersion(): string
    {
        return php_uname('r');
    }

    public function getServerTime(): string
    {
        return now()->format('Y-m-d H:i:s T');
    }

    // ── Top Processes ───────────────────────────────────────────
    public function getTopProcesses(): array
    {
        $procs = [];
        try {
            $out = shell_exec("ps aux --sort=-%cpu 2>/dev/null | head -n 8 | tail -n +2");
            if ($out) {
                foreach (array_filter(explode("\n", trim($out))) as $line) {
                    $p = preg_split('/\s+/', trim($line), 11);
                    if (count($p) >= 11) {
                        $procs[] = ['user' => $p[0], 'pid' => $p[1], 'cpu' => (float) $p[2], 'mem' => (float) $p[3], 'command' => mb_substr($p[10], 0, 60)];
                    }
                }
            }
        } catch (\Throwable $e) {
        }
        return $procs;
    }

    // ── Helpers ──────────────────────────────────────────────────
    private function parseCpuStat(string $content): array
    {
        $parts = preg_split('/\s+/', trim(strtok($content, "\n")));
        $vals = array_map('intval', array_slice($parts, 1, 8));
        $idle = ($vals[3] ?? 0) + ($vals[4] ?? 0);
        return ['idle' => $idle, 'total' => array_sum($vals)];
    }

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
