<div class="space-y-4">
    @forelse($history as $entry)
        @php
            $bgColor = match ($entry->action_color) {
                'success' => 'bg-success-100 dark:bg-success-900/30',
                'info' => 'bg-info-100 dark:bg-info-900/30',
                'warning' => 'bg-warning-100 dark:bg-warning-900/30',
                'danger' => 'bg-danger-100 dark:bg-danger-900/30',
                'primary' => 'bg-primary-100 dark:bg-primary-900/30',
                default => 'bg-gray-100 dark:bg-gray-700',
            };
            $textColor = match ($entry->action_color) {
                'success' => 'text-success-600 dark:text-success-400',
                'info' => 'text-info-600 dark:text-info-400',
                'warning' => 'text-warning-600 dark:text-warning-400',
                'danger' => 'text-danger-600 dark:text-danger-400',
                'primary' => 'text-primary-600 dark:text-primary-400',
                default => 'text-gray-600 dark:text-gray-400',
            };
        @endphp
        <div class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
            {{-- Action Icon --}}
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $bgColor }}">
                    <x-dynamic-component :component="$entry->action_icon" class="w-5 h-5 {{ $textColor }}" />
                </div>
            </div>

            {{-- Content --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <span class="font-medium text-gray-900 dark:text-white">
                        {{ $entry->action_label }}
                    </span>
                    @if($entry->version)
                        <span
                            class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded">
                            v{{ $entry->version->version_number }}
                        </span>
                    @endif
                </div>

                @if($entry->description)
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ $entry->description }}
                    </p>
                @endif

                @if($entry->metadata && count($entry->metadata) > 0)
                    <div class="mt-2 text-xs text-gray-500 dark:text-gray-500">
                        @foreach($entry->metadata as $key => $value)
                            @if($value)
                                <span class="inline-block mr-3">
                                    <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                    {{ is_array($value) ? json_encode($value) : $value }}
                                </span>
                            @endif
                        @endforeach
                    </div>
                @endif

                <div class="mt-2 flex items-center gap-3 text-xs text-gray-500 dark:text-gray-500">
                    @if($entry->user)
                        <span class="flex items-center gap-1">
                            <x-heroicon-o-user class="w-3 h-3" />
                            {{ $entry->user->name }}
                        </span>
                    @endif
                    <span class="flex items-center gap-1">
                        <x-heroicon-o-clock class="w-3 h-3" />
                        {{ $entry->time_ago }}
                    </span>
                    @if($entry->ip_address)
                        <span class="hidden sm:flex items-center gap-1">
                            <x-heroicon-o-globe-alt class="w-3 h-3" />
                            {{ $entry->ip_address }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
            <x-heroicon-o-clock class="w-12 h-12 mx-auto mb-3 opacity-50" />
            <p>No history entries yet</p>
        </div>
    @endforelse
</div>