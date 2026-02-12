{{-- Global Loading Overlay --}}
<div x-data="{
        show: false,
        timer: null,
        start() {
            this.timer = setTimeout(() => { this.show = true }, 150);
        },
        stop() {
            clearTimeout(this.timer);
            this.show = false;
        }
    }" x-on:livewire:navigate.window="start()" x-on:livewire:navigated.window="stop()"
    x-on:livewire:request.window="start()" x-on:livewire:response.window="stop()" x-show="show"
    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;"
    class="fixed inset-0 z-[9999] flex items-center justify-center bg-gray-950/20 backdrop-blur-[2px] dark:bg-gray-950/40">
    <div
        class="flex flex-col items-center gap-4 p-6 bg-white rounded-2xl shadow-2xl ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        {{-- Spinner --}}
        <div class="relative flex items-center justify-center w-12 h-12">
            <div class="absolute inset-0 rounded-full border-[3px] border-gray-200 dark:border-gray-700"></div>
            <div
                class="absolute inset-0 rounded-full border-[3px] border-transparent border-t-primary-500 animate-spin">
            </div>
            <svg class="w-5 h-5 text-primary-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                fill="currentColor">
                <path fill-rule="evenodd"
                    d="M9.315 7.584C12.195 3.883 16.695 1.5 21.75 1.5a.75.75 0 0 1 .75.75c0 5.056-2.383 9.555-6.084 12.436A6.75 6.75 0 0 1 9.75 22.5a.75.75 0 0 1-.75-.75v-4.131A15.838 15.838 0 0 1 6.382 15H2.25a.75.75 0 0 1-.75-.75 6.75 6.75 0 0 1 7.815-6.666ZM15 6.75a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5Z"
                    clip-rule="evenodd" />
            </svg>
        </div>
        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Loading…</span>
    </div>
</div>