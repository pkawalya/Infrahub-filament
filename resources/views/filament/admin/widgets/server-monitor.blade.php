@php
    $cpu = $this->getCpuUsage();
    $memory = $this->getMemoryUsage();
    $disks = $this->getDiskUsage();
    $uptime = $this->getUptime();
    $hostname = $this->getHostname();
    $os = $this->getOsInfo();
    $php = $this->getPhpVersion();
    $kernel = $this->getKernelVersion();
    $serverTime = $this->getServerTime();
    $processes = $this->getTopProcesses();
    $network = $this->getNetworkStats();
    $services = $this->getServiceStatus();
    $app = $this->getAppMetrics();

    $cpuColor = $cpu['percent'] > 90 ? '#ef4444' : ($cpu['percent'] > 70 ? '#f59e0b' : ($cpu['percent'] > 50 ? '#3b82f6' : '#10b981'));
    $memColor = $memory['percent'] > 90 ? '#ef4444' : ($memory['percent'] > 70 ? '#f59e0b' : ($memory['percent'] > 50 ? '#3b82f6' : '#10b981'));
    $circumference = 2 * pi() * 54;
@endphp

<x-filament-widgets::widget>
<div class="sm-root" wire:poll.5s>

    {{-- ═══ Header Banner ═══ --}}
    <div class="sm-header">
        <div class="sm-header-left">
            <div class="sm-header-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="2" y="3" width="20" height="14" rx="2" />
                    <path d="M8 21h8M12 17v4" />
                    <path d="M6 10l3-3 2 2 4-4 3 3" class="sm-pulse-line" />
                </svg>
            </div>
            <div>
                <h2 class="sm-title">System Monitor</h2>
                <div class="sm-subtitle">
                    <span class="sm-live-dot"></span>
                    <span class="sm-mono">{{ $hostname }}</span>
                    <span class="sm-sep">•</span>
                    <span>{{ $os }}</span>
                </div>
            </div>
        </div>
        <div class="sm-header-badges">
            <div class="sm-badge sm-badge-uptime">
                <svg viewBox="0 0 20 20" fill="currentColor" class="sm-badge-icon"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clip-rule="evenodd" /></svg>
                <span>Uptime <strong>{{ $uptime }}</strong></span>
            </div>
            <div class="sm-badge sm-badge-time">
                <svg viewBox="0 0 20 20" fill="currentColor" class="sm-badge-icon"><path fill-rule="evenodd" d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.75 2.75 0 0118 6.75v8.5A2.75 2.75 0 0115.25 18H4.75A2.75 2.75 0 012 15.25v-8.5A2.75 2.75 0 014.75 4H5V2.75A.75.75 0 015.75 2zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75z" clip-rule="evenodd" /></svg>
                <span class="sm-mono" style="font-size:0.72rem">{{ $serverTime }}</span>
            </div>
        </div>
    </div>

    {{-- ═══ System Info Strip ═══ --}}
    <div class="sm-info-strip">
        @foreach([
                ['label' => 'Kernel', 'value' => $kernel, 'icon' => '⚙️'],
                ['label' => 'PHP', 'value' => $php, 'icon' => '🐘'],
                ['label' => 'Laravel', 'value' => $app['laravel'], 'icon' => '🔶'],
                ['label' => 'Cache', 'value' => ucfirst($app['cache_driver']), 'icon' => '⚡'],
                ['label' => 'Queue', 'value' => ucfirst($app['queue_driver']), 'icon' => '📬'],
                ['label' => 'Session', 'value' => ucfirst($app['session_driver']), 'icon' => '🔑'],
                ['label' => 'DB Size', 'value' => $app['db_size'], 'icon' => '💾'],
                ['label' => 'PHP Mem', 'value' => $app['php_memory'], 'icon' => '🧠'],
            ] as $info)
                <div class="sm-info-chip">
                    <span class="sm-info-emoji">{{ $info['icon'] }}</span>
                    <div>
                        <div class="sm-info-label">{{ $info['label'] }}</div>
                        <div class="sm-info-value sm-mono">{{ $info['value'] }}</div>
                    </div>
                </div>
        @endforeach
    </div>

    {{-- ═══ Main Gauges ═══ --}}
    <div class="sm-gauges">
        {{-- CPU Gauge --}}
        <div class="sm-gauge-card" style="--gc: {{ $cpuColor }}">
            <div class="sm-gauge-ring-wrap">
                <svg viewBox="0 0 128 128" class="sm-gauge-svg">
                    <defs>
                        <linearGradient id="cpuGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="{{ $cpuColor }}" stop-opacity="1"/>
                            <stop offset="100%" stop-color="{{ $cpuColor }}" stop-opacity="0.4"/>
                        </linearGradient>
                    </defs>
                    <circle cx="64" cy="64" r="54" fill="none" stroke="currentColor" stroke-width="7" opacity="0.08"/>
                    <circle cx="64" cy="64" r="54" fill="none" stroke="url(#cpuGrad)" stroke-width="7"
                        stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $circumference * (1 - $cpu['percent'] / 100) }}"
                        stroke-linecap="round" transform="rotate(-90 64 64)" class="sm-gauge-arc"/>
                </svg>
                <div class="sm-gauge-center">
                    <span class="sm-gauge-num">{{ $cpu['percent'] }}</span>
                    <span class="sm-gauge-pct">%</span>
                </div>
                <div class="sm-gauge-glow" style="background: {{ $cpuColor }}"></div>
            </div>
            <div class="sm-gauge-body">
                <h4 class="sm-gauge-title">CPU Usage</h4>
                <div class="sm-gauge-sub">{{ $cpu['cores'] }} Cores</div>
                <div class="sm-gauge-bar-wrap">
                    <div class="sm-gauge-bar" style="width:{{ $cpu['percent'] }}%;background:{{ $cpuColor }}"></div>
                </div>
                <div class="sm-gauge-meta">
                    <span>Load:&ensp;<strong class="sm-mono">{{ implode(' / ', $cpu['load_avg']) }}</strong></span>
                </div>
            </div>
        </div>

        {{-- Memory Gauge --}}
        <div class="sm-gauge-card" style="--gc: {{ $memColor }}">
            <div class="sm-gauge-ring-wrap">
                <svg viewBox="0 0 128 128" class="sm-gauge-svg">
                    <defs>
                        <linearGradient id="memGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="{{ $memColor }}" stop-opacity="1"/>
                            <stop offset="100%" stop-color="{{ $memColor }}" stop-opacity="0.4"/>
                        </linearGradient>
                    </defs>
                    <circle cx="64" cy="64" r="54" fill="none" stroke="currentColor" stroke-width="7" opacity="0.08"/>
                    <circle cx="64" cy="64" r="54" fill="none" stroke="url(#memGrad)" stroke-width="7"
                        stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $circumference * (1 - $memory['percent'] / 100) }}"
                        stroke-linecap="round" transform="rotate(-90 64 64)" class="sm-gauge-arc"/>
                </svg>
                <div class="sm-gauge-center">
                    <span class="sm-gauge-num">{{ $memory['percent'] }}</span>
                    <span class="sm-gauge-pct">%</span>
                </div>
                <div class="sm-gauge-glow" style="background: {{ $memColor }}"></div>
            </div>
            <div class="sm-gauge-body">
                <h4 class="sm-gauge-title">Memory</h4>
                <div class="sm-gauge-sub">{{ $memory['used_formatted'] }} / {{ $memory['total_formatted'] }}</div>
                <div class="sm-gauge-bar-wrap">
                    <div class="sm-gauge-bar" style="width:{{ $memory['percent'] }}%;background:{{ $memColor }}"></div>
                </div>
                <div class="sm-gauge-meta-grid">
                    <div><span class="sm-dot" style="background:{{ $memColor }}"></span> Used: {{ $memory['used_formatted'] }}</div>
                    <div><span class="sm-dot" style="background:#64748b"></span> Cache: {{ $memory['cached_formatted'] }}</div>
                    @if($memory['swap_total'] > 0)
                        <div><span class="sm-dot" style="background:#a855f7"></span> Swap: {{ $memory['swap_used_formatted'] }}/{{ $memory['swap_total_formatted'] }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ Services + Network Row ═══ --}}
    <div class="sm-row-2col">
        {{-- Service Status --}}
        <div class="sm-panel">
            <h4 class="sm-panel-title">
                <svg viewBox="0 0 20 20" fill="currentColor" class="sm-panel-icon"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/></svg>
                Service Health
            </h4>
            <div class="sm-services">
                @foreach($services as $svc)
                    <div class="sm-service-row">
                        <div class="sm-service-left">
                            <span class="sm-status-dot sm-status-{{ $svc['status'] }}"></span>
                            <span class="sm-service-name">{{ $svc['name'] }}</span>
                        </div>
                        <div class="sm-service-right">
                            <span class="sm-service-detail sm-mono">{{ $svc['detail'] }}</span>
                            <span class="sm-service-badge sm-badge-{{ $svc['status'] }}">{{ ucfirst($svc['status']) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Network I/O --}}
        <div class="sm-panel">
            <h4 class="sm-panel-title">
                <svg viewBox="0 0 20 20" fill="currentColor" class="sm-panel-icon"><path fill-rule="evenodd" d="M12.577 4.878a.75.75 0 01.919-.53l4.78 1.281a.75.75 0 01.531.919l-1.281 4.78a.75.75 0 01-1.449-.387l.81-3.022a19.407 19.407 0 00-5.594 5.203.75.75 0 01-1.139.093L7 10.06l-4.72 4.72a.75.75 0 01-1.06-1.06l5.25-5.25a.75.75 0 011.06 0l3.074 3.073a20.923 20.923 0 015.545-4.931l-3.042.815a.75.75 0 01-.53-.919z" clip-rule="evenodd"/></svg>
                Network I/O
            </h4>
            @if(count($network) > 0)
                <div class="sm-net-grid">
                    @foreach($network as $iface)
                        <div class="sm-net-card">
                            <div class="sm-net-name sm-mono">{{ $iface['name'] }}</div>
                            <div class="sm-net-stats">
                                <div class="sm-net-stat">
                                    <span class="sm-net-arrow sm-net-rx">↓</span>
                                    <span class="sm-net-label">RX</span>
                                    <span class="sm-mono sm-net-val">{{ $iface['rx'] }}</span>
                                </div>
                                <div class="sm-net-stat">
                                    <span class="sm-net-arrow sm-net-tx">↑</span>
                                    <span class="sm-net-label">TX</span>
                                    <span class="sm-mono sm-net-val">{{ $iface['tx'] }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="sm-empty">No network interfaces detected</div>
            @endif
        </div>
    </div>

    {{-- ═══ Disk Partitions ═══ --}}
    <div class="sm-panel">
        <h4 class="sm-panel-title">
            <svg viewBox="0 0 20 20" fill="currentColor" class="sm-panel-icon"><path fill-rule="evenodd" d="M17 6v10a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2zM5 8h10v1H5V8z" clip-rule="evenodd"/></svg>
            Disk Partitions
        </h4>
        <div class="sm-disk-grid">
            @foreach($disks as $disk)
                @php $dc = $disk['percent'] > 90 ? '#ef4444' : ($disk['percent'] > 75 ? '#f59e0b' : ($disk['percent'] > 50 ? '#3b82f6' : '#10b981')); @endphp
                <div class="sm-disk-card">
                    <div class="sm-disk-head">
                        <div class="sm-disk-mount">
                            <svg viewBox="0 0 20 20" fill="{{ $dc }}" class="sm-disk-icon"><path d="M4.5 2A2.5 2.5 0 002 4.5v3.879a2.5 2.5 0 00.732 1.767l7.5 7.5a2.5 2.5 0 003.536 0l3.878-3.878a2.5 2.5 0 000-3.536l-7.5-7.5A2.5 2.5 0 008.38 2H4.5z"/></svg>
                            <span class="sm-mono">{{ $disk['mount'] }}</span>
                        </div>
                        <span class="sm-disk-pct" style="color:{{ $dc }}">{{ $disk['percent'] }}%</span>
                    </div>
                    <div class="sm-bar-track"><div class="sm-bar-fill" style="width:{{ $disk['percent'] }}%;background:{{ $dc }}"></div></div>
                    <div class="sm-disk-stats">
                        <span>{{ $disk['used_formatted'] }} used</span>
                        <span>{{ $disk['available_formatted'] }} free</span>
                        <span>{{ $disk['total_formatted'] }} total</span>
                    </div>
                    <div class="sm-disk-dev sm-mono">{{ $disk['device'] }}</div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ═══ Top Processes ═══ --}}
    @if(count($processes) > 0)
        <div class="sm-panel sm-panel-terminal">
            <h4 class="sm-panel-title">
                <svg viewBox="0 0 20 20" fill="currentColor" class="sm-panel-icon"><path fill-rule="evenodd" d="M3.25 3A2.25 2.25 0 001 5.25v9.5A2.25 2.25 0 003.25 17h13.5A2.25 2.25 0 0019 14.75v-9.5A2.25 2.25 0 0016.75 3H3.25zM2.5 9v5.75c0 .414.336.75.75.75h13.5a.75.75 0 00.75-.75V9h-15zM4 5.25a.75.75 0 00-.75.75v.01c0 .414.336.75.75.75h.01a.75.75 0 00.75-.75V6a.75.75 0 00-.75-.75H4zM6.25 6a.75.75 0 01.75-.75h.01a.75.75 0 01.75.75v.01a.75.75 0 01-.75.75H7a.75.75 0 01-.75-.75V6zm3-.75a.75.75 0 00-.75.75v.01c0 .414.336.75.75.75h.01a.75.75 0 00.75-.75V6a.75.75 0 00-.75-.75H9.25z" clip-rule="evenodd"/></svg>
                Top Processes <span class="sm-panel-dim">(by CPU)</span>
            </h4>
            <div class="sm-procs-table">
                <div class="sm-proc-header">
                    <span class="sm-col-user">USER</span>
                    <span class="sm-col-pid">PID</span>
                    <span class="sm-col-cpu">CPU</span>
                    <span class="sm-col-mem">MEM</span>
                    <span class="sm-col-cmd">COMMAND</span>
                </div>
                @foreach($processes as $proc)
                    <div class="sm-proc-row">
                        <span class="sm-col-user">{{ $proc['user'] }}</span>
                        <span class="sm-col-pid sm-mono">{{ $proc['pid'] }}</span>
                        <span class="sm-col-cpu">
                            <span class="sm-cpu-pill @if($proc['cpu'] > 50) sm-pill-danger @elseif($proc['cpu'] > 20) sm-pill-warn @else sm-pill-ok @endif">
                                {{ $proc['cpu'] }}%
                            </span>
                        </span>
                        <span class="sm-col-mem sm-mono">{{ $proc['mem'] }}%</span>
                        <span class="sm-col-cmd sm-mono" title="{{ $proc['command'] }}">{{ $proc['command'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>

<style>
/* ══════════════════════════════════════════════════════════════
   Server Monitor - Premium Mission Control Design
   ══════════════════════════════════════════════════════════════ */

.sm-root { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
.sm-mono { font-family: 'JetBrains Mono', 'Fira Code', ui-monospace, monospace; }

/* ── Header ── */
.sm-header {
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;
    padding: 1.25rem 1.5rem; border-radius: 16px; margin-bottom: 1.25rem;
    background: linear-gradient(135deg, rgba(99,102,241,0.07) 0%, rgba(168,85,247,0.05) 100%);
    border: 1px solid rgba(99,102,241,0.12);
    backdrop-filter: blur(12px);
}
.dark .sm-header { background: linear-gradient(135deg,rgba(99,102,241,0.14),rgba(168,85,247,0.08)); border-color: rgba(99,102,241,0.2); }
.sm-header-left { display:flex; align-items:center; gap:1rem; }
.sm-header-icon {
    width:48px; height:48px; border-radius:14px; display:flex; align-items:center; justify-content:center; flex-shrink:0;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    box-shadow: 0 4px 20px rgba(99,102,241,0.35), inset 0 1px 1px rgba(255,255,255,0.2);
}
.sm-header-icon svg { width:24px; height:24px; color:#fff; }
.sm-pulse-line { animation: smPulse 2.5s ease-in-out infinite; }
@keyframes smPulse { 0%,100%{opacity:1} 50%{opacity:0.3} }
.sm-title { font-size:1.2rem; font-weight:800; color:#0f172a; margin:0; letter-spacing:-0.03em; }
.dark .sm-title { color:#f1f5f9; }
.sm-subtitle { font-size:0.78rem; color:#64748b; margin:0.15rem 0 0; display:flex; align-items:center; gap:0.4rem; flex-wrap:wrap; }
.dark .sm-subtitle { color:#94a3b8; }
.sm-live-dot { width:8px; height:8px; border-radius:50%; background:#10b981; animation: smDot 2s ease-in-out infinite; box-shadow:0 0 8px rgba(16,185,129,0.6); }
@keyframes smDot { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.4;transform:scale(0.75)} }
.sm-sep { color:#cbd5e1; } .dark .sm-sep { color:#475569; }
.sm-header-badges { display:flex; gap:0.5rem; flex-wrap:wrap; }
.sm-badge { display:flex; align-items:center; gap:0.35rem; padding:0.4rem 0.9rem; border-radius:999px; font-size:0.76rem; font-weight:500; }
.sm-badge-icon { width:14px; height:14px; }
.sm-badge-uptime { color:#6366f1; background:rgba(99,102,241,0.08); border:1px solid rgba(99,102,241,0.12); }
.sm-badge-time { color:#64748b; background:rgba(100,116,139,0.06); border:1px solid rgba(100,116,139,0.1); }
.dark .sm-badge-uptime { color:#a5b4fc; background:rgba(99,102,241,0.12); border-color:rgba(99,102,241,0.2); }
.dark .sm-badge-time { color:#94a3b8; background:rgba(100,116,139,0.1); border-color:rgba(100,116,139,0.15); }

/* ── Info Strip ── */
.sm-info-strip {
    display:grid; grid-template-columns:repeat(auto-fill, minmax(140px,1fr)); gap:0.65rem; margin-bottom:1.25rem;
}
.sm-info-chip {
    display:flex; align-items:center; gap:0.55rem; padding:0.65rem 0.9rem; border-radius:12px;
    background:#fff; border:1px solid #e2e8f0; transition: all 0.2s ease;
}
.dark .sm-info-chip { background:rgba(30,41,59,0.5); border-color:rgba(51,65,85,0.4); }
.sm-info-chip:hover { transform:translateY(-1px); box-shadow:0 4px 12px rgba(0,0,0,0.06); }
.dark .sm-info-chip:hover { box-shadow:0 4px 12px rgba(0,0,0,0.2); }
.sm-info-emoji { font-size:1.15rem; flex-shrink:0; }
.sm-info-label { font-size:0.65rem; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.05em; }
.sm-info-value { font-size:0.78rem; font-weight:600; color:#1e293b; margin-top:1px; }
.dark .sm-info-value { color:#e2e8f0; }

/* ── Gauges ── */
.sm-gauges { display:grid; grid-template-columns:repeat(auto-fit,minmax(340px,1fr)); gap:1.25rem; margin-bottom:1.25rem; }
.sm-gauge-card {
    display:flex; align-items:center; gap:1.5rem; padding:1.5rem 1.75rem; border-radius:16px;
    background:#fff; border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,0.04);
    transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
}
.dark .sm-gauge-card { background:rgba(15,23,42,0.6); border-color:rgba(51,65,85,0.4); box-shadow:0 1px 3px rgba(0,0,0,0.2); }
.sm-gauge-card:hover { transform:translateY(-3px); box-shadow:0 12px 32px rgba(0,0,0,0.08); border-color: var(--gc); }
.dark .sm-gauge-card:hover { box-shadow:0 12px 32px rgba(0,0,0,0.3); }
.sm-gauge-ring-wrap { position:relative; width:120px; height:120px; flex-shrink:0; }
.sm-gauge-svg { width:100%; height:100%; }
.sm-gauge-arc { transition: stroke-dashoffset 1.2s cubic-bezier(0.4,0,0.2,1); }
.sm-gauge-center { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; gap:1px; }
.sm-gauge-num { font-size:1.75rem; font-weight:800; color:var(--gc); line-height:1; letter-spacing:-0.04em; }
.sm-gauge-pct { font-size:0.85rem; font-weight:700; color:var(--gc); opacity:0.6; margin-top:0.3rem; }
.sm-gauge-glow { position:absolute; bottom:10px; left:50%; transform:translateX(-50%); width:60px; height:60px; border-radius:50%; opacity:0.12; filter:blur(20px); pointer-events:none; }
.sm-gauge-body { flex:1; min-width:0; }
.sm-gauge-title { font-size:1rem; font-weight:700; color:#0f172a; margin:0 0 0.3rem; } .dark .sm-gauge-title { color:#f1f5f9; }
.sm-gauge-sub { font-size:0.78rem; color:#64748b; margin-bottom:0.65rem; } .dark .sm-gauge-sub { color:#94a3b8; }
.sm-gauge-bar-wrap { width:100%; height:6px; background:rgba(0,0,0,0.05); border-radius:99px; overflow:hidden; }
.dark .sm-gauge-bar-wrap { background:rgba(255,255,255,0.06); }
.sm-gauge-bar { height:100%; border-radius:99px; transition:width 1s cubic-bezier(0.4,0,0.2,1); box-shadow:0 0 8px rgba(0,0,0,0.1); }
.sm-gauge-meta { font-size:0.76rem; color:#64748b; margin-top:0.55rem; } .dark .sm-gauge-meta { color:#94a3b8; }
.sm-gauge-meta-grid { display:flex; flex-direction:column; gap:0.25rem; margin-top:0.5rem; font-size:0.73rem; color:#64748b; } .dark .sm-gauge-meta-grid { color:#94a3b8; }
.sm-dot { display:inline-block; width:6px; height:6px; border-radius:50%; margin-right:4px; vertical-align:middle; }

/* ── Panels ── */
.sm-panel {
    padding:1.25rem 1.5rem; border-radius:16px; margin-bottom:1.25rem;
    background:#fff; border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,0.04);
}
.dark .sm-panel { background:rgba(15,23,42,0.6); border-color:rgba(51,65,85,0.4); box-shadow:0 1px 3px rgba(0,0,0,0.2); }
.sm-panel-terminal { background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%); border-color: rgba(51,65,85,0.6); }
.sm-panel-terminal .sm-panel-title, .sm-panel-terminal .sm-panel-dim { color: #94a3b8 !important; }
.sm-panel-title { display:flex; align-items:center; gap:0.5rem; font-size:0.9rem; font-weight:700; color:#0f172a; margin:0 0 1rem; }
.dark .sm-panel-title { color:#f1f5f9; }
.sm-panel-icon { width:18px; height:18px; color:#6366f1; }
.sm-panel-dim { font-weight:400; color:#94a3b8; font-size:0.8rem; }
.sm-row-2col { display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:1.25rem; margin-bottom:0; }
.sm-row-2col .sm-panel { margin-bottom:1.25rem; }

/* ── Services ── */
.sm-services { display:flex; flex-direction:column; gap:0.5rem; }
.sm-service-row { display:flex; align-items:center; justify-content:space-between; padding:0.6rem 0.75rem; border-radius:10px; transition:background 0.15s; }
.sm-service-row:hover { background:rgba(99,102,241,0.04); } .dark .sm-service-row:hover { background:rgba(99,102,241,0.08); }
.sm-service-left { display:flex; align-items:center; gap:0.6rem; }
.sm-service-right { display:flex; align-items:center; gap:0.6rem; }
.sm-service-name { font-size:0.85rem; font-weight:600; color:#334155; } .dark .sm-service-name { color:#e2e8f0; }
.sm-service-detail { font-size:0.72rem; color:#94a3b8; }
.sm-status-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; }
.sm-status-running { background:#10b981; box-shadow:0 0 8px rgba(16,185,129,0.5); animation: smDot 2s ease-in-out infinite; }
.sm-status-warning { background:#f59e0b; box-shadow:0 0 8px rgba(245,158,11,0.5); }
.sm-status-down { background:#ef4444; box-shadow:0 0 8px rgba(239,68,68,0.5); animation: smDot 1s ease-in-out infinite; }
.sm-status-unknown { background:#64748b; }
.sm-service-badge {
    padding:0.15rem 0.6rem; border-radius:99px; font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.04em;
}
.sm-badge-running { background:rgba(16,185,129,0.1); color:#10b981; }
.sm-badge-warning { background:rgba(245,158,11,0.1); color:#f59e0b; }
.sm-badge-down { background:rgba(239,68,68,0.1); color:#ef4444; }
.sm-badge-unknown { background:rgba(100,116,139,0.1); color:#64748b; }

/* ── Network ── */
.sm-net-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:0.75rem; }
.sm-net-card { padding:0.85rem 1rem; border-radius:10px; background:rgba(99,102,241,0.04); border:1px solid rgba(99,102,241,0.08); }
.dark .sm-net-card { background:rgba(99,102,241,0.06); border-color:rgba(99,102,241,0.12); }
.sm-net-name { font-size:0.8rem; font-weight:700; color:#4f46e5; margin-bottom:0.5rem; } .dark .sm-net-name { color:#818cf8; }
.sm-net-stats { display:flex; gap:1rem; }
.sm-net-stat { display:flex; align-items:center; gap:0.3rem; font-size:0.75rem; color:#64748b; } .dark .sm-net-stat { color:#94a3b8; }
.sm-net-arrow { font-size:0.9rem; font-weight:800; }
.sm-net-rx { color:#10b981; } .sm-net-tx { color:#3b82f6; }
.sm-net-label { font-weight:600; font-size:0.65rem; text-transform:uppercase; }
.sm-net-val { font-weight:600; color:#334155; } .dark .sm-net-val { color:#e2e8f0; }
.sm-empty { font-size:0.8rem; color:#94a3b8; text-align:center; padding:1.5rem; }

/* ── Disks ── */
.sm-disk-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:1rem; }
.sm-disk-card { padding:1rem 1.25rem; border-radius:12px; background:rgba(0,0,0,0.02); border:1px solid rgba(0,0,0,0.04); transition:all 0.2s; }
.dark .sm-disk-card { background:rgba(255,255,255,0.02); border-color:rgba(255,255,255,0.04); }
.sm-disk-card:hover { transform:translateY(-1px); box-shadow:0 4px 12px rgba(0,0,0,0.05); }
.sm-disk-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:0.6rem; }
.sm-disk-mount { display:flex; align-items:center; gap:0.4rem; font-size:0.85rem; font-weight:600; color:#334155; } .dark .sm-disk-mount { color:#e2e8f0; }
.sm-disk-icon { width:16px; height:16px; }
.sm-disk-pct { font-size:1.15rem; font-weight:800; letter-spacing:-0.02em; }
.sm-bar-track { width:100%; height:8px; background:rgba(0,0,0,0.05); border-radius:99px; overflow:hidden; margin-bottom:0.6rem; }
.dark .sm-bar-track { background:rgba(255,255,255,0.06); }
.sm-bar-fill { height:100%; border-radius:99px; transition:width 1s cubic-bezier(0.4,0,0.2,1); }
.sm-disk-stats { display:flex; justify-content:space-between; font-size:0.72rem; color:#64748b; } .dark .sm-disk-stats { color:#94a3b8; }
.sm-disk-dev { font-size:0.68rem; color:#94a3b8; margin-top:0.4rem; } .dark .sm-disk-dev { color:#64748b; }

/* ── Processes ── */
.sm-procs-table { overflow-x:auto; }
.sm-proc-header, .sm-proc-row { display:grid; grid-template-columns:90px 65px 75px 70px 1fr; gap:0.5rem; padding:0.6rem 0.75rem; align-items:center; }
.sm-proc-header { font-weight:700; color:#64748b; border-bottom:2px solid rgba(100,116,139,0.2); text-transform:uppercase; letter-spacing:0.06em; font-size:0.68rem; }
.sm-proc-row { color:#cbd5e1; border-bottom:1px solid rgba(100,116,139,0.1); font-size:0.78rem; transition:background 0.15s; }
.sm-proc-row:hover { background:rgba(99,102,241,0.06); }
.sm-proc-row:last-child { border-bottom:none; }
.sm-col-cmd { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-size:0.73rem; color:#94a3b8; }
.sm-col-pid { font-size:0.73rem; }
.sm-col-user { font-size:0.76rem; }
.sm-cpu-pill { padding:0.15rem 0.5rem; border-radius:99px; font-size:0.72rem; font-weight:700; }
.sm-pill-ok { background:rgba(16,185,129,0.15); color:#10b981; }
.sm-pill-warn { background:rgba(245,158,11,0.15); color:#f59e0b; }
.sm-pill-danger { background:rgba(239,68,68,0.15); color:#ef4444; }

/* ── Responsive ── */
@media (max-width:640px) {
    .sm-gauges { grid-template-columns:1fr; }
    .sm-gauge-card { flex-direction:column; text-align:center; }
    .sm-gauge-meta-grid { align-items:center; }
    .sm-header { flex-direction:column; align-items:flex-start; }
    .sm-proc-header,.sm-proc-row { grid-template-columns:65px 55px 60px 55px 1fr; font-size:0.7rem; }
    .sm-info-strip { grid-template-columns:repeat(auto-fill,minmax(120px,1fr)); }
    .sm-row-2col { grid-template-columns:1fr; }
}
</style>
</x-filament-widgets::widget>