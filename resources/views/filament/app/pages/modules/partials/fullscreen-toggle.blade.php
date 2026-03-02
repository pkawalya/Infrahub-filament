{{-- Fullscreen Toggle Button (include in any module page) --}}
<div x-data="{ isFullscreen: false }"
    x-on:keydown.escape.window="if(isFullscreen) { isFullscreen = false; $el.closest('.fi-page')?.classList.remove('module-fullscreen'); document.body.style.overflow = ''; }"
    style="position: fixed; top: 68px; right: 16px; z-index: 100;">

    <button type="button" x-on:click="
            isFullscreen = !isFullscreen;
            const page = $el.closest('div[x-data]')?.closest('.fi-page') || document.querySelector('.fi-page');
            if (isFullscreen) {
                page?.classList.add('module-fullscreen');
                document.body.style.overflow = 'hidden';
            } else {
                page?.classList.remove('module-fullscreen');
                document.body.style.overflow = '';
            }
        " class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border transition-all duration-200 shadow-sm
               border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400
               hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200
               dark:hover:bg-indigo-900/30 dark:hover:text-indigo-400 dark:hover:border-indigo-700">
        <template x-if="!isFullscreen">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
            </svg>
        </template>
        <template x-if="isFullscreen">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 9V4.5M9 9H4.5M9 9 3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5 5.25 5.25" />
            </svg>
        </template>
        <span x-text="isFullscreen ? 'Exit Fullscreen' : 'Fullscreen'"></span>
    </button>
</div>

@once
    @push('styles')
        <style>
            .module-fullscreen {
                position: fixed !important;
                inset: 0 !important;
                z-index: 9999 !important;
                background: white !important;
                overflow: auto !important;
                padding: 16px !important;
                margin: 0 !important;
                width: 100vw !important;
                height: 100vh !important;
                max-width: none !important;
            }

            .dark .module-fullscreen {
                background: #111827 !important;
            }

            .module-fullscreen .fi-header {
                padding-right: 180px;
            }
        </style>
    @endpush
@endonce