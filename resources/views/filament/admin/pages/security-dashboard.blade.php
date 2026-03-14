<x-filament-panels::page>
    <style>
        .security-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .security-stat-card {
            background: var(--fi-body-bg, #fff);
            border: 1px solid var(--fi-hr-color, #e5e7eb);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
        }

        .dark .security-stat-card {
            background: rgba(255, 255, 255, 0.03);
            border-color: rgba(255, 255, 255, 0.08);
        }

        .stat-number {
            font-size: 32px;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 12px;
            font-weight: 500;
            color: var(--fi-text-muted, #6b7280);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-success {
            color: #10b981;
        }

        .stat-danger {
            color: #ef4444;
        }

        .stat-warning {
            color: #f59e0b;
        }

        .stat-info {
            color: #3b82f6;
        }

        .security-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .security-table th {
            text-align: left;
            padding: 10px 12px;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--fi-text-muted, #6b7280);
            border-bottom: 2px solid var(--fi-hr-color, #e5e7eb);
        }

        .dark .security-table th {
            border-bottom-color: rgba(255, 255, 255, 0.1);
        }

        .security-table td {
            padding: 10px 12px;
            border-bottom: 1px solid var(--fi-hr-color, #e5e7eb);
        }

        .dark .security-table td {
            border-bottom-color: rgba(255, 255, 255, 0.05);
        }

        .security-table tr:hover td {
            background: rgba(59, 130, 246, 0.04);
        }

        .badge-danger {
            display: inline-block;
            background: rgba(239, 68, 68, 0.12);
            color: #ef4444;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 100px;
        }

        .badge-success {
            display: inline-block;
            background: rgba(16, 185, 129, 0.12);
            color: #10b981;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 100px;
        }

        .badge-warning {
            display: inline-block;
            background: rgba(245, 158, 11, 0.12);
            color: #f59e0b;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 100px;
        }

        .section-header {
            font-size: 16px;
            font-weight: 700;
            margin: 24px 0 12px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ip-threat {
            padding: 12px 16px;
            border-radius: 10px;
            border: 1px solid rgba(239, 68, 68, 0.2);
            background: rgba(239, 68, 68, 0.04);
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .dark .ip-threat {
            background: rgba(239, 68, 68, 0.08);
            border-color: rgba(239, 68, 68, 0.15);
        }
    </style>

    @if(!empty($stats['no_table']))
        <div style="text-align: center; padding: 48px; color: var(--fi-text-muted);">
            <x-heroicon-o-shield-exclamation style="width: 48px; height: 48px; margin: 0 auto 16px; opacity: 0.5;" />
            <p style="font-size: 16px; font-weight: 600;">Login activity table not yet created</p>
            <p style="font-size: 13px;">Run <code>php artisan migrate</code> to create the security audit tables.</p>
        </div>
    @else
        {{-- ── Stats Overview ── --}}
        <div class="security-grid">
            <div class="security-stat-card">
                <div class="stat-number stat-success">{{ $stats['success_today'] ?? 0 }}</div>
                <div class="stat-label">Logins Today</div>
            </div>
            <div class="security-stat-card">
                <div class="stat-number stat-danger">{{ $stats['failed_today'] ?? 0 }}</div>
                <div class="stat-label">Failed Today</div>
            </div>
            <div class="security-stat-card">
                <div class="stat-number stat-warning">{{ $stats['locked_today'] ?? 0 }}</div>
                <div class="stat-label">Lockouts Today</div>
            </div>
            <div class="security-stat-card">
                <div class="stat-number stat-info">{{ $stats['unique_users_today'] ?? 0 }}</div>
                <div class="stat-label">Active Users Today</div>
            </div>
            <div class="security-stat-card">
                <div class="stat-number stat-success">{{ $stats['success_7d'] ?? 0 }}</div>
                <div class="stat-label">Logins (7d)</div>
            </div>
            <div class="security-stat-card">
                <div class="stat-number stat-danger">{{ $stats['failed_7d'] ?? 0 }}</div>
                <div class="stat-label">Failed (7d)</div>
            </div>
        </div>

        {{-- ── Suspicious IPs ── --}}
        @if(!empty($suspiciousIps))
            <div class="section-header">
                <x-heroicon-o-exclamation-triangle style="width: 20px; color: #ef4444;" />
                Suspicious IP Addresses
            </div>
            @foreach($suspiciousIps as $ip)
                <div class="ip-threat">
                    <div>
                        <strong style="font-family: monospace; font-size: 14px;">{{ $ip['ip_address'] }}</strong>
                        <span style="font-size: 12px; color: var(--fi-text-muted); margin-left: 12px;">
                            Last attempt: {{ \Carbon\Carbon::parse($ip['last_attempt'])->diffForHumans() }}
                        </span>
                    </div>
                    <span class="badge-danger">{{ $ip['attempts'] }} failed attempts</span>
                </div>
            @endforeach
        @endif

        {{-- ── Recent Failed Attempts ── --}}
        <div class="section-header">
            <x-heroicon-o-x-circle style="width: 20px; color: #ef4444;" />
            Recent Failed Login Attempts
        </div>
        <div style="border-radius: 12px; overflow: hidden; border: 1px solid var(--fi-hr-color, #e5e7eb);">
            <table class="security-table">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>IP Address</th>
                        <th>Reason</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($failedAttempts as $attempt)
                        <tr>
                            <td>{{ $attempt['email'] }}</td>
                            <td style="font-family: monospace; font-size: 12px;">{{ $attempt['ip_address'] }}</td>
                            <td>
                                @php
                                    $reason = $attempt['failure_reason'] ?? 'unknown';
                                    $badgeClass = match ($reason) {
                                        'user_not_found' => 'badge-warning',
                                        'user_disabled', 'company_suspended' => 'badge-danger',
                                        default => 'badge-danger',
                                    };
                                @endphp
                                <span class="{{ $badgeClass }}">{{ str_replace('_', ' ', $reason) }}</span>
                            </td>
                            <td style="font-size: 12px; color: var(--fi-text-muted);">
                                {{ \Carbon\Carbon::parse($attempt['created_at'])->diffForHumans() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 24px; color: var(--fi-text-muted);">No failed
                                attempts recorded</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ── Recent Logins ── --}}
        <div class="section-header">
            <x-heroicon-o-check-circle style="width: 20px; color: #10b981;" />
            Recent Successful Logins
        </div>
        <div style="border-radius: 12px; overflow: hidden; border: 1px solid var(--fi-hr-color, #e5e7eb);">
            <table class="security-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>IP Address</th>
                        <th>Browser</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentLogins as $login)
                        <tr>
                            <td>{{ $login['email'] }}</td>
                            <td style="font-family: monospace; font-size: 12px;">{{ $login['ip_address'] }}</td>
                            <td
                                style="font-size: 12px; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ Str::limit($login['user_agent'] ?? '', 60) }}
                            </td>
                            <td style="font-size: 12px; color: var(--fi-text-muted);">
                                {{ \Carbon\Carbon::parse($login['created_at'])->diffForHumans() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 24px; color: var(--fi-text-muted);">No login
                                records yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="text-align: center; margin-top: 24px; font-size: 12px; color: var(--fi-text-muted);">
            Total records: {{ number_format($stats['total_records'] ?? 0) }} · Retention:
            {{ config('security.audit.log_retention_days', 90) }} days ·
            CLI: <code>php artisan security:audit --report</code>
        </div>
    @endif
</x-filament-panels::page>