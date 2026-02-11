<x-filament-panels::page>
    {{-- Subfolders Grid --}}
    @php
        $subfolders = $this->getSubfolders();
    @endphp

    @if($this->folderId || $subfolders->count() > 0)
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-3">
                <x-heroicon-o-folder class="w-5 h-5 text-warning-500" />
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Folders</h3>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                {{-- Go Up Button --}}
                @if($this->folderId)
                    <button wire:click="navigateUp"
                        class="group flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-primary-500 dark:hover:border-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-all duration-200">
                        <x-heroicon-o-arrow-uturn-left
                            class="w-8 h-8 text-gray-400 group-hover:text-primary-500 transition-colors" />
                        <span
                            class="mt-2 text-xs font-medium text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400">
                            Go Up
                        </span>
                    </button>
                @endif

                {{-- Subfolders --}}
                @foreach($subfolders as $folder)
                    <button wire:click="navigateToFolder({{ $folder->id }})"
                        class="group flex flex-col items-center justify-center p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-primary-500 dark:hover:border-primary-500 hover:shadow-md transition-all duration-200">
                        <div class="relative">
                            <x-heroicon-s-folder
                                class="w-10 h-10 text-amber-400 group-hover:text-amber-500 transition-colors" />
                            @if($folder->documents_count > 0 || $folder->children_count > 0)
                                <span
                                    class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 text-[10px] font-bold text-white bg-primary-500 rounded-full">
                                    {{ $folder->documents_count + $folder->children_count }}
                                </span>
                            @endif
                        </div>
                        <span
                            class="mt-2 text-xs font-medium text-gray-700 dark:text-gray-300 group-hover:text-primary-600 dark:group-hover:text-primary-400 text-center line-clamp-2">
                            {{ $folder->name }}
                        </span>
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Documents Table --}}
    <div>
        <div class="flex items-center gap-2 mb-3">
            <x-heroicon-o-document-text class="w-5 h-5 text-primary-500" />
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Documents</h3>
        </div>

        {{ $this->table }}
    </div>

    {{-- Quick Stats --}}
    <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div
                    class="flex items-center justify-center w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg">
                    <x-heroicon-o-document-text class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $this->project?->documents()->count() ?? 0 }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Documents</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div
                    class="flex items-center justify-center w-10 h-10 bg-warning-100 dark:bg-warning-900/30 rounded-lg">
                    <x-heroicon-o-folder class="w-5 h-5 text-warning-600 dark:text-warning-400" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $this->project?->folders()->count() ?? 0 }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Folders</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 bg-danger-100 dark:bg-danger-900/30 rounded-lg">
                    <x-heroicon-o-lock-closed class="w-5 h-5 text-danger-600 dark:text-danger-400" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $this->project?->documents()->where('is_locked', true)->count() ?? 0 }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Locked Files</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div
                    class="flex items-center justify-center w-10 h-10 bg-success-100 dark:bg-success-900/30 rounded-lg">
                    <x-heroicon-o-arrow-up-tray class="w-5 h-5 text-success-600 dark:text-success-400" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ \App\Models\DocumentVersion::whereHas('document', fn($q) => $q->where('project_id', $this->projectId))->count() }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Versions</p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>