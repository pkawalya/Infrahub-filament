@props([
    'variant' => 'accent', // accent, success, warning, danger
    'value' => '0',
    'label' => 'Metric',
    'id' => null
])

<div class="m-stat {{ $variant }}">
    <div class="m-stat-value" @if($id) id="{{ $id }}" @endif>{{ $value }}</div>
    <div class="m-stat-label">{{ $label }}</div>
</div>
