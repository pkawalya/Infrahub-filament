@php
    $cpu = $this->getCpuUsage();
    $memory = $this->getMemoryUsage();
    $disks = $this->getDiskUsage();
    $uptime = $this->getUptime();
    $hostname = $this->getHostname();
    $os = $this->getOsInfo();
    $php = $this->getPhpVersion();
    $processes = $this->getTopProcesses();

    // Color determination based on thresholds
    $cpuColor = $cpu['percent'] > 90 ? 'red' : ($cpu['percent'] > 70 ? 'amber' : ($cpu['percent'] > 50 ? 'sky' : 'emerald'));
    $memColor = $memory['percent'] > 90 ? 'red' : ($memory['percent'] > 70 ? 'amber' : ($memory['percent'] > 50 ? 'sky' : 'emerald'));

    $colorMap = [
        'emerald' => ['ring' => '#10b981', 'bg' => 'rgba(16, 185, 129, 0.1)', 'glow' => 'rgba(16, 185, 129, 0.3)'],
        'sky' => ['ring' => '#0ea5e9', 'bg' => 'rgba(14, 165, 233, 0.1)', 'glow' => 'rgba(14, 165, 233, 0.3)'],
        'amber' => ['ring' => '#f59e0b', 'bg' => 'rgba(245, 158, 11, 0.1)', 'glow' => 'rgba(245, 158, 11, 0.3)'],
        'red' => ['ring' => '#ef4444', 'bg' => 'rgba(239, 68, 68, 0.1)', 'glow' => 'rgba(239, 68, 68, 0.3)'],
    ];
@endphp

<x-filament-widgets::widget>
    <div class="server-monitor" wire:poll.5s>

        {{-- Header --}}
        <div class="monitor-header">
            <div class="header-left">
                <div class="header-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon-pulse">
                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2" />
                        <line x1="8" y1="21" x2="16" y2="21" />
                        <line x1="12" y1="17" x2="12" y2="21" />
                        <path d="M6 10l3-3 2 2 4-4 3 3" class="pulse-line" />
                    </svg>
                </div>
                <div>
                    <h3 class="header-title">Server Monitor</h3>
                    <p class="header-subtitle">
                        <span class="live-dot"></span>
                        <span>{{ $hostname }}</span>
                        <span class="header-sep">•</span>
                        <span>{{ $os }}</span>
                        <span class="header-sep">•</span>
                        <span>PHP {{ $php }}</span>
                    </p>
                </div>
            </div>
            <div class="header-right">
                <div class="uptime-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="badge-icon">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z"
                            clip-rule="evenodd" />
                    </svg>
                    Uptime: <strong>{{ $uptime }}</strong>
                </div>
            </div>
        </div>

        {{-- Main Gauges Row --}}
        <div class="gauges-row">

            {{-- CPU Gauge --}}
            <div class="gauge-card"
                style="--gauge-color: {{ $colorMap[$cpuColor]['ring'] }}; --gauge-bg: {{ $colorMap[$cpuColor]['bg'] }}; --gauge-glow: {{ $colorMap[$cpuColor]['glow'] }};">
                <div class="gauge-visual">
                    <svg viewBox="0 0 120 120" class="gauge-ring">
                        <circle cx="60" cy="60" r="52" fill="none" stroke="currentColor" stroke-width="8"
                            opacity="0.1" />
                        <circle cx="60" cy="60" r="52" fill="none" stroke="var(--gauge-color)" stroke-width="8"
                            stroke-dasharray="{{ 2 * pi() * 52 }}"
                            stroke-dashoffset="{{ 2 * pi() * 52 * (1 - $cpu['percent'] / 100) }}" stroke-linecap="round"
                            transform="rotate(-90 60 60)" class="gauge-progress" />
                    </svg>
                    <div class="gauge-center">
                        <span class="gauge-value">{{ $cpu['percent'] }}</span>
                        <span class="gauge-unit">%</span>
                    </div>
                </div>
                <div class="gauge-info">
                    <h4 class="gauge-label">CPU Usage</h4>
                    <div class="gauge-details">
                        <span>{{ $cpu['cores'] }} Cores</span>
                        <span class="detail-sep">•</span>
                        <span>Load: {{ implode(' / ', $cpu['load_avg']) }}</span>
                    </div>
                    <div class="gauge-bar-container">
                        <div class="gauge-bar" style="width: {{ $cpu['percent'] }}%; background: var(--gauge-color);">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Memory Gauge --}}
            <div class="gauge-card"
                style="--gauge-color: {{ $colorMap[$memColor]['ring'] }}; --gauge-bg: {{ $colorMap[$memColor]['bg'] }}; --gauge-glow: {{ $colorMap[$memColor]['glow'] }};">
                <div class="gauge-visual">
                    <svg viewBox="0 0 120 120" class="gauge-ring">
                        <circle cx="60" cy="60" r="52" fill="none" stroke="currentColor" stroke-width="8"
                            opacity="0.1" />
                        <circle cx="60" cy="60" r="52" fill="none" stroke="var(--gauge-color)" stroke-width="8"
                            stroke-dasharray="{{ 2 * pi() * 52 }}"
                            stroke-dashoffset="{{ 2 * pi() * 52 * (1 - $memory['percent'] / 100) }}"
                            stroke-linecap="round" transform="rotate(-90 60 60)" class="gauge-progress" />
                    </svg>
                    <div class="gauge-center">
                        <span class="gauge-value">{{ $memory['percent'] }}</span>
                        <span class="gauge-unit">%</span>
                    </div>
                </div>
                <div class="gauge-info">
                    <h4 class="gauge-label">Memory Usage</h4>
                    <div class="gauge-details">
                        <span>{{ $memory['used_formatted'] }} / {{ $memory['total_formatted'] }}</span>
                    </div>
                    <div class="gauge-meta">
                        <div class="meta-item">
                            <span class="meta-dot" style="background: var(--gauge-color);"></span>
                            <span>Used: {{ $memory['used_formatted'] }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-dot" style="background: #64748b;"></span>
                            <span>Cache: {{ $memory['cached_formatted'] }}</span>
                        </div>
                        @if($memory['swap_total'] > 0)
                            <div class="meta-item">
                                <span class="meta-dot" style="background: #a855f7;"></span>
                                <span>Swap: {{ $memory['swap_used_formatted'] }} /
                                    {{ $memory['swap_total_formatted'] }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Disk Usage Section --}}
        <div class="disk-section">
            <h4 class="section-title">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="section-icon">
                    <path fill-rule="evenodd"
                        d="M17 6v10a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2zM5 8h10v1H5V8z"
                        clip-rule="evenodd" />
                </svg>
                Disk Partitions
            </h4>
            <div class="disk-grid">
                @foreach($disks as $disk)
                    @php
                        $diskColor = $disk['percent'] > 90 ? 'red' : ($disk['percent'] > 75 ? 'amber' : ($disk['percent'] > 50 ? 'sky' : 'emerald'));
                    @endphp
                    <div class="disk-card"
                        style="--disk-color: {{ $colorMap[$diskColor]['ring'] }}; --disk-bg: {{ $colorMap[$diskColor]['bg'] }};">
                        <div class="disk-header">
                            <div class="disk-mount">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    class="disk-icon">
                                    <path
                                        d="M4.5 2A2.5 2.5 0 002 4.5v3.879a2.5 2.5 0 00.732 1.767l7.5 7.5a2.5 2.5 0 003.536 0l3.878-3.878a2.5 2.5 0 000-3.536l-7.5-7.5A2.5 2.5 0 008.38 2H4.5z" />
                                </svg>
                                <span class="mount-path">{{ $disk['mount'] }}</span>
                            </div>
                            <span class="disk-percent" style="color: var(--disk-color);">{{ $disk['percent'] }}%</span>
                        </div>
                        <div class="disk-bar-container">
                            <div class="disk-bar" style="width: {{ $disk['percent'] }}%; background: var(--disk-color);">
                            </div>
                        </div>
                        <div class="disk-stats">
                            <span>{{ $disk['used_formatted'] }} used</span>
                            <span>{{ $disk['available_formatted'] }} free</span>
                            <span>{{ $disk['total_formatted'] }} total</span>
                        </div>
                        <div class="disk-device">{{ $disk['device'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Top Processes --}}
        @if(count($processes) > 0)
            <div class="processes-section">
                <h4 class="section-title">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="section-icon">
                        <path fill-rule="evenodd"
                            d="M6 3.75A2.75 2.75 0 018.75 1h2.5A2.75 2.75 0 0114 3.75v.443c.572.055 1.14.122 1.706.2C17.053 4.582 18 5.75 18 7.07v3.469c0 1.126-.694 2.191-1.83 2.54-1.952.6-4.03.93-6.17.93s-4.219-.33-6.17-.93C2.694 12.73 2 11.665 2 10.539V7.07c0-1.321.947-2.489 2.294-2.676A41.047 41.047 0 016 4.193V3.75zm6.5 0v.325a41.622 41.622 0 00-5 0V3.75c0-.69.56-1.25 1.25-1.25h2.5c.69 0 1.25.56 1.25 1.25zM10 10a1 1 0 00-1 1v.01a1 1 0 001 1h.01a1 1 0 001-1V11a1 1 0 00-1-1H10z"
                            clip-rule="evenodd" />
                        <path
                            d="M3 15.055v-.684c.126.053.255.1.39.142 2.092.642 4.313.987 6.61.987 2.297 0 4.518-.345 6.61-.987.135-.041.264-.089.39-.142v.684c0 1.347-.985 2.53-2.363 2.686A41.454 41.454 0 0110 18c-1.572 0-3.114-.112-4.637-.329C3.985 17.585 3 16.402 3 15.055z" />
                    </svg>
                    Top Processes (by CPU)
                </h4>
                <div class="processes-table">
                    <div class="process-header-row">
                        <span class="col-user">User</span>
                        <span class="col-pid">PID</span>
                        <span class="col-cpu">CPU %</span>
                        <span class="col-mem">MEM %</span>
                        <span class="col-cmd">Command</span>
                    </div>
                    @foreach($processes as $proc)
                        <div class="process-row">
                            <span class="col-user">{{ $proc['user'] }}</span>
                            <span class="col-pid">{{ $proc['pid'] }}</span>
                            <span class="col-cpu">
                                <span
                                    class="cpu-badge @if($proc['cpu'] > 50) badge-danger @elseif($proc['cpu'] > 20) badge-warning @else badge-normal @endif">
                                    {{ $proc['cpu'] }}%
                                </span>
                            </span>
                            <span class="col-mem">{{ $proc['mem'] }}%</span>
                            <span class="col-cmd" title="{{ $proc['command'] }}">{{ $proc['command'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    <style>
        /* ── Server Monitor Base ── */
        .server-monitor {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
        }

        /* ── Header ── */
        .monitor-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            padding: 1.25rem 1.5rem;
            border-radius: 1rem;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.08), rgba(168, 85, 247, 0.06));
            border: 1px solid rgba(99, 102, 241, 0.15);
            margin-bottom: 1.25rem;
        }

        .dark .monitor-header {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.12), rgba(168, 85, 247, 0.08));
            border-color: rgba(99, 102, 241, 0.2);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-icon {
            width: 3rem;
            height: 3rem;
            border-radius: 0.75rem;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .header-icon svg {
            width: 1.5rem;
            height: 1.5rem;
            color: #fff;
        }

        .icon-pulse .pulse-line {
            animation: pulseLine 2s ease-in-out infinite;
        }

        @keyframes pulseLine {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.4;
            }
        }

        .header-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
            letter-spacing: -0.02em;
        }

        .dark .header-title {
            color: #f1f5f9;
        }

        .header-subtitle {
            font-size: 0.8rem;
            color: #64748b;
            margin: 0.2rem 0 0;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            flex-wrap: wrap;
        }

        .dark .header-subtitle {
            color: #94a3b8;
        }

        .live-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #10b981;
            display: inline-block;
            animation: livePulse 2s ease-in-out infinite;
            box-shadow: 0 0 6px rgba(16, 185, 129, 0.5);
        }

        @keyframes livePulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.5;
                transform: scale(0.8);
            }
        }

        .header-sep {
            color: #cbd5e1;
        }

        .dark .header-sep {
            color: #475569;
        }

        .uptime-badge {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 500;
            color: #6366f1;
            background: rgba(99, 102, 241, 0.08);
            border: 1px solid rgba(99, 102, 241, 0.15);
        }

        .dark .uptime-badge {
            color: #a5b4fc;
            background: rgba(99, 102, 241, 0.12);
            border-color: rgba(99, 102, 241, 0.2);
        }

        .badge-icon {
            width: 1rem;
            height: 1rem;
        }

        /* ── Gauges Row ── */
        .gauges-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.25rem;
            margin-bottom: 1.25rem;
        }

        .gauge-card {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            padding: 1.5rem;
            border-radius: 1rem;
            background: #fff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
        }

        .dark .gauge-card {
            background: rgba(30, 41, 59, 0.6);
            border-color: rgba(51, 65, 85, 0.5);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        .gauge-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px var(--gauge-glow);
            border-color: var(--gauge-color);
        }

        .gauge-visual {
            position: relative;
            width: 110px;
            height: 110px;
            flex-shrink: 0;
        }

        .gauge-ring {
            width: 100%;
            height: 100%;
            filter: drop-shadow(0 0 8px var(--gauge-glow));
        }

        .gauge-progress {
            transition: stroke-dashoffset 1s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .gauge-center {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1px;
        }

        .gauge-value {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--gauge-color);
            line-height: 1;
            letter-spacing: -0.04em;
        }

        .gauge-unit {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--gauge-color);
            opacity: 0.7;
            align-self: flex-start;
            margin-top: 0.3rem;
        }

        .gauge-info {
            flex: 1;
            min-width: 0;
        }

        .gauge-label {
            font-size: 0.95rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 0.35rem;
        }

        .dark .gauge-label {
            color: #f1f5f9;
        }

        .gauge-details {
            font-size: 0.78rem;
            color: #64748b;
            margin-bottom: 0.6rem;
        }

        .dark .gauge-details {
            color: #94a3b8;
        }

        .detail-sep {
            margin: 0 0.25rem;
        }

        .gauge-bar-container {
            width: 100%;
            height: 6px;
            background: rgba(0, 0, 0, 0.06);
            border-radius: 999px;
            overflow: hidden;
            margin-top: 0.5rem;
        }

        .dark .gauge-bar-container {
            background: rgba(255, 255, 255, 0.06);
        }

        .gauge-bar {
            height: 100%;
            border-radius: 999px;
            transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 0 6px var(--gauge-glow);
        }

        .gauge-meta {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
            margin-top: 0.4rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.73rem;
            color: #64748b;
        }

        .dark .meta-item {
            color: #94a3b8;
        }

        .meta-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        /* ── Disk Section ── */
        .disk-section,
        .processes-section {
            padding: 1.25rem 1.5rem;
            border-radius: 1rem;
            background: #fff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            margin-bottom: 1.25rem;
        }

        .dark .disk-section,
        .dark .processes-section {
            background: rgba(30, 41, 59, 0.6);
            border-color: rgba(51, 65, 85, 0.5);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 1rem;
        }

        .dark .section-title {
            color: #f1f5f9;
        }

        .section-icon {
            width: 1.1rem;
            height: 1.1rem;
            color: #6366f1;
        }

        .disk-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1rem;
        }

        .disk-card {
            padding: 1rem 1.25rem;
            border-radius: 0.75rem;
            background: var(--disk-bg);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.25s ease;
        }

        .dark .disk-card {
            border-color: rgba(255, 255, 255, 0.05);
        }

        .disk-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }

        .disk-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.6rem;
        }

        .disk-mount {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .disk-icon {
            width: 1rem;
            height: 1rem;
            color: var(--disk-color);
        }

        .mount-path {
            font-size: 0.85rem;
            font-weight: 600;
            color: #334155;
            font-family: 'JetBrains Mono', ui-monospace, monospace;
        }

        .dark .mount-path {
            color: #e2e8f0;
        }

        .disk-percent {
            font-size: 1.1rem;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .disk-bar-container {
            width: 100%;
            height: 8px;
            background: rgba(0, 0, 0, 0.06);
            border-radius: 999px;
            overflow: hidden;
            margin-bottom: 0.6rem;
        }

        .dark .disk-bar-container {
            background: rgba(255, 255, 255, 0.06);
        }

        .disk-bar {
            height: 100%;
            border-radius: 999px;
            transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .disk-stats {
            display: flex;
            justify-content: space-between;
            font-size: 0.72rem;
            color: #64748b;
        }

        .dark .disk-stats {
            color: #94a3b8;
        }

        .disk-device {
            font-size: 0.68rem;
            color: #94a3b8;
            margin-top: 0.4rem;
            font-family: 'JetBrains Mono', ui-monospace, monospace;
        }

        .dark .disk-device {
            color: #64748b;
        }

        /* ── Processes Table ── */
        .processes-table {
            overflow-x: auto;
        }

        .process-header-row,
        .process-row {
            display: grid;
            grid-template-columns: 100px 70px 80px 80px 1fr;
            gap: 0.5rem;
            padding: 0.6rem 0.75rem;
            align-items: center;
            font-size: 0.78rem;
        }

        .process-header-row {
            font-weight: 700;
            color: #475569;
            border-bottom: 2px solid #e2e8f0;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.7rem;
        }

        .dark .process-header-row {
            color: #94a3b8;
            border-bottom-color: rgba(51, 65, 85, 0.5);
        }

        .process-row {
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.15s ease;
        }

        .dark .process-row {
            color: #cbd5e1;
            border-bottom-color: rgba(51, 65, 85, 0.3);
        }

        .process-row:hover {
            background: rgba(99, 102, 241, 0.04);
        }

        .dark .process-row:hover {
            background: rgba(99, 102, 241, 0.08);
        }

        .process-row:last-child {
            border-bottom: none;
        }

        .col-cmd {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-family: 'JetBrains Mono', ui-monospace, monospace;
            font-size: 0.73rem;
        }

        .col-pid {
            font-family: 'JetBrains Mono', ui-monospace, monospace;
            font-size: 0.73rem;
        }

        .cpu-badge {
            padding: 0.15rem 0.5rem;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 600;
        }

        .badge-normal {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .badge-warning {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .badge-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        /* ── Responsive ── */
        @media (max-width: 640px) {
            .gauge-card {
                flex-direction: column;
                text-align: center;
            }

            .gauge-meta {
                align-items: center;
            }

            .gauges-row {
                grid-template-columns: 1fr;
            }

            .process-header-row,
            .process-row {
                grid-template-columns: 70px 60px 65px 65px 1fr;
                font-size: 0.7rem;
            }

            .monitor-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</x-filament-widgets::widget>