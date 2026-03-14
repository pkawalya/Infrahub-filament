<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SecurityDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Security';
    protected static string|\UnitEnum|null $navigationGroup = 'System';
    protected static ?int $navigationSort = 10;
    protected static ?string $title = 'Security Dashboard';
    protected static ?string $slug = 'security-dashboard';
    protected string $view = 'filament.admin.pages.security-dashboard';

    public array $stats = [];
    public array $recentLogins = [];
    public array $failedAttempts = [];
    public array $suspiciousIps = [];

    public function mount(): void
    {
        $this->loadStats();
    }

    protected function loadStats(): void
    {
        if (!Schema::hasTable('login_activities')) {
            $this->stats = ['no_table' => true];
            return;
        }

        $today = now()->toDateString();
        $sevenDaysAgo = now()->subDays(7)->toDateString();

        $this->stats = [
            'success_today' => DB::table('login_activities')
                ->whereDate('created_at', $today)->where('status', 'success')->count(),
            'failed_today' => DB::table('login_activities')
                ->whereDate('created_at', $today)->where('status', 'failed')->count(),
            'locked_today' => DB::table('login_activities')
                ->whereDate('created_at', $today)->whereIn('status', ['locked', 'blocked'])->count(),
            'unique_users_today' => DB::table('login_activities')
                ->whereDate('created_at', $today)->where('status', 'success')
                ->distinct('user_id')->count('user_id'),
            'success_7d' => DB::table('login_activities')
                ->whereDate('created_at', '>=', $sevenDaysAgo)->where('status', 'success')->count(),
            'failed_7d' => DB::table('login_activities')
                ->whereDate('created_at', '>=', $sevenDaysAgo)->where('status', 'failed')->count(),
            'total_records' => DB::table('login_activities')->count(),
        ];

        // Recent successful logins
        $this->recentLogins = DB::table('login_activities')
            ->where('status', 'success')
            ->orderByDesc('created_at')
            ->limit(15)
            ->get(['user_id', 'email', 'ip_address', 'user_agent', 'created_at'])
            ->map(fn($r) => (array) $r)
            ->toArray();

        // Recent failed attempts
        $this->failedAttempts = DB::table('login_activities')
            ->where('status', 'failed')
            ->orderByDesc('created_at')
            ->limit(15)
            ->get(['email', 'ip_address', 'failure_reason', 'user_agent', 'created_at'])
            ->map(fn($r) => (array) $r)
            ->toArray();

        // Suspicious IPs (5+ failures in last 7 days)
        $this->suspiciousIps = DB::table('login_activities')
            ->where('status', 'failed')
            ->whereDate('created_at', '>=', $sevenDaysAgo)
            ->groupBy('ip_address')
            ->havingRaw('count(*) >= 5')
            ->orderByRaw('count(*) desc')
            ->limit(10)
            ->select('ip_address', DB::raw('count(*) as attempts'), DB::raw('max(created_at) as last_attempt'))
            ->get()
            ->map(fn($r) => (array) $r)
            ->toArray();
    }
}
