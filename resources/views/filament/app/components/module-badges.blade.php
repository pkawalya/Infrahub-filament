@php
    $record = $getRecord();
    $enabled = $record->getEnabledModules();
    $modules = \App\Models\Module::$availableModules;
@endphp

@if(empty($enabled))
    <div style="padding: 0.75rem; text-align: center; color: rgba(var(--gray-400), 1); font-size: 0.8125rem;">
        No modules enabled yet.
    </div>
@else
    <div style="display: flex; flex-direction: column; gap: 0.375rem;">
        @foreach($enabled as $code)
            @php $def = $modules[$code] ?? null; @endphp
            @if($def)
                <a href="{{ \App\Filament\App\Resources\CdeProjectResource::getUrl('module-' . str_replace('_', '-', $code), ['record' => $record]) }}"
                    style="display: flex; align-items: center; gap: 0.5rem; padding: 0.4375rem 0.625rem; border-radius: 0.5rem; background: rgba(var(--gray-50), 1); text-decoration: none; transition: background 150ms; border: 1px solid rgba(var(--gray-100), 1);"
                    onmouseover="this.style.background='rgba(var(--primary-50), 1)'; this.style.borderColor='rgba(var(--primary-200), 1)';"
                    onmouseout="this.style.background='rgba(var(--gray-50), 1)'; this.style.borderColor='rgba(var(--gray-100), 1)';">
                    <x-filament::icon :icon="$def['icon']"
                        style="width: 1rem; height: 1rem; color: rgba(var(--primary-500), 1); flex-shrink: 0;" />
                    <span style="font-size: 0.8125rem; font-weight: 500; color: rgba(var(--gray-700), 1);">{{ $def['name'] }}</span>
                </a>
            @endif
        @endforeach
    </div>
@endif