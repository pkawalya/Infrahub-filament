@php
    $projects = \App\Models\CdeProject::query()
        ->select('id', 'name', 'code', 'status')
        ->orderBy('name')
        ->get();

    $currentProjectId = null;
    if (request()->route('record') && request()->is('app/cde-projects/*')) {
        $currentProjectId = request()->route('record');
    }

    $projectData = $projects->map(fn($p) => [
        'id' => $p->id,
        'name' => $p->name,
        'code' => $p->code,
        'status' => $p->status,
        'url' => \App\Filament\App\Resources\CdeProjectResource::getUrl('view', ['record' => $p]),
    ])->values()->toArray();
@endphp

<div x-data="{
        open: false,
        search: '',
        projects: @js($projectData),
        currentId: @js($currentProjectId),
        get filtered() {
            if (!this.search) return this.projects;
            const q = this.search.toLowerCase();
            return this.projects.filter(p => p.name.toLowerCase().includes(q) || (p.code && p.code.toLowerCase().includes(q)));
        },
        get current() { return this.projects.find(p => p.id == this.currentId); },
        go(p) { this.open = false; this.search = ''; window.location.href = p.url; },
        statusColor(s) { 
            return {
                active: 'bg-emerald-500',
                planning: 'bg-blue-500',
                on_hold: 'bg-amber-500',
                completed: 'bg-gray-500',
                cancelled: 'bg-red-500'
            }[s] || 'bg-gray-500'; 
        },
        statusBg(s) { 
            return {
                active: 'bg-emerald-50 text-emerald-600',
                planning: 'bg-blue-50 text-blue-600',
                on_hold: 'bg-amber-50 text-amber-600',
                completed: 'bg-gray-50 text-gray-600',
                cancelled: 'bg-red-50 text-red-600'
            }[s] || 'bg-gray-50 text-gray-600'; 
        }
    }" @click.outside="open = false" @keydown.escape.window="open = false"
    class="relative h-9 mr-4 hidden md:flex items-center">
    <!-- Trigger Button -->
    <button @click="open = !open" type="button"
        class="group flex items-center justify-between w-64 h-full px-3 text-sm text-gray-950 bg-white border border-gray-200 rounded-lg shadow-sm transition-all hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-gray-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800"
        :class="{ 'ring-2 ring-primary-500 border-primary-500': open }">
        <div class="flex items-center gap-2.5 truncate">
            <!-- Project Icon -->
            <div
                class="flex-shrink-0 flex items-center justify-center w-5 h-5 rounded overflow-hidden bg-gradient-to-br from-primary-500 to-primary-600 text-white shadow-sm">
                <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M2.24 6.8a.75.75 0 001.06-.04l1.95-2.1v8.59a.75.75 0 001.5 0V4.66l1.95 2.1a.75.75 0 101.1-1.02l-3.25-3.5a.75.75 0 00-1.1 0L2.2 5.74a.75.75 0 00.04 1.06zm8 4a.75.75 0 000 1.5h7.5a.75.75 0 000-1.5h-7.5zM10.25 15a.75.75 0 000 1.5h7.5a.75.75 0 000-1.5h-7.5z"
                        clip-rule="evenodd" />
                </svg>
            </div>

            <!-- Project Name -->
            <div class="flex flex-col items-start text-left truncate">
                <template x-if="current">
                    <span class="font-medium truncate block" x-text="current.name"></span>
                </template>
                <template x-if="!current">
                    <span class="text-gray-500 dark:text-gray-400">Select project...</span>
                </template>
            </div>
        </div>

        <!-- Chevron -->
        <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-500 transition-transform duration-200"
            :class="{ 'rotate-180 text-primary-500': open }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
            fill="currentColor">
            <path fill-rule="evenodd"
                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                clip-rule="evenodd" />
        </svg>
    </button>

    <!-- Dropdown Panel -->
    <div x-show="open" x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95 translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-1"
        class="absolute top-full right-0 mt-2 w-80 max-h-[26rem] flex flex-col bg-white border border-gray-100 rounded-xl shadow-xl ring-1 ring-gray-950/5 z-50 overflow-hidden dark:bg-gray-900 dark:border-gray-800 dark:ring-white/10"
        style="display: none;">
        <!-- Search Input -->
        <div class="flex-shrink-0 p-3 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900">
            <div class="relative group">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-primary-500"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
                <input x-model="search" x-ref="searchInput" type="text" placeholder="Find a project..."
                    class="w-full h-9 pl-9 pr-3 text-sm rounded-lg border-gray-200 bg-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500 placeholder-gray-400 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                    @keydown.enter.prevent="if(filtered.length > 0) go(filtered[0])"
                    x-init="$watch('open', v => { if(v) setTimeout(() => $refs.searchInput.focus(), 50) })">
            </div>
        </div>

        <!-- Project List -->
        <div class="flex-1 overflow-y-auto p-1.5 space-y-0.5">
            <template x-for="p in filtered" :key="p.id">
                <button @click="go(p)" type="button"
                    class="w-full flex items-center gap-3 px-3 py-2.5 text-left rounded-lg transition-colors group hover:bg-gray-50 dark:hover:bg-gray-800/50"
                    :class="{ '!bg-primary-50 dark:!bg-primary-900/10': p.id == currentId }">
                    <!-- Status Dot -->
                    <div class="flex-shrink-0 w-2 h-2 rounded-full ring-2 ring-white dark:ring-gray-900"
                        :class="statusColor(p.status)">
                    </div>

                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-sm font-medium text-gray-900 truncate dark:text-white"
                                x-text="p.name"></span>
                            <span
                                class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] uppercase font-bold tracking-wider"
                                :class="p.id == currentId ? 'bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400'"
                                x-text="p.code || 'PRJ'">
                            </span>
                        </div>
                    </div>

                    <!-- Selected Tick -->
                    <template x-if="p.id == currentId">
                        <svg class="flex-shrink-0 w-4 h-4 text-primary-600 dark:text-primary-400"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                                clip-rule="evenodd" />
                        </svg>
                    </template>
                </button>
            </template>

            <!-- Empty State -->
            <template x-if="filtered.length === 0">
                <div class="py-8 text-center">
                    <div
                        class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gray-50 text-gray-400 dark:bg-gray-800">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">No projects found.</p>
                </div>
            </template>
        </div>

        <!-- Footer -->
        <div class="flex-shrink-0 p-2 border-t border-gray-100 bg-gray-50/50 dark:border-gray-800 dark:bg-gray-900">
            <a href="{{ \App\Filament\App\Resources\CdeProjectResource::getUrl() }}"
                class="flex items-center justify-center w-full px-4 py-2 text-xs font-semibold uppercase tracking-wide text-gray-600 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 hover:text-gray-900 transition-colors focus:ring-1 focus:ring-gray-300 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700 dark:hover:bg-gray-700 dark:hover:text-white">
                Browse All Projects
            </a>
        </div>
    </div>
</div>