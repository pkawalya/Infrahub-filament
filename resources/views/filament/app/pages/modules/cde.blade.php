<x-filament-panels::page>
    {{-- Stats --}}
    @include('filament.app.pages.modules.partials.stat-cards', ['stats' => $this->getStats()])

    {{-- Current Path Breadcrumb --}}
    <div
        class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4 mb-4">
        <div class="flex items-center gap-2 text-sm">
            <span class="font-medium text-gray-500 dark:text-gray-400">📂 Location:</span>
            <button wire:click="navigateToFolder(null)"
                class="text-primary-600 hover:text-primary-700 hover:underline font-medium dark:text-primary-400">
                Root
            </button>
            @if($this->currentFolderId)
                @php
                    $breadcrumbFolders = [];
                    $f = \App\Models\CdeFolder::find($this->currentFolderId);
                    while ($f) {
                        array_unshift($breadcrumbFolders, $f);
                        $f = $f->parent;
                    }
                @endphp
                @foreach($breadcrumbFolders as $bf)
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                            clip-rule="evenodd" />
                    </svg>
                    @if($loop->last)
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $bf->name }}</span>
                    @else
                        <button wire:click="navigateToFolder({{ $bf->id }})"
                            class="text-primary-600 hover:text-primary-700 hover:underline dark:text-primary-400">
                            {{ $bf->name }}
                        </button>
                    @endif
                @endforeach
            @endif
        </div>
    </div>

    {{-- Subfolders --}}
    @php $subfolders = $this->getSubfolders(); @endphp
    @if($subfolders->isNotEmpty())
        <div
            class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4 mb-4">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                </svg>
                Folders
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach($subfolders as $sf)
                    <button wire:click="navigateToFolder({{ $sf->id }})"
                        class="group flex flex-col items-center gap-2 p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-primary-50 hover:border-primary-300 dark:hover:bg-primary-900/20 dark:hover:border-primary-700 transition-all duration-150 cursor-pointer">
                        <div
                            class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center group-hover:bg-primary-100 dark:group-hover:bg-primary-900/50 transition-colors">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 group-hover:text-primary-600 dark:group-hover:text-primary-400"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M19.5 21a3 3 0 003-3v-4.5a3 3 0 00-3-3h-15a3 3 0 00-3 3V18a3 3 0 003 3h15zM1.5 10.146V6a3 3 0 013-3h5.379a2.25 2.25 0 011.59.659l2.122 2.121c.14.141.331.22.53.22H19.5a3 3 0 013 3v1.146A4.483 4.483 0 0019.5 9h-15a4.483 4.483 0 00-3 1.146z" />
                            </svg>
                        </div>
                        <span
                            class="text-xs font-medium text-gray-700 dark:text-gray-300 text-center truncate w-full group-hover:text-primary-700 dark:group-hover:text-primary-400">
                            {{ $sf->name }}
                        </span>
                        @if($sf->suitability_code)
                            <span
                                class="text-[10px] px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 font-mono">
                                {{ $sf->suitability_code }}
                            </span>
                        @endif
                        <span class="text-[10px] text-gray-400">{{ $sf->documents()->count() }} docs</span>
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Documents Table --}}
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="p-4 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                Documents in {{ $this->folderPath }}
            </h3>
        </div>
        {{ $this->table }}
    </div>
</x-filament-panels::page>