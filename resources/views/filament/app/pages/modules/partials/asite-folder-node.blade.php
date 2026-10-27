@php
    $hasChildren = $node->children->isNotEmpty();
    $isExpanded = in_array($node->id, $expandedFolderIds ?? [], true);
    $isSelected = ((int) $currentFolderId === (int) $node->id);
    $indent = ($depth * 14) + 6;
@endphp

<div class="asite-folder-row-wrap" x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false">
    <div class="asite-folder-row {{ $isSelected ? 'is-selected' : '' }}"
         style="padding-left: {{ $indent }}px;"
         wire:click="selectFolder({{ $node->id }})">

        {{-- Expand / Collapse arrow --}}
        @if($hasChildren)
            <button type="button"
                    class="asite-tree-chevron"
                    wire:click.stop="toggleFolderExpand({{ $node->id }})"
                    title="{{ $isExpanded ? 'Collapse' : 'Expand' }}">
                @if($isExpanded)
                    <svg viewBox="0 0 20 20" width="13" height="13" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                @else
                    <svg viewBox="0 0 20 20" width="13" height="13" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                @endif
            </button>
        @else
            <span class="asite-tree-spacer"></span>
        @endif

        {{-- Folder Icon --}}
        <span class="asite-folder-icon" title="Folder">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="#f59e0b">
                <path d="M19.5 21a3 3 0 003-3v-4.5a3 3 0 00-3-3h-15a3 3 0 00-3 3V18a3 3 0 003 3h15zM1.5 10.146V6a3 3 0 013-3h5.379a2.25 2.25 0 011.59.659l2.122 2.121c.14.141.331.22.53.22H19.5a3 3 0 013 3v1.146A4.483 4.483 0 0019.5 9h-15a4.483 4.483 0 00-3 1.146z" />
            </svg>
        </span>

        {{-- Folder Name --}}
        <span class="asite-folder-name" title="{{ $node->name }}">
            {{ $node->name }}
        </span>

        {{-- Document Count Badge --}}
        @php $docCount = $node->documents_count ?? $node->documents()->count(); @endphp
        @if($docCount > 0)
            <span class="asite-folder-count" title="{{ $docCount }} document(s)">
                {{ $docCount }}
            </span>
        @endif

        {{-- Inline Hover Actions --}}
        <div class="asite-folder-actions" x-show="hovered" x-cloak>
            <button type="button"
                    wire:click.stop="openNewFolderModal({{ $node->id }})"
                    class="asite-icon-btn"
                    title="Add Subfolder">
                <svg viewBox="0 0 20 20" width="12" height="12" fill="currentColor">
                    <path d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" />
                </svg>
            </button>
            <button type="button"
                    wire:click.stop="openRenameFolderModal({{ $node->id }})"
                    class="asite-icon-btn"
                    title="Rename Folder">
                <svg viewBox="0 0 20 20" width="12" height="12" fill="currentColor">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
            </button>
            <button type="button"
                    wire:click.stop="deleteFolder({{ $node->id }})"
                    wire:confirm="Delete folder &quot;{{ $node->name }}&quot;?"
                    class="asite-icon-btn asite-danger"
                    title="Delete Folder">
                <svg viewBox="0 0 20 20" width="12" height="12" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Recursive Children --}}
    @if($hasChildren && $isExpanded)
        <div class="asite-tree-branch">
            @foreach($node->children->sortBy('sort_order') as $child)
                @include('filament.app.pages.modules.partials.asite-folder-node', [
                    'node' => $child,
                    'depth' => $depth + 1,
                    'expandedFolderIds' => $expandedFolderIds,
                    'currentFolderId' => $currentFolderId
                ])
            @endforeach
        </div>
    @endif
</div>
