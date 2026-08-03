@props([
    'status' => 'active',
    'label' => null
])

@php
    $text = $label ?? ucfirst(str_replace('_', ' ', $status));
    $statusClass = strtolower(str_replace(' ', '_', $status));
@endphp

<span class="m-pill {{ $statusClass }}">
    <span class="m-pill-dot"></span>
    <span class="m-pill-text">{{ $text }}</span>
</span>
