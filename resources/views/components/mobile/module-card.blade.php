@props([
    'href' => '#',
    'icon' => 'default',
    'color' => 'blue', // blue, green, red, amber, purple, emerald, pink, cyan
    'title' => '',
    'subtitle' => '',
    'category' => 'operations'
])

@php
    $bgMap = [
        'blue' => 'rgba(59,130,246,0.15)',
        'green' => 'rgba(34,197,94,0.15)',
        'red' => 'rgba(239,68,68,0.15)',
        'amber' => 'rgba(245,158,11,0.15)',
        'purple' => 'rgba(168,85,247,0.15)',
        'emerald' => 'rgba(16,185,129,0.15)',
        'pink' => 'rgba(236,72,153,0.15)',
        'cyan' => 'rgba(14,165,233,0.15)',
        'indigo' => 'rgba(99,102,241,0.15)'
    ];
    $textMap = [
        'blue' => '#60a5fa',
        'green' => '#4ade80',
        'red' => '#f87171',
        'amber' => '#fbbf24',
        'purple' => '#c084fc',
        'emerald' => '#34d399',
        'pink' => '#f472b6',
        'cyan' => '#38bdf8',
        'indigo' => '#818cf8'
    ];
    $badgeBg = $bgMap[$color] ?? $bgMap['indigo'];
    $badgeColor = $textMap[$color] ?? $textMap['indigo'];
@endphp

<a href="{{ $href }}" class="m-card m-module-card" data-category="{{ $category }}">
    <div class="m-icon-badge" style="background:{{ $badgeBg }};color:{{ $badgeColor }};">
        <x-mobile.icon :name="$icon" size="20" stroke="2.2" />
    </div>
    <div class="m-card-title">{{ $title }}</div>
    <div class="m-card-subtitle">{{ $subtitle }}</div>
</a>
