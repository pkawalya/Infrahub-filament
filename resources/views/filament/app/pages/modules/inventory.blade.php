<x-filament-panels::page>
    {{-- Heroicon SVG helpers (inline, 16px default) --}}
    @php
        $ico = fn($path, $size = 16) => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:' . $size . 'px;height:' . $size . 'px;display:inline-block;vertical-align:middle"><path stroke-linecap="round" stroke-linejoin="round" d="' . $path . '" /></svg>';
        // Standard Heroicons (outline)
        $iCube = $ico('M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9');
        $iTag = $ico('M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z M6 6h.008v.008H6V6z');
        $iStore = $ico('M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z');
        $iCart = $ico('M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z');
        $iInbox = $ico('M9 3.75H6.912a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.238 5.338a2.25 2.25 0 00-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859');
        $iClipboard = $ico('M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15a2.25 2.25 0 012.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z');
        $iArrows = $ico('M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5');
        $iScale = $ico('M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0012 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 01-2.031.352 5.988 5.988 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L18.75 4.971zm-16.5.52c.99-.203 1.99-.377 3-.52m0 0l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.989 5.989 0 01-2.031.352 5.989 5.989 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L5.25 4.971z');
        $iPlus = $ico('M12 4.5v15m7.5-7.5h-15');
        $iPencil = $ico('M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125');
        $iChart = $ico('M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z');
        $iWrench = $ico('M11.42 15.17l-5.1 5.1a2.121 2.121 0 01-3-3l5.1-5.1m3 3l6.172-6.171a2.121 2.121 0 00-3-3l-6.172 6.171m3 3l-3-3');
        $iBolt = $ico('M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z');
        $iWarn = $ico('M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z');
        $iTrash = $ico('M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0');
        $iSave = $ico('M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z');
        // Lifecycle icons
        $iEye = $ico('M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178zM15 12a3 3 0 11-6 0 3 3 0 016 0z');
        $iCheckout = $ico('M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5'); // arrow-up-tray
        $iCheckin = $ico('M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12M12 16.5V3'); // arrow-down-tray
        $iTransfer = $ico('M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5'); // arrows-right-left
        $iPause = $ico('M14.25 9v6m-4.5 0V9M21 12a9 9 0 11-18 0 9 9 0 0118 0z'); // pause-circle
        $iMapPin = $ico('M15 10.5a3 3 0 11-6 0 3 3 0 016 0z M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z');
        $iClock = $ico('M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z');
        $iDots = $ico('M6.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM12.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM18.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0z'); // ellipsis-horizontal
        $iTruck = $ico('M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12');
        $iRoute = $ico('M9 6.75V15m0-8.25a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM9 15a1.5 1.5 0 100 3 1.5 1.5 0 000-3zm6-5.25V18m0-11.25a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM15 18a1.5 1.5 0 100 3 1.5 1.5 0 000-3z');
        $iSignal = $ico('M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z');
        $iFloppy = $ico('M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z');
        $iSaveDoc = $ico('M9 3.75H6.912a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.238 5.338a2.25 2.25 0 00-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859');
    @endphp


    <style>
        /* ─── Inventory Dark Mode ─── */
        .dark .inv-tab-bar {
            border-color: #374151 !important;
        }

        .dark .inv-tab:not(.inv-tab-active) {
            color: #9ca3af !important;
        }

        .dark .inv-tab:not(.inv-tab-active):hover {
            background: #1e1b4b !important;
            color: #a5b4fc !important;
        }

        .dark .inv-tab .inv-badge {
            background: #374151 !important;
            color: #9ca3af !important;
        }

        .dark .inv-card {
            background: #1f2937 !important;
            border-color: #374151 !important;
            color: #e5e7eb !important;
        }

        .dark .inv-card:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, .3) !important;
        }

        .dark .inv-card strong {
            color: #f3f4f6 !important;
        }

        .dark .inv-card .inv-divider {
            border-color: #374151 !important;
        }

        .dark .inv-btn-outline {
            background: #374151 !important;
            border-color: #4b5563 !important;
            color: #d1d5db !important;
        }

        .dark .inv-btn-outline:hover {
            background: #4b5563 !important;
        }

        .dark .inv-table {
            color: #e5e7eb !important;
        }

        .dark .inv-table thead tr {
            background: #1f2937 !important;
            border-color: #374151 !important;
        }

        .dark .inv-table th {
            color: #9ca3af !important;
        }

        .dark .inv-table tbody tr {
            border-color: #1f2937 !important;
        }

        .dark .inv-table tbody tr:hover {
            background: #111827 !important;
        }

        .dark .inv-modal {
            background: #1f2937 !important;
            color: #e5e7eb !important;
        }

        .dark .inv-modal h3 {
            color: #f3f4f6 !important;
        }

        .dark .inv-modal label {
            color: #d1d5db !important;
        }

        .dark .inv-modal input,
        .dark .inv-modal select,
        .dark .inv-modal textarea {
            background: #111827 !important;
            border-color: #4b5563 !important;
            color: #e5e7eb !important;
        }

        .dark .inv-modal .inv-btn-cancel {
            background: #374151 !important;
            border-color: #4b5563 !important;
            color: #d1d5db !important;
        }

        .dark .inv-label {
            color: #9ca3af !important;
        }

        .dark .inv-empty {
            color: #6b7280 !important;
        }

        .dark .inv-icon-box {
            background: linear-gradient(135deg, #1e3a5f, #312e81) !important;
        }

        /* Modal close button & dividers */
        .dark .inv-modal button[type="button"]:not([wire\:click*="submit"]) {
            color: #d1d5db !important;
        }

        .dark .inv-modal [style*="border-top"] {
            border-color: #374151 !important;
        }

        /* Issuance table rows */
        .dark .inv-table td {
            color: #d1d5db !important;
        }

        .dark .inv-table tbody tr {
            border-color: #374151 !important;
        }
    </style>
    @include('filament.app.pages.modules.partials.stat-cards', ['stats' => $this->getStats()])

    {{-- ═══════════════ INVENTORY SUB-TABS ═══════════════ --}}
    @php $activeInventoryTab = $this->activeInventoryTab ?? 'products'; @endphp

    <div class="inv-tab-bar"
        style="display:flex; gap:4px; border-bottom:2px solid #e5e7eb; margin-bottom:16px; flex-wrap:wrap;">
        @foreach(['products' => $iCube . ' Products', 'assets' => $iTag . ' Assets', 'stores' => $iStore . ' Stores', 'purchase_orders' => $iCart . ' Purchase Orders', 'grn' => $iInbox . ' GRN', 'issuances' => $iClipboard . ' Issuances', 'delivery_notes' => $iTruck . ' Delivery Notes', 'transfers' => $iArrows . ' Transfers', 'adjustments' => $iScale . ' Adjustments', 'stock_monitor' => $iSignal . ' Stock Monitor', 'tracking' => $iRoute . ' Tracking'] as $tab => $label)
            <button wire:click="$set('activeInventoryTab', '{{ $tab }}')"
                class="inv-tab {{ $activeInventoryTab === $tab ? 'inv-tab-active' : '' }}"
                style="padding:10px 20px; font-size:13px; font-weight:600; border:none; cursor:pointer; transition:all .2s; border-radius:8px 8px 0 0;
                                                                                                                                                                                                                            {{ $activeInventoryTab === $tab ? 'background:#4f46e5; color:white;' : 'background:transparent; color:#6b7280;' }}">
                {!! $label !!}
                @if($tab === 'products')
                    <span class="inv-badge"
                        style="margin-left:4px; padding:2px 8px; border-radius:99px; font-size:11px; font-weight:700;
                                                                                                                                                                                                                                                                                                                                                                                                                                                {{ $activeInventoryTab === $tab ? 'background:rgba(255,255,255,0.2); color:white;' : 'background:#e5e7eb; color:#6b7280;' }}">
                        {{ \App\Models\Product::where('company_id', $this->record->company_id)->count() }}
                    </span>
                @endif
            </button>
        @endforeach
    </div>

    {{-- ═══════════════ PRODUCTS TAB ═══════════════ --}}
    @if($activeInventoryTab === 'products')
        <div style="display:flex; justify-content:flex-end; margin-bottom:12px;">
            <button wire:click="$set('showProductModal', true)"
                style="display:inline-flex; align-items:center; gap:6px; padding:8px 16px; font-size:13px; font-weight:600; border-radius:8px; border:none; background:#4f46e5; color:white; cursor:pointer;">
                {!! $iPlus !!} Add Product
            </button>
        </div>
        {{ $this->table }}
    @endif

    {{-- ═══════════════ STORES TAB ═══════════════ --}}
    @if($activeInventoryTab === 'stores')
        <div style="display:flex; justify-content:flex-end; margin-bottom:12px;">
            <button wire:click="$set('showStoreModal', true)"
                style="display:inline-flex; align-items:center; gap:6px; padding:8px 16px; font-size:13px; font-weight:600; border-radius:8px; border:none; background:#059669; color:white; cursor:pointer;">
                {!! $iPlus !!} Add Store / Warehouse
            </button>
        </div>

        <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:16px;">
            @forelse($this->getStores() as $store)
                <div class="inv-card" style="background:white; border:1px solid #e5e7eb; border-radius:12px; padding:16px;">
                    <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                        <div class="inv-icon-box"
                            style="width:42px; height:42px; border-radius:10px; background:linear-gradient(135deg,#dbeafe,#e0e7ff); display:flex; align-items:center; justify-content:center; font-size:20px;">
                            {!! $ico('M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z', 32) !!}
                        </div>
                        <div>
                            <div style="font-weight:700; font-size:15px;">{{ $store->name }}</div>
                            <div style="font-size:11px; color:#9ca3af;">{{ $store->code ?? 'No code' }} ·
                                {{ $store->city ?? 'No city' }}
                            </div>
                        </div>
                        <span
                            style="margin-left:auto; padding:3px 10px; border-radius:99px; font-size:10px; font-weight:700;
                                                                                                                                                                                                                                                                                                                                                                                                                                                    {{ $store->is_active ? 'background:#dcfce7; color:#16a34a;' : 'background:#fee2e2; color:#ef4444;' }}">
                            {{ $store->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <div style="font-size:12px; display:grid; grid-template-columns:1fr 1fr; gap:6px;">
                        <div><span class="inv-label" style="color:#9ca3af;">Manager:</span>
                            <strong>{{ $store->manager?->name ?? '—' }}</strong>
                        </div>
                        <div><span style="color:#9ca3af;">Products:</span> <strong>{{ $store->stockLevels->count() }}</strong>
                        </div>
                        <div style="grid-column:1/-1;"><span style="color:#9ca3af;">Address:</span> {{ $store->address ?? '—' }}
                        </div>
                    </div>

                    <div class="inv-divider"
                        style="display:flex; gap:6px; margin-top:12px; padding-top:10px; border-top:1px solid #f3f4f6;">
                        <button wire:click="openEditStoreModal({{ $store->id }})" class="inv-btn-outline"
                            style="padding:4px 10px; font-size:11px; font-weight:600; border-radius:6px; border:1px solid #e5e7eb; background:white; cursor:pointer;">{!! $iPencil !!}
                            Edit</button>
                        <button wire:click="openStockMonitor({{ $store->id }})"
                            style="padding:4px 10px; font-size:11px; font-weight:600; border-radius:6px; border:none; background:#7c3aed; color:white; cursor:pointer;">{!! $iSignal !!}
                            Monitor</button>
                    </div>
                </div>
            @empty
                <div class="inv-empty" style="grid-column:1/-1; text-align:center; padding:40px; color:#9ca3af;">
                    <div style="margin-bottom:8px;color:#9ca3af">
                        {!! $ico('M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z', 32) !!}
                    </div>
                    <div style="font-weight:600;">No Stores Yet</div>
                    <div style="font-size:13px;">Add your first store or warehouse to manage inventory locations.</div>
                </div>
            @endforelse
        </div>
    @endif

    {{-- ═══════════════ PURCHASE ORDERS TAB ═══════════════ --}}
    @if($activeInventoryTab === 'purchase_orders')
        <div style="display:flex; justify-content:flex-end; margin-bottom:12px;">
            <button wire:click="initNewPO"
                style="display:inline-flex; align-items:center; gap:6px; padding:8px 16px; font-size:13px; font-weight:600; border-radius:8px; border:none; background:#4f46e5; color:white; cursor:pointer;">
                {!! $iPlus !!} New Purchase Order
            </button>
        </div>
        {{ $this->table }}
    @endif

    {{-- ═══════════════ GRN TAB ═══════════════ --}}
    @if($activeInventoryTab === 'grn')
        <div style="display:flex; justify-content:flex-end; gap:8px; margin-bottom:12px;">
            <button wire:click="initNewGRN()"
                style="display:inline-flex; align-items:center; gap:6px; padding:8px 16px; font-size:13px; font-weight:600; border-radius:8px; border:none; background:#059669; color:white; cursor:pointer;">
                {!! $iInbox !!} Receive Goods
            </button>
        </div>

        <table class="inv-table" style="width:100%;border-collapse:collapse;font-size:13px">
            <thead>
                <tr style="background:#f9fafb;border-bottom:2px solid #e5e7eb">
                    <th style="padding:10px;text-align:left;font-weight:700;color:#6b7280">GRN #</th>
                    <th style="padding:10px;text-align:left;font-weight:700;color:#6b7280">PO Ref</th>
                    <th style="padding:10px;text-align:left;font-weight:700;color:#6b7280">Warehouse</th>
                    <th style="padding:10px;text-align:left;font-weight:700;color:#6b7280">Status</th>
                    <th style="padding:10px;text-align:left;font-weight:700;color:#6b7280">Items</th>
                    <th style="padding:10px;text-align:left;font-weight:700;color:#6b7280">Date</th>
                    <th style="padding:10px;text-align:left;font-weight:700;color:#6b7280">Received By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->getGRNs() as $grn)
                    <tr style="border-bottom:1px solid #f3f4f6">
                        <td style="padding:10px;font-weight:700">{{ $grn->grn_number }}</td>
                        <td style="padding:10px">{{ $grn->purchaseOrder?->po_number ?? '—' }}</td>
                        <td style="padding:10px">{{ $grn->warehouse?->name ?? '—' }}</td>
                        <td style="padding:10px">
                            <span
                                style="padding:2px 10px;border-radius:99px;font-size:11px;font-weight:600;
                                                                                                                                                                                                                                                                                                                                                                {{ match ($grn->status) { 'accepted' => 'background:#d1fae5;color:#065f46', 'partial' => 'background:#fef3c7;color:#92400e', 'rejected' => 'background:#fee2e2;color:#991b1b', default => 'background:#f3f4f6;color:#6b7280'} }}">
                                {{ \App\Models\GoodsReceivedNote::$statuses[$grn->status] ?? $grn->status }}
                            </span>
                        </td>
                        <td style="padding:10px">{{ $grn->items->count() }} items ({{ $grn->total_received }}
                            rcvd{{ $grn->total_rejected > 0 ? ", {$grn->total_rejected} rejected" : '' }})</td>
                        <td style="padding:10px">{{ $grn->received_date?->format('M d, Y') ?? '—' }}</td>
                        <td style="padding:10px">{{ $grn->receivedBy?->name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="inv-empty" style="text-align:center;padding:40px;color:#9ca3af">
                            <div style="margin-bottom:8px;color:#9ca3af">
                                {!! $ico('M9 3.75H6.912a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.238 5.338a2.25 2.25 0 00-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859', 32) !!}
                            </div>
                            <div style="font-weight:600">No Goods Received Notes yet</div>
                            <div style="font-size:12px;margin-top:4px">Receive goods against purchase orders</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Quick receive from open POs --}}
        @php $openPOs = $this->getAvailablePOs(); @endphp
        @if(count($openPOs) > 0)
            <div class="inv-card"
                style="margin-top:16px;padding:16px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px">
                <div style="font-size:13px;font-weight:700;margin-bottom:8px">{!! $iBolt !!} Quick Receive Against PO</div>
                <div style="display:flex;gap:8px;flex-wrap:wrap">
                    @foreach($openPOs as $poId => $poNum)
                        <button wire:click="initNewGRN({{ $poId }})"
                            style="padding:6px 14px;font-size:12px;font-weight:600;border-radius:6px;border:1px solid #059669;background:white;color:#059669;cursor:pointer">
                            {!! $iInbox !!} {{ $poNum }}
                        </button>
                    @endforeach
                </div>
            </div>
        @endif
    @endif

    {{-- ═══════════════ MATERIAL ISSUANCES TAB ═══════════════ --}}
    @if($activeInventoryTab === 'issuances')
        <div style="display:flex; justify-content:flex-end; margin-bottom:12px;">
            <button wire:click="$set('showIssuanceModal', true)"
                style="display:inline-flex; align-items:center; gap:6px; padding:8px 16px; font-size:13px; font-weight:600; border-radius:8px; border:none; background:#d97706; color:white; cursor:pointer;">
                {!! $iPlus !!} Issue Materials
            </button>
        </div>

        <div style="overflow-x:auto;">
            <table class="inv-table" style="width:100%; border-collapse:collapse; font-size:13px;">
                <thead>
                    <tr style="background:#f9fafb; border-bottom:2px solid #e5e7eb;">
                        <th style="padding:10px 12px; text-align:left; font-weight:600; color:#6b7280;">Issue #</th>
                        <th style="padding:10px 12px; text-align:left; font-weight:600; color:#6b7280;">Issued To</th>
                        <th style="padding:10px 12px; text-align:left; font-weight:600; color:#6b7280;">Purpose</th>
                        <th style="padding:10px 12px; text-align:left; font-weight:600; color:#6b7280;">Store</th>
                        <th style="padding:10px 12px; text-align:left; font-weight:600; color:#6b7280;">Items</th>
                        <th style="padding:10px 12px; text-align:left; font-weight:600; color:#6b7280;">Status</th>
                        <th style="padding:10px 12px; text-align:left; font-weight:600; color:#6b7280;">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->getIssuances() as $iss)
                        @php
                            $statusColors = ['draft' => 'background:#f3f4f6; color:#6b7280;', 'issued' => 'background:#dbeafe; color:#2563eb;', 'partial_return' => 'background:#fef3c7; color:#d97706;', 'returned' => 'background:#dcfce7; color:#16a34a;'];
                        @endphp
                        <tr style="border-bottom:1px solid #f3f4f6;">
                            <td style="padding:10px 12px; font-weight:600;">{{ $iss->issuance_number }}</td>
                            <td style="padding:10px 12px;">{{ $iss->issuedTo?->name ?? $iss->issued_to_name ?? '—' }}</td>
                            <td style="padding:10px 12px;">
                                {{ \App\Models\MaterialIssuance::$purposes[$iss->purpose] ?? $iss->purpose }}
                            </td>
                            <td style="padding:10px 12px;">{{ $iss->warehouse?->name ?? '—' }}</td>
                            <td style="padding:10px 12px;">{{ $iss->items_count ?? $iss->items->count() }} items</td>
                            <td style="padding:10px 12px;">
                                <span
                                    style="padding:3px 10px; border-radius:99px; font-size:11px; font-weight:600; {{ $statusColors[$iss->status] ?? '' }}">
                                    {{ \App\Models\MaterialIssuance::$statuses[$iss->status] ?? $iss->status }}
                                </span>
                            </td>
                            <td style="padding:10px 12px; font-size:12px; color:#9ca3af;">
                                {{ $iss->issue_date?->format('M d, Y') ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding:40px; text-align:center; color:#9ca3af;">
                                <div style="margin-bottom:8px;color:#9ca3af">
                                    {!! $ico('M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15a2.25 2.25 0 012.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z', 32) !!}
                                </div>
                                <div style="font-weight:600;">No Material Issuances</div>
                                <div style="font-size:13px;">Issue materials to track who received what.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    {{-- ═══════════════ TRANSFERS TAB ═══════════════ --}}
    @if($activeInventoryTab === 'transfers')
        <div style="display:flex; justify-content:flex-end; margin-bottom:12px;">
            <button wire:click="initNewTransfer"
                style="display:inline-flex; align-items:center; gap:6px; padding:8px 16px; font-size:13px; font-weight:600; border-radius:8px; border:none; background:#7c3aed; color:white; cursor:pointer;">
                {!! $iArrows !!} New Transfer
            </button>
        </div>

        <table class="inv-table" style="width:100%;border-collapse:collapse;font-size:13px">
            <thead>
                <tr style="background:#f9fafb;border-bottom:2px solid #e5e7eb">
                    <th style="padding:10px;text-align:left;font-weight:700;color:#6b7280">Transfer #</th>
                    <th style="padding:10px;text-align:left;font-weight:700;color:#6b7280">From → To</th>
                    <th style="padding:10px;text-align:left;font-weight:700;color:#6b7280">Status</th>
                    <th style="padding:10px;text-align:left;font-weight:700;color:#6b7280">Priority</th>
                    <th style="padding:10px;text-align:left;font-weight:700;color:#6b7280">Items</th>
                    <th style="padding:10px;text-align:left;font-weight:700;color:#6b7280">Date</th>
                    <th style="padding:10px;text-align:left;font-weight:700;color:#6b7280">By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->getTransfers() as $transfer)
                    <tr style="border-bottom:1px solid #f3f4f6">
                        <td style="padding:10px;font-weight:700">{{ $transfer->transfer_number }}</td>
                        <td style="padding:10px">{{ $transfer->fromWarehouse?->name ?? '—' }} →
                            {{ $transfer->toWarehouse?->name ?? '—' }}
                        </td>
                        <td style="padding:10px">
                            <span
                                style="padding:2px 10px;border-radius:99px;font-size:11px;font-weight:600;
                                                                                                                                                                                                                                                                                                                                                        {{ match ($transfer->status) { 'received' => 'background:#d1fae5;color:#065f46', 'in_transit' => 'background:#dbeafe;color:#1e40af', 'cancelled' => 'background:#fee2e2;color:#991b1b', default => 'background:#f3f4f6;color:#6b7280'} }}">
                                {{ \App\Models\StockTransfer::$statuses[$transfer->status] ?? $transfer->status }}
                            </span>
                        </td>
                        <td style="padding:10px">
                            <span
                                style="padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600;
                                                                                                                                                                                                                                                                                                                                                        {{ match ($transfer->priority) { 'urgent' => 'background:#fee2e2;color:#991b1b', 'high' => 'background:#fef3c7;color:#92400e', default => 'background:#f3f4f6;color:#6b7280'} }}">
                                {{ ucfirst($transfer->priority ?? 'normal') }}
                            </span>
                        </td>
                        <td style="padding:10px">{{ $transfer->items->count() }} products</td>
                        <td style="padding:10px">{{ $transfer->created_at?->format('M d, Y') ?? '—' }}</td>
                        <td style="padding:10px">{{ $transfer->creator?->name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="inv-empty" style="text-align:center;padding:40px;color:#9ca3af">
                            <div style="margin-bottom:8px;color:#9ca3af">
                                {!! $ico('M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5', 32) !!}
                            </div>
                            <div style="font-weight:600">No Stock Transfers</div>
                            <div style="font-size:12px;margin-top:4px">Transfer stock between warehouses</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    {{-- ═══════════════ ADJUSTMENTS TAB ═══════════════ --}}
    @if($activeInventoryTab === 'adjustments')
        <div style="display:flex; justify-content:flex-end; margin-bottom:12px;">
            <button wire:click="initNewAdjustment"
                style="display:inline-flex; align-items:center; gap:6px; padding:8px 16px; font-size:13px; font-weight:600; border-radius:8px; border:none; background:#dc2626; color:white; cursor:pointer;">
                {!! $iScale !!} New Adjustment
            </button>
        </div>

        {{-- Reorder Alerts --}}
        @php $alerts = $this->getReorderAlerts(); @endphp
        @if($alerts->count() > 0)
            <div class="inv-card"
                style="margin-bottom:16px;padding:16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px">
                <div style="font-size:14px;font-weight:700;color:#dc2626;margin-bottom:8px">{!! $iWarn !!} Reorder Alerts
                    ({{ $alerts->count() }} products below reorder level)</div>
                <div style="display:flex;gap:8px;flex-wrap:wrap">
                    @foreach($alerts as $product)
                        <span
                            style="padding:4px 12px;font-size:12px;font-weight:600;border-radius:6px;background:white;border:1px solid #fca5a5;color:#dc2626">
                            {{ $product->name }} —
                            {{ $product->stockLevels->sum('quantity_on_hand') }}/{{ $product->reorder_level }} min
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        <table class="inv-table" style="width:100%;border-collapse:collapse;font-size:13px">
            <thead>
                <tr style="background:#f9fafb;border-bottom:2px solid #e5e7eb">
                    <th style="padding:10px;text-align:left;font-weight:700;color:#6b7280">Adj #</th>
                    <th style="padding:10px;text-align:left;font-weight:700;color:#6b7280">Product</th>
                    <th style="padding:10px;text-align:left;font-weight:700;color:#6b7280">Warehouse</th>
                    <th style="padding:10px;text-align:left;font-weight:700;color:#6b7280">Type</th>
                    <th style="padding:10px;text-align:right;font-weight:700;color:#6b7280">Before</th>
                    <th style="padding:10px;text-align:right;font-weight:700;color:#6b7280">Change</th>
                    <th style="padding:10px;text-align:right;font-weight:700;color:#6b7280">After</th>
                    <th style="padding:10px;text-align:left;font-weight:700;color:#6b7280">Reason</th>
                    <th style="padding:10px;text-align:left;font-weight:700;color:#6b7280">By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->getAdjustments() as $adj)
                    <tr style="border-bottom:1px solid #f3f4f6">
                        <td style="padding:10px;font-weight:700">{{ $adj->adjustment_number }}</td>
                        <td style="padding:10px">{{ $adj->product?->name ?? '—' }}</td>
                        <td style="padding:10px">{{ $adj->warehouse?->name ?? '—' }}</td>
                        <td style="padding:10px">
                            <span
                                style="padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600;background:#f3f4f6;color:#6b7280">
                                {{ \App\Models\StockAdjustment::$types[$adj->type] ?? $adj->type }}
                            </span>
                        </td>
                        <td style="padding:10px;text-align:right;font-family:monospace">
                            {{ number_format($adj->quantity_before, 0) }}
                        </td>
                        <td
                            style="padding:10px;text-align:right;font-family:monospace;font-weight:700;color:{{ $adj->quantity_change >= 0 ? '#059669' : '#dc2626' }}">
                            {{ $adj->quantity_change >= 0 ? '+' : '' }}{{ number_format($adj->quantity_change, 0) }}
                        </td>
                        <td style="padding:10px;text-align:right;font-family:monospace;font-weight:700">
                            {{ number_format($adj->quantity_after, 0) }}
                        </td>
                        <td style="padding:10px;font-size:12px">{{ Str::limit($adj->reason, 40) }}</td>
                        <td style="padding:10px">{{ $adj->performedBy?->name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="inv-empty" style="text-align:center;padding:40px;color:#9ca3af">
                            <div style="margin-bottom:8px;color:#9ca3af">
                                {!! $ico('M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0012 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 01-2.031.352 5.988 5.988 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L18.75 4.971zm-16.5.52c.99-.203 1.99-.377 3-.52m0 0l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.989 5.989 0 01-2.031.352 5.989 5.989 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L5.25 4.971z', 32) !!}
                            </div>
                            <div style="font-weight:600">No Stock Adjustments</div>
                            <div style="font-size:12px;margin-top:4px">Record stock counts, damages, or corrections</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    {{-- ═══════════════ ASSETS TAB ═══════════════ --}}
    @if($activeInventoryTab === 'assets')
        @php
            $assetsPaginated = $this->getAssets();
            $stats = $this->getAssetStats();
            $statusColors = [
                'available' => ['bg' => '#dcfce7', 'text' => '#16a34a'],
                'assigned' => ['bg' => '#dbeafe', 'text' => '#2563eb'],
                'maintenance' => ['bg' => '#fef3c7', 'text' => '#d97706'],
                'retired' => ['bg' => '#f3f4f6', 'text' => '#6b7280'],
                'lost' => ['bg' => '#fee2e2', 'text' => '#ef4444'],
                'disposed' => ['bg' => '#fecaca', 'text' => '#dc2626'],
            ];
        @endphp

        {{-- ── Toolbar: Stats + Search + Filters + View Toggle ── --}}
        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:10px;margin-bottom:16px">
            {{-- Status filter pills --}}
            <div style="display:flex;gap:4px;flex-wrap:wrap;flex:1">
                @foreach(array_merge(['all' => 'All'], \App\Models\Asset::$statuses) as $key => $label)
                    @php
                        $count = $stats[$key] ?? 0;
                        $isActive = $assetStatusFilter === $key;
                        $sc = $statusColors[$key] ?? ['bg' => '#f3f4f6', 'text' => '#6b7280'];
                    @endphp
                    <button wire:click="$set('assetStatusFilter', '{{ $key }}')"
                        style="padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;cursor:pointer;border:1.5px solid {{ $isActive ? ($sc['text'] ?? '#6366f1') : '#e5e7eb' }};background:{{ $isActive ? ($sc['bg'] ?? '#eef2ff') : 'white' }};color:{{ $isActive ? ($sc['text'] ?? '#4f46e5') : '#6b7280' }};transition:all .15s;display:flex;align-items:center;gap:4px">
                        {{ $label }}
                        @if($count > 0)
                            <span
                                style="background:{{ $isActive ? ($sc['text'] ?? '#4f46e5') : '#e5e7eb' }};color:{{ $isActive ? 'white' : '#6b7280' }};padding:1px 7px;border-radius:10px;font-size:10px;font-weight:700">{{ number_format($count) }}</span>
                        @endif
                    </button>
                @endforeach
            </div>

            {{-- Search --}}
            <div style="position:relative;min-width:220px">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#94a3b8;pointer-events:none"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m21 21-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
                <input type="text" wire:model.live.debounce.300ms="assetSearch" placeholder="Search assets..."
                    style="width:100%;padding:7px 10px 7px 32px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;box-sizing:border-box"
                    onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#d1d5db'">
            </div>

            {{-- View toggle --}}
            <div style="display:flex;border:1px solid #d1d5db;border-radius:8px;overflow:hidden">
                <button wire:click="$set('assetViewMode', 'table')"
                    style="padding:6px 12px;font-size:12px;font-weight:600;border:none;cursor:pointer;background:{{ $assetViewMode === 'table' ? '#4f46e5' : 'white' }};color:{{ $assetViewMode === 'table' ? 'white' : '#6b7280' }}">
                    {!! $ico('M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0112 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M10.875 12c-.621 0-1.125.504-1.125 1.125M12 10.875c-.621 0-1.125.504-1.125 1.125m0 1.5v-1.5m0 0c0-.621.504-1.125 1.125-1.125m0 1.5c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m-2.25 0c-.621 0-1.125.504-1.125 1.125m0 1.5v-1.5', 14) !!}
                    Table
                </button>
                <button wire:click="$set('assetViewMode', 'cards')"
                    style="padding:6px 12px;font-size:12px;font-weight:600;border:none;border-left:1px solid #d1d5db;cursor:pointer;background:{{ $assetViewMode === 'cards' ? '#4f46e5' : 'white' }};color:{{ $assetViewMode === 'cards' ? 'white' : '#6b7280' }}">
                    {!! $ico('M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z', 14) !!}
                    Cards
                </button>
            </div>

            {{-- Register button --}}
            <button wire:click="$set('showAssetModal', true)"
                style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;font-size:13px;font-weight:600;border-radius:8px;border:none;background:#7c3aed;color:white;cursor:pointer">
                {!! $iPlus !!} Register Asset
            </button>
        </div>

        {{-- Results count --}}
        <div style="font-size:12px;color:#9ca3af;margin-bottom:8px">
            Showing {{ $assetsPaginated->firstItem() ?? 0 }}–{{ $assetsPaginated->lastItem() ?? 0 }} of
            {{ number_format($assetsPaginated->total()) }} assets
            @if($assetSearch) <span style="color:#4f46e5;font-weight:600">matching "{{ $assetSearch }}"</span> @endif
        </div>

        {{-- ══ TABLE VIEW ══ --}}
        @if($assetViewMode === 'table')
            <div style="border:1px solid #e5e7eb;border-radius:12px;overflow:visible;position:relative">
                <table class="inv-table" style="width:100%;border-collapse:collapse;font-size:13px">
                    <thead>
                        <tr style="background:#f9fafb;border-bottom:2px solid #e5e7eb">
                            <th
                                style="padding:10px 12px;text-align:left;font-weight:700;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:.5px">
                                Asset Tag</th>
                            <th
                                style="padding:10px 12px;text-align:left;font-weight:700;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:.5px">
                                Name / Product</th>
                            <th
                                style="padding:10px 12px;text-align:left;font-weight:700;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:.5px">
                                Status</th>
                            <th
                                style="padding:10px 12px;text-align:left;font-weight:700;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:.5px">
                                Holder</th>
                            <th
                                style="padding:10px 12px;text-align:left;font-weight:700;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:.5px">
                                Location</th>
                            <th
                                style="padding:10px 12px;text-align:left;font-weight:700;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:.5px">
                                Condition</th>
                            <th
                                style="padding:10px 12px;text-align:right;font-weight:700;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:.5px">
                                Book Value</th>
                            <th
                                style="padding:10px 12px;text-align:center;font-weight:700;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:.5px;width:140px">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assetsPaginated as $asset)
                            @php $sc = $statusColors[$asset->status] ?? ['bg' => '#f3f4f6', 'text' => '#6b7280']; @endphp
                            <tr style="border-bottom:1px solid #f3f4f6;transition:background .1s"
                                onmouseenter="this.style.background='#f9fafb'" onmouseleave="this.style.background=''">
                                <td style="padding:8px 12px;font-family:monospace;font-size:12px;font-weight:600;color:#4f46e5">
                                    {{ $asset->asset_tag }}
                                </td>
                                <td style="padding:8px 12px">
                                    <div style="font-weight:600">{{ $asset->display_name }}</div>
                                    @if($asset->serial_number)
                                    <div style="font-size:11px;color:#9ca3af">S/N: {{ $asset->serial_number }}</div>@endif
                                </td>
                                <td style="padding:8px 12px">
                                    <span
                                        style="padding:2px 8px;border-radius:6px;font-size:11px;font-weight:600;background:{{ $sc['bg'] }};color:{{ $sc['text'] }}">
                                        {{ \App\Models\Asset::$statuses[$asset->status] ?? $asset->status }}
                                    </span>
                                </td>
                                <td style="padding:8px 12px;font-size:12px">{{ $asset->currentHolder?->name ?? '—' }}</td>
                                <td style="padding:8px 12px;font-size:12px">
                                    {{ $asset->current_location ?? $asset->warehouse?->name ?? '—' }}
                                </td>
                                <td style="padding:8px 12px;font-size:12px">
                                    <span
                                        style="{{ $asset->condition === 'damaged' ? 'color:#ef4444;font-weight:600' : '' }}">{{ \App\Models\Asset::$conditions[$asset->condition] ?? $asset->condition }}</span>
                                </td>
                                <td style="padding:8px 12px;text-align:right;font-family:monospace;font-weight:600">
                                    {{ \App\Support\CurrencyHelper::format($asset->current_book_value, 0) }}
                                </td>
                                <td style="padding:8px 12px;text-align:center">
                                    <div x-data="{ open: false }"
                                        style="position:relative;display:inline-flex;gap:4px;align-items:center">
                                        {{-- Primary action --}}
                                        @if($asset->status === 'available')
                                            <button wire:click="openCheckoutModal({{ $asset->id }})"
                                                style="padding:4px 10px;font-size:11px;font-weight:600;border-radius:6px;border:none;background:#2563eb;color:white;cursor:pointer;display:inline-flex;align-items:center;gap:3px"
                                                title="Checkout">{!! $iCheckout !!} Checkout</button>
                                        @elseif($asset->status === 'assigned')
                                            <button wire:click="checkinAsset({{ $asset->id }})"
                                                style="padding:4px 10px;font-size:11px;font-weight:600;border-radius:6px;border:none;background:#16a34a;color:white;cursor:pointer;display:inline-flex;align-items:center;gap:3px"
                                                title="Checkin">{!! $iCheckin !!} Checkin</button>
                                        @else
                                            <button wire:click="openAssetDetail({{ $asset->id }})"
                                                style="padding:4px 10px;font-size:11px;font-weight:600;border-radius:6px;border:1px solid #d1d5db;background:white;color:#374151;cursor:pointer;display:inline-flex;align-items:center;gap:3px"
                                                title="View">{!! $iEye !!} View</button>
                                        @endif
                                        {{-- Dropdown --}}
                                        <button x-on:click="open = !open"
                                            style="padding:4px 6px;border-radius:6px;border:1px solid #d1d5db;background:white;cursor:pointer;display:inline-flex;align-items:center;color:#6b7280"
                                            title="More actions">{!! $iDots !!}</button>
                                        <div x-show="open" x-cloak x-on:click.outside="open = false" x-transition.opacity
                                            style="position:absolute;right:0;top:100%;margin-top:4px;background:white;border:1px solid #e2e8f0;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);z-index:50;min-width:170px;overflow:hidden;white-space:nowrap">
                                            <button wire:click="openAssetDetail({{ $asset->id }})" x-on:click="open=false"
                                                class="inv-dd-item"
                                                style="display:flex;width:100%;padding:7px 14px;font-size:12px;align-items:center;gap:8px;border:none;background:none;cursor:pointer;color:#374151"
                                                onmouseover="this.style.background='#f1f5f9'"
                                                onmouseout="this.style.background=''">{!! $iEye !!} View Details</button>
                                            <button wire:click="openEditAssetModal({{ $asset->id }})" x-on:click="open=false"
                                                class="inv-dd-item"
                                                style="display:flex;width:100%;padding:7px 14px;font-size:12px;align-items:center;gap:8px;border:none;background:none;cursor:pointer;color:#374151"
                                                onmouseover="this.style.background='#f1f5f9'"
                                                onmouseout="this.style.background=''">{!! $iPencil !!} Edit</button>
                                            <div style="border-top:1px solid #f3f4f6"></div>
                                            @if(in_array($asset->status, ['available', 'assigned']))
                                                <button wire:click="openTransferAssetModal({{ $asset->id }})" x-on:click="open=false"
                                                    class="inv-dd-item"
                                                    style="display:flex;width:100%;padding:7px 14px;font-size:12px;align-items:center;gap:8px;border:none;background:none;cursor:pointer;color:#374151"
                                                    onmouseover="this.style.background='#f1f5f9'"
                                                    onmouseout="this.style.background=''">{!! $iTransfer !!} Transfer</button>
                                            @endif
                                            <button wire:click="openMaintenanceModal({{ $asset->id }})" x-on:click="open=false"
                                                class="inv-dd-item"
                                                style="display:flex;width:100%;padding:7px 14px;font-size:12px;align-items:center;gap:8px;border:none;background:none;cursor:pointer;color:#374151"
                                                onmouseover="this.style.background='#f1f5f9'"
                                                onmouseout="this.style.background=''">{!! $iWrench !!} Maintenance</button>
                                            <button wire:click="openConditionModal({{ $asset->id }})" x-on:click="open=false"
                                                class="inv-dd-item"
                                                style="display:flex;width:100%;padding:7px 14px;font-size:12px;align-items:center;gap:8px;border:none;background:none;cursor:pointer;color:#374151"
                                                onmouseover="this.style.background='#f1f5f9'"
                                                onmouseout="this.style.background=''">{!! $iClipboard !!} Condition</button>
                                            <a href="{{ $asset->qr_code_url }}" target="_blank" class="inv-dd-item"
                                                style="display:flex;width:100%;padding:7px 14px;font-size:12px;align-items:center;gap:8px;text-decoration:none;color:#374151"
                                                onmouseover="this.style.background='#f1f5f9'"
                                                onmouseout="this.style.background=''">{!! $iTag !!} QR Code</a>
                                            @if(!in_array($asset->status, ['retired', 'disposed', 'lost']))
                                                <div style="border-top:1px solid #f3f4f6"></div>
                                                <button wire:click="openReplaceAssetModal({{ $asset->id }})" x-on:click="open=false"
                                                    class="inv-dd-item"
                                                    style="display:flex;width:100%;padding:7px 14px;font-size:12px;align-items:center;gap:8px;border:none;background:none;cursor:pointer;color:#4f46e5"
                                                    onmouseover="this.style.background='#eef2ff'"
                                                    onmouseout="this.style.background=''">{!! $iTransfer !!} Replace</button>
                                                <button wire:click="openDisposeAssetModal({{ $asset->id }}, 'retire')"
                                                    x-on:click="open=false" class="inv-dd-item"
                                                    style="display:flex;width:100%;padding:7px 14px;font-size:12px;align-items:center;gap:8px;border:none;background:none;cursor:pointer;color:#d97706"
                                                    onmouseover="this.style.background='#fef3c7'"
                                                    onmouseout="this.style.background=''">{!! $iPause !!} Retire</button>
                                                <button wire:click="openDisposeAssetModal({{ $asset->id }}, 'dispose')"
                                                    x-on:click="open=false" class="inv-dd-item"
                                                    style="display:flex;width:100%;padding:7px 14px;font-size:12px;align-items:center;gap:8px;border:none;background:none;cursor:pointer;color:#dc2626"
                                                    onmouseover="this.style.background='#fee2e2'"
                                                    onmouseout="this.style.background=''">{!! $iTrash !!} Dispose</button>
                                                <button wire:click="openDisposeAssetModal({{ $asset->id }}, 'lost')"
                                                    x-on:click="open=false" class="inv-dd-item"
                                                    style="display:flex;width:100%;padding:7px 14px;font-size:12px;align-items:center;gap:8px;border:none;background:none;cursor:pointer;color:#ef4444"
                                                    onmouseover="this.style.background='#fee2e2'"
                                                    onmouseout="this.style.background=''">{!! $iWarn !!} Report Lost</button>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="inv-empty" style="text-align:center;padding:40px;color:#9ca3af">
                                    <div style="font-weight:600">
                                        {{ $assetSearch ? 'No assets match your search' : 'No Assets Registered' }}
                                    </div>
                                    <div style="font-size:12px;margin-top:4px">
                                        {{ $assetSearch ? 'Try a different search term' : 'Register assets to track equipment, tools, and machinery.' }}
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        {{-- ══ CARD VIEW ══ --}}
        @if($assetViewMode === 'cards')
            <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(320px, 1fr));gap:16px">
                @forelse($assetsPaginated as $asset)
                    @php $sc = $statusColors[$asset->status] ?? ['bg' => '#f3f4f6', 'text' => '#6b7280']; @endphp
                    <div class="inv-card"
                        style="background:white;border:1px solid #e5e7eb;border-radius:12px;padding:16px;transition:all .2s"
                        onmouseenter="this.style.boxShadow='0 4px 16px rgba(0,0,0,.08)';this.style.transform='translateY(-2px)'"
                        onmouseleave="this.style.boxShadow='none';this.style.transform='none'">
                        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px">
                            <div>
                                <div style="font-weight:700;font-size:15px">{{ $asset->display_name }}</div>
                                <div style="font-size:11px;color:#9ca3af;margin-top:2px;font-family:monospace">
                                    {{ $asset->asset_tag }}
                                </div>
                                @if($asset->serial_number)
                                <div style="font-size:10px;color:#9ca3af">S/N: {{ $asset->serial_number }}</div>@endif
                            </div>
                            <span
                                style="padding:3px 10px;border-radius:6px;font-size:11px;font-weight:600;background:{{ $sc['bg'] }};color:{{ $sc['text'] }}">
                                {{ \App\Models\Asset::$statuses[$asset->status] ?? $asset->status }}
                            </span>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;font-size:12px;margin-bottom:10px">
                            <div><span style="color:#9ca3af">Holder:</span>
                                <strong>{{ $asset->currentHolder?->name ?? '—' }}</strong>
                            </div>
                            <div><span style="color:#9ca3af">Location:</span>
                                <strong>{{ $asset->current_location ?? $asset->warehouse?->name ?? '—' }}</strong>
                            </div>
                            <div><span style="color:#9ca3af">Condition:</span> <strong
                                    style="{{ $asset->condition === 'damaged' ? 'color:#ef4444;' : '' }}">{{ \App\Models\Asset::$conditions[$asset->condition] ?? $asset->condition }}</strong>
                            </div>
                            <div><span style="color:#9ca3af">Book Value:</span>
                                <strong>{{ \App\Support\CurrencyHelper::format($asset->current_book_value, 0) }}</strong>
                            </div>
                        </div>
                        <div
                            style="display:flex;align-items:center;justify-content:space-between;padding-top:10px;border-top:1px solid #f3f4f6">
                            <div style="display:flex;gap:4px;align-items:center">
                                @if($asset->status === 'available')
                                    <button wire:click="openCheckoutModal({{ $asset->id }})"
                                        style="padding:4px 10px;font-size:11px;font-weight:600;border-radius:6px;border:none;background:#2563eb;color:white;cursor:pointer;display:inline-flex;align-items:center;gap:3px">{!! $iCheckout !!}
                                        Checkout</button>
                                @elseif($asset->status === 'assigned')
                                    <button wire:click="checkinAsset({{ $asset->id }})"
                                        style="padding:4px 10px;font-size:11px;font-weight:600;border-radius:6px;border:none;background:#16a34a;color:white;cursor:pointer;display:inline-flex;align-items:center;gap:3px">{!! $iCheckin !!}
                                        Checkin</button>
                                @endif
                                <button wire:click="openAssetDetail({{ $asset->id }})"
                                    style="padding:4px 8px;font-size:11px;border-radius:6px;border:1px solid #e5e7eb;background:white;cursor:pointer;display:inline-flex;align-items:center;color:#6b7280"
                                    title="View Details">{!! $iEye !!}</button>
                                <button wire:click="openMaintenanceModal({{ $asset->id }})"
                                    style="padding:4px 8px;font-size:11px;border-radius:6px;border:1px solid #e5e7eb;background:white;cursor:pointer;display:inline-flex;align-items:center;color:#6b7280"
                                    title="Maintenance">{!! $iWrench !!}</button>
                                <a href="{{ $asset->qr_code_url }}" target="_blank"
                                    style="padding:4px 8px;font-size:11px;border-radius:6px;border:1px solid #e5e7eb;background:white;display:inline-flex;align-items:center;color:#6b7280;text-decoration:none"
                                    title="QR Code">{!! $iTag !!}</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="inv-empty" style="grid-column:1/-1;text-align:center;padding:40px;color:#9ca3af">
                        <div style="font-weight:600">{{ $assetSearch ? 'No assets match your search' : 'No Assets Registered' }}
                        </div>
                        <div style="font-size:13px">Register assets to track equipment, tools, and machinery.</div>
                    </div>
                @endforelse
            </div>
        @endif

        {{-- ══ PAGINATION ══ --}}
        @if($assetsPaginated->lastPage() > 1)
            <div style="display:flex;justify-content:center;align-items:center;gap:4px;margin-top:16px;flex-wrap:wrap">
                {{-- Prev --}}
                <button wire:click="gotoAssetPage({{ max(1, $assetPage - 1) }})" @if($assetPage <= 1) disabled @endif
                    style="padding:6px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;background:white;color:{{ $assetPage <= 1 ? '#d1d5db' : '#374151' }}">
                    ← Prev
                </button>

                {{-- Page numbers --}}
                @php
                    $lastPage = $assetsPaginated->lastPage();
                    $from = max(1, $assetPage - 3);
                    $to = min($lastPage, $assetPage + 3);
                @endphp
                @if($from > 1)
                    <button wire:click="gotoAssetPage(1)"
                        style="padding:6px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:12px;cursor:pointer;background:white;color:#374151">1</button>
                    @if($from > 2) <span style="color:#9ca3af;font-size:12px">…</span> @endif
                @endif
                @for($p = $from; $p <= $to; $p++)
                    <button wire:click="gotoAssetPage({{ $p }})"
                        style="padding:6px 10px;border:1px solid {{ $p === $assetPage ? '#4f46e5' : '#d1d5db' }};border-radius:6px;font-size:12px;font-weight:{{ $p === $assetPage ? '700' : '500' }};cursor:pointer;background:{{ $p === $assetPage ? '#4f46e5' : 'white' }};color:{{ $p === $assetPage ? 'white' : '#374151' }}">
                        {{ $p }}
                    </button>
                @endfor
                @if($to < $lastPage)
                    @if($to < $lastPage - 1) <span style="color:#9ca3af;font-size:12px">…</span> @endif
                    <button wire:click="gotoAssetPage({{ $lastPage }})"
                        style="padding:6px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:12px;cursor:pointer;background:white;color:#374151">{{ $lastPage }}</button>
                @endif

                {{-- Next --}}
                <button wire:click="gotoAssetPage({{ min($lastPage, $assetPage + 1) }})" @if($assetPage >= $lastPage) disabled
                @endif
                    style="padding:6px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;background:white;color:{{ $assetPage >= $lastPage ? '#d1d5db' : '#374151' }}">
                    Next →
                </button>
            </div>
        @endif
    @endif

    {{-- ═══════════════ ADD PRODUCT MODAL ═══════════════ --}}
    @if($showProductModal ?? false)
        <div style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.5)"
            wire:click.self="$set('showProductModal', false)">
            <div style="background:white;border-radius:16px;padding:24px;width:100%;max-width:600px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.2)"
                class="inv-modal">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
                    <h3 style="font-size:18px;font-weight:700">{!! $iCube !!} {{ $editingProductId ? 'Edit' : 'Add' }}
                        Product</h3>
                    <button wire:click="$set('showProductModal', false)" type="button"
                        style="background:none;border:none;font-size:20px;cursor:pointer">&times;</button>
                </div>
                <form wire:submit="submitProduct">
                    <div style="display:grid;gap:12px">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Name *</label>
                            <input type="text" wire:model="productForm.name" required
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">SKU</label>
                                <input type="text" wire:model="productForm.sku"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                            </div>
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Brand</label>
                                <input type="text" wire:model="productForm.brand"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Model
                                    #</label>
                                <input type="text" wire:model="productForm.model_number"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                            </div>
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Serial
                                    #</label>
                                <input type="text" wire:model="productForm.serial_number"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px">
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Unit</label>
                                <select wire:model="productForm.unit_of_measure"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                                    @foreach(\App\Models\Product::$units as $k => $v) <option value="{{ $k }}">{{ $v }}
                                    </option> @endforeach
                                </select>
                            </div>
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Cost
                                    Price</label>
                                <input type="number" wire:model="productForm.cost_price" step="0.01"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                            </div>
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Sell
                                    Price</label>
                                <input type="number" wire:model="productForm.selling_price" step="0.01"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px">
                            <div>
                                <label
                                    style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Condition</label>
                                <select wire:model="productForm.condition"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                                    @foreach(\App\Models\Product::$conditions as $k => $v) <option value="{{ $k }}">{{ $v }}
                                    </option> @endforeach
                                </select>
                            </div>
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Reorder
                                    Level</label>
                                <input type="number" wire:model="productForm.reorder_level"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                            </div>
                            <div>
                                <label
                                    style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Location</label>
                                <input type="text" wire:model="productForm.location"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                            </div>
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Description</label>
                            <textarea wire:model="productForm.description" rows="2"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;resize:vertical"></textarea>
                        </div>
                    </div>
                    <div
                        style="display:flex;justify-content:flex-end;gap:8px;padding-top:16px;margin-top:16px;border-top:1px solid #e5e7eb">
                        <button type="button" wire:click="$set('showProductModal', false)" class="inv-btn-cancel"
                            style="padding:8px 20px;border-radius:8px;border:1px solid #d1d5db;background:white;font-size:13px;font-weight:600;cursor:pointer">Cancel</button>
                        <button type="submit"
                            style="padding:8px 20px;border-radius:8px;border:none;background:#4f46e5;color:white;font-size:13px;font-weight:600;cursor:pointer">
                            <span wire:loading.remove
                                wire:target="submitProduct">{{ $editingProductId ? 'Save' : 'Add Product' }}</span>
                            <span wire:loading wire:target="submitProduct">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ═══════════════ ADD STORE MODAL ═══════════════ --}}
    @if($showStoreModal ?? false)
        <div style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.5)"
            wire:click.self="$set('showStoreModal', false)">
            <div style="background:white;border-radius:16px;padding:24px;width:100%;max-width:500px;box-shadow:0 20px 60px rgba(0,0,0,.2)"
                class="inv-modal">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
                    <h3 style="font-size:18px;font-weight:700">{!! $iStore !!} {{ $editingStoreId ? 'Edit' : 'Add' }} Store
                    </h3>
                    <button wire:click="$set('showStoreModal', false)" type="button"
                        style="background:none;border:none;font-size:20px;cursor:pointer">&times;</button>
                </div>
                <form wire:submit="submitStore">
                    <div style="display:grid;gap:12px">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Store Name
                                *</label>
                            <input type="text" wire:model="storeForm.name" required
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Code</label>
                                <input type="text" wire:model="storeForm.code"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                            </div>
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">City</label>
                                <input type="text" wire:model="storeForm.city"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                            </div>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Address</label>
                            <textarea wire:model="storeForm.address" rows="2"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;resize:vertical"></textarea>
                        </div>
                    </div>
                    <div
                        style="display:flex;justify-content:flex-end;gap:8px;padding-top:16px;margin-top:16px;border-top:1px solid #e5e7eb">
                        <button type="button" wire:click="$set('showStoreModal', false)" class="inv-btn-cancel"
                            style="padding:8px 20px;border-radius:8px;border:1px solid #d1d5db;background:white;font-size:13px;font-weight:600;cursor:pointer">Cancel</button>
                        <button type="submit"
                            style="padding:8px 20px;border-radius:8px;border:none;background:#059669;color:white;font-size:13px;font-weight:600;cursor:pointer">
                            <span wire:loading.remove
                                wire:target="submitStore">{{ $editingStoreId ? 'Save' : 'Add Store' }}</span>
                            <span wire:loading wire:target="submitStore">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ═══════════════ ISSUE MATERIALS MODAL ═══════════════ --}}
    @if($showIssuanceModal ?? false)
        <div style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.5)"
            wire:click.self="$set('showIssuanceModal', false)">
            <div style="background:white;border-radius:16px;padding:24px;width:100%;max-width:550px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.2)"
                class="inv-modal">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
                    <h3 style="font-size:18px;font-weight:700">{!! $iClipboard !!} Issue Materials</h3>
                    <button wire:click="$set('showIssuanceModal', false)" type="button"
                        style="background:none;border:none;font-size:20px;cursor:pointer">&times;</button>
                </div>
                <form wire:submit="submitIssuance">
                    <div style="display:grid;gap:12px">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Issued To
                                    (User)</label>
                                <x-searchable-select :wire-model="'issuanceForm.issued_to'"
                                    :options="$this->getTeamOptions()" placeholder="-- Select User --" />
                            </div>
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Or External
                                    Name</label>
                                <input type="text" wire:model="issuanceForm.issued_to_name"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                            <div>
                                <label
                                    style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Purpose</label>
                                <select wire:model="issuanceForm.purpose"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                                    @foreach(\App\Models\MaterialIssuance::$purposes as $k => $v) <option value="{{ $k }}">
                                        {{ $v }}
                                    </option> @endforeach
                                </select>
                            </div>
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">From Store
                                    *</label>
                                <x-searchable-select :wire-model="'issuanceForm.warehouse_id'"
                                    :options="$this->getStoreOptions()" placeholder="-- Select Store --" :required="true" />
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Issue
                                    Date</label>
                                <input type="date" wire:model="issuanceForm.issue_date"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                            </div>
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Expected
                                    Return</label>
                                <input type="date" wire:model="issuanceForm.expected_return_date"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                            </div>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Product *</label>
                            <div style="display:flex;gap:6px">
                                <div style="flex:1"><x-searchable-select :wire-model="'issuanceForm.product_id'"
                                        :options="$this->getProductOptions()" placeholder="-- Select Product --"
                                        :required="true" /></div>
                                <button type="button" wire:click="openQuickProduct"
                                    style="padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;background:#f9fafb;cursor:pointer;font-size:14px;font-weight:700;color:#4f46e5;height:38px"
                                    title="Add Product">+</button>
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Quantity
                                    *</label>
                                <input type="number" wire:model="issuanceForm.quantity" min="1" required
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                            </div>
                            <div>
                                <label
                                    style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Condition</label>
                                <select wire:model="issuanceForm.condition"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                                    @foreach(\App\Models\Product::$conditions as $k => $v) <option value="{{ $k }}">{{ $v }}
                                    </option> @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Notes</label>
                            <textarea wire:model="issuanceForm.notes" rows="2"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;resize:vertical"></textarea>
                        </div>
                    </div>

                    <div
                        style="display:flex;justify-content:flex-end;gap:8px;padding-top:16px;margin-top:16px;border-top:1px solid #e5e7eb">
                        <button type="button" wire:click="$set('showIssuanceModal', false)" class="inv-btn-cancel"
                            style="padding:8px 20px;border-radius:8px;border:1px solid #d1d5db;background:white;font-size:13px;font-weight:600;cursor:pointer">Cancel</button>
                        <button type="submit"
                            style="padding:8px 20px;border-radius:8px;border:none;background:#d97706;color:white;font-size:13px;font-weight:600;cursor:pointer">
                            <span wire:loading.remove wire:target="submitIssuance">Issue Materials</span>
                            <span wire:loading wire:target="submitIssuance">Issuing...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ═══════════════ REGISTER ASSET MODAL ═══════════════ --}}
    @if($showAssetModal ?? false)
        <div style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.5)"
            wire:click.self="$set('showAssetModal', false)">
            <div style="background:white;border-radius:16px;padding:24px;width:100%;max-width:560px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.2)"
                class="inv-modal">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
                    <h3 style="font-size:18px;font-weight:700">{!! $iTag !!} Register Asset</h3>
                    <button wire:click="$set('showAssetModal', false)" type="button"
                        style="background:none;border:none;font-size:20px;cursor:pointer">&times;</button>
                </div>
                <form wire:submit="submitAsset">
                    <div style="display:grid;gap:12px">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Name *</label>
                            <input type="text" wire:model="assetForm.name" required
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Product
                                    Type</label>
                                <div style="display:flex;gap:6px">
                                    <div style="flex:1"><x-searchable-select :wire-model="'assetForm.product_id'"
                                            :options="$this->getProductOptions()" placeholder="-- Select Product --" />
                                    </div>
                                    <button type="button" wire:click="openQuickProduct"
                                        style="padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;background:#f9fafb;cursor:pointer;font-size:14px;font-weight:700;color:#4f46e5;height:38px"
                                        title="Add Product">+</button>
                                </div>
                            </div>
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Serial
                                    Number</label>
                                <input type="text" wire:model="assetForm.serial_number"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                            <div>
                                <label
                                    style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Condition</label>
                                <select wire:model="assetForm.condition"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                                    @foreach(\App\Models\Asset::$conditions as $k => $v) <option value="{{ $k }}">{{ $v }}
                                    </option> @endforeach
                                </select>
                            </div>
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Store /
                                    Warehouse</label>
                                <x-searchable-select :wire-model="'assetForm.warehouse_id'"
                                    :options="$this->getStoreOptions()" placeholder="-- Select Store --" />
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px">
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Purchase
                                    Date</label>
                                <input type="date" wire:model="assetForm.purchase_date"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                            </div>
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Purchase
                                    Cost</label>
                                <input type="number" wire:model="assetForm.purchase_cost" step="0.01"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                            </div>
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Warranty
                                    Expiry</label>
                                <input type="date" wire:model="assetForm.warranty_expiry"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Useful Life
                                    (years)</label>
                                <input type="number" wire:model="assetForm.useful_life_years" min="1"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                            </div>
                            <div>
                                <label
                                    style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Location</label>
                                <input type="text" wire:model="assetForm.current_location"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                            </div>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Notes</label>
                            <textarea wire:model="assetForm.notes" rows="2"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;resize:vertical"></textarea>
                        </div>
                    </div>
                    <div
                        style="display:flex;justify-content:flex-end;gap:8px;padding-top:16px;margin-top:16px;border-top:1px solid #e5e7eb">
                        <button type="button" wire:click="$set('showAssetModal', false)" class="inv-btn-cancel"
                            style="padding:8px 20px;border-radius:8px;border:1px solid #d1d5db;background:white;font-size:13px;font-weight:600;cursor:pointer">Cancel</button>
                        <button type="submit"
                            style="padding:8px 20px;border-radius:8px;border:none;background:#7c3aed;color:white;font-size:13px;font-weight:600;cursor:pointer">
                            <span wire:loading.remove wire:target="submitAsset">Register Asset</span>
                            <span wire:loading wire:target="submitAsset">Registering...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ═══════════════ CHECKOUT ASSET MODAL ═══════════════ --}}
    @if($showCheckoutModal ?? false)
        <div style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.5)"
            wire:click.self="$set('showCheckoutModal', false)">
            <div style="background:white;border-radius:16px;padding:24px;width:100%;max-width:450px;box-shadow:0 20px 60px rgba(0,0,0,.2)"
                class="inv-modal">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
                    <h3 style="font-size:18px;font-weight:700">{!! $iCheckout !!} Checkout Asset</h3>
                    <button wire:click="$set('showCheckoutModal', false)" type="button"
                        style="background:none;border:none;font-size:20px;cursor:pointer">&times;</button>
                </div>
                <form wire:submit="submitCheckout">
                    <div style="display:grid;gap:12px">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Assign To
                                (User)</label>
                            <x-searchable-select :wire-model="'checkoutForm.assigned_to'" :options="$this->getTeamOptions()"
                                placeholder="-- Select User --" />
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Or External
                                Person</label>
                            <input type="text" wire:model="checkoutForm.assigned_to_name"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                            <div>
                                <label
                                    style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Location</label>
                                <input type="text" wire:model="checkoutForm.location"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                            </div>
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Expected
                                    Return</label>
                                <input type="date" wire:model="checkoutForm.expected_return_date"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                            </div>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Notes</label>
                            <textarea wire:model="checkoutForm.notes" rows="2"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;resize:vertical"></textarea>
                        </div>
                    </div>
                    <div
                        style="display:flex;justify-content:flex-end;gap:8px;padding-top:16px;margin-top:16px;border-top:1px solid #e5e7eb">
                        <button type="button" wire:click="$set('showCheckoutModal', false)" class="inv-btn-cancel"
                            style="padding:8px 20px;border-radius:8px;border:1px solid #d1d5db;background:white;font-size:13px;font-weight:600;cursor:pointer">Cancel</button>
                        <button type="submit"
                            style="padding:8px 20px;border-radius:8px;border:none;background:#2563eb;color:white;font-size:13px;font-weight:600;cursor:pointer">
                            <span wire:loading.remove wire:target="submitCheckout">Checkout</span>
                            <span wire:loading wire:target="submitCheckout">Processing...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ═══════════════ MAINTENANCE LOG MODAL (Enhanced) ═══════════════ --}}
    @if($showMaintenanceModal ?? false)
        <div style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.5)"
            wire:click.self="$set('showMaintenanceModal', false)">
            <div style="background:white;border-radius:16px;padding:24px;width:100%;max-width:540px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.2)"
                class="inv-modal">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
                    <h3 style="font-size:18px;font-weight:700;display:flex;align-items:center;gap:6px">{!! $iWrench !!} Log
                        Maintenance</h3>
                    <button wire:click="$set('showMaintenanceModal', false)" type="button"
                        style="background:none;border:none;font-size:20px;cursor:pointer">&times;</button>
                </div>
                <form wire:submit="submitMaintenance">
                    <div style="display:grid;gap:12px">
                        {{-- Row 1: Type + Priority --}}
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                            <div>
                                <label
                                    style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Type
                                    *</label>
                                <select wire:model="maintenanceForm.type" required
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;box-sizing:border-box">
                                    @foreach(\App\Models\AssetMaintenanceLog::$types as $k => $v) <option value="{{ $k }}">
                                        {{ $v }}
                                    </option> @endforeach
                                </select>
                            </div>
                            <div>
                                <label
                                    style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Priority</label>
                                <select wire:model="maintenanceForm.priority"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;box-sizing:border-box">
                                    @foreach(\App\Models\AssetMaintenanceLog::$priorities as $k => $v) <option
                                    value="{{ $k }}">{{ $v }}</option> @endforeach
                                </select>
                            </div>
                        </div>
                        {{-- Title --}}
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Title
                                *</label>
                            <input type="text" wire:model="maintenanceForm.title" required
                                placeholder="e.g. Engine oil change"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;box-sizing:border-box">
                        </div>
                        {{-- Row 2: Cost + Vendor --}}
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                            <div>
                                <label
                                    style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Cost
                                    ($)</label>
                                <input type="number" wire:model="maintenanceForm.cost" step="0.01" min="0"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;box-sizing:border-box">
                            </div>
                            <div>
                                <label
                                    style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Vendor
                                    / Technician</label>
                                <input type="text" wire:model="maintenanceForm.vendor" placeholder="Service provider"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;box-sizing:border-box">
                            </div>
                        </div>
                        {{-- Row 3: Condition After + Downtime --}}
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                            <div>
                                <label
                                    style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Condition
                                    After</label>
                                <select wire:model="maintenanceForm.condition_after"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;box-sizing:border-box">
                                    @foreach(\App\Models\Asset::$conditions as $k => $v) <option value="{{ $k }}">{{ $v }}
                                    </option> @endforeach
                                </select>
                            </div>
                            <div>
                                <label
                                    style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Downtime
                                    (Hours)</label>
                                <input type="number" wire:model="maintenanceForm.downtime_hours" step="0.5" min="0"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;box-sizing:border-box">
                            </div>
                        </div>
                        {{-- Row 4: Meter Reading + Scheduled Date --}}
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                            <div>
                                <label
                                    style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Meter
                                    / Hour Reading</label>
                                <input type="number" wire:model="maintenanceForm.meter_reading" step="0.1" min="0"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;box-sizing:border-box">
                            </div>
                            <div>
                                <label
                                    style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Next
                                    Service Due</label>
                                <input type="date" wire:model="maintenanceForm.next_service_date"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;box-sizing:border-box">
                            </div>
                        </div>
                        {{-- Parts Used --}}
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Parts
                                / Materials Used</label>
                            <input type="text" wire:model="maintenanceForm.parts_used"
                                placeholder="e.g. Oil filter, 5L engine oil"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;box-sizing:border-box">
                        </div>
                        {{-- Description --}}
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Description</label>
                            <textarea wire:model="maintenanceForm.description" rows="2" placeholder="Work performed..."
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;resize:vertical;box-sizing:border-box"></textarea>
                        </div>
                    </div>
                    <div
                        style="display:flex;justify-content:flex-end;gap:8px;padding-top:16px;margin-top:16px;border-top:1px solid #e5e7eb">
                        <button type="button" wire:click="$set('showMaintenanceModal', false)"
                            style="padding:8px 20px;border-radius:8px;border:1px solid #d1d5db;background:white;font-size:13px;font-weight:600;cursor:pointer">Cancel</button>
                        <button type="submit"
                            style="padding:8px 20px;border-radius:8px;border:none;background:#d97706;color:white;font-size:13px;font-weight:600;cursor:pointer">
                            <span wire:loading.remove wire:target="submitMaintenance">{!! $iWrench !!} Log
                                Maintenance</span>
                            <span wire:loading wire:target="submitMaintenance">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ═══════════════ GRN MODAL (Full Page) ═══════════════ --}}
    @if($showGRNModal ?? false)
        <div style="position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.4)">
            <div class="inv-modal" style="position:absolute;inset:0;background:white;overflow-y:auto;padding:32px 48px">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
                    <div>
                        <h3 style="font-size:20px;font-weight:800;margin:0">{!! $iInbox !!} Goods Received Note</h3>
                        <div style="font-size:12px;color:#9ca3af;margin-top:2px">Record received goods and update stock
                        </div>
                    </div>
                    <button wire:click="$set('showGRNModal', false)" type="button"
                        style="background:none;border:none;font-size:24px;cursor:pointer;color:#9ca3af">&times;</button>
                </div>

                <form wire:submit="submitGRN">
                    <div
                        style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:20px;padding-bottom:16px;border-bottom:2px solid #e5e7eb">
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Against
                                PO</label>
                            <x-searchable-select :wire-model="'grnHeader.purchase_order_id'"
                                :options="$this->getAvailablePOs()" placeholder="-- No PO (Open Receipt) --" />
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Receiving
                                Warehouse *</label>
                            <div style="display:flex;gap:6px">
                                <div style="flex:1"><x-searchable-select :wire-model="'grnHeader.warehouse_id'"
                                        :options="$this->getWarehouseOptions()" placeholder="-- Select --"
                                        :required="true" /></div>
                                <button type="button" wire:click="openQuickWarehouse"
                                    style="padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;background:#f9fafb;cursor:pointer;font-size:14px;font-weight:700;color:#4f46e5"
                                    title="Add Warehouse">+</button>
                            </div>
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Received
                                Date</label>
                            <input type="date" wire:model="grnHeader.received_date"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Delivery
                                Note Ref</label>
                            <input type="text" wire:model="grnHeader.delivery_note_ref" placeholder="Supplier DN #"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                        </div>
                        <div style="grid-column:span 2">
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Notes</label>
                            <input type="text" wire:model="grnHeader.notes" placeholder="Optional notes..."
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                        </div>
                    </div>

                    <div style="margin-bottom:16px">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
                            <label style="font-size:13px;font-weight:700">{!! $iCube !!} Received Items</label>
                            <button type="button" wire:click="addGRNItem"
                                style="display:inline-flex;align-items:center;gap:4px;padding:6px 14px;font-size:12px;font-weight:600;border-radius:6px;border:1px dashed #059669;background:#ecfdf5;color:#059669;cursor:pointer">+
                                Add Line</button>
                        </div>
                        <table class="inv-table" style="width:100%;border-collapse:collapse;font-size:13px">
                            <thead>
                                <tr style="background:#f9fafb;border-bottom:2px solid #e5e7eb">
                                    <th style="padding:8px 6px;text-align:left;font-weight:700;color:#6b7280;width:30px">#
                                    </th>
                                    <th style="padding:8px 6px;text-align:left;font-weight:700;color:#6b7280">Product</th>
                                    <th style="padding:8px 6px;text-align:left;font-weight:700;color:#6b7280">Description
                                    </th>
                                    <th style="padding:8px 6px;text-align:right;font-weight:700;color:#6b7280;width:80px">
                                        Expected</th>
                                    <th style="padding:8px 6px;text-align:right;font-weight:700;color:#6b7280;width:80px">
                                        Received</th>
                                    <th style="padding:8px 6px;text-align:right;font-weight:700;color:#6b7280;width:80px">
                                        Rejected</th>
                                    <th style="padding:8px 6px;text-align:left;font-weight:700;color:#6b7280;width:100px">
                                        Condition</th>
                                    <th style="padding:8px 6px;width:40px"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($grnItems as $idx => $item)
                                    <tr style="border-bottom:1px solid #f3f4f6">
                                        <td style="padding:4px 6px;color:#9ca3af;font-weight:600">{{ $idx + 1 }}</td>
                                        <td style="padding:4px 6px">
                                            <x-searchable-select :wire-model=\"'grnItems.'.$idx.'.product_id'\"
                                                :options=\"$this->getProductOptions()\" placeholder=\"-- Optional --\" />
                                        </td>
                                        <td style="padding:4px 6px"><input type="text"
                                                wire:model="grnItems.{{ $idx }}.description"
                                                style="width:100%;padding:6px 8px;border:1px solid #e5e7eb;border-radius:6px;font-size:13px;background:transparent">
                                        </td>
                                        <td style="padding:4px 6px"><input type="number"
                                                wire:model="grnItems.{{ $idx }}.qty_expected" min="0" step="any"
                                                style="width:100%;padding:6px 8px;border:1px solid #e5e7eb;border-radius:6px;font-size:13px;text-align:right;background:#f9fafb"
                                                readonly></td>
                                        <td style="padding:4px 6px"><input type="number"
                                                wire:model="grnItems.{{ $idx }}.qty_received" min="0" step="any"
                                                style="width:100%;padding:6px 8px;border:1px solid #e5e7eb;border-radius:6px;font-size:13px;text-align:right;background:transparent">
                                        </td>
                                        <td style="padding:4px 6px"><input type="number"
                                                wire:model="grnItems.{{ $idx }}.qty_rejected" min="0" step="any"
                                                style="width:100%;padding:6px 8px;border:1px solid #e5e7eb;border-radius:6px;font-size:13px;text-align:right;background:transparent">
                                        </td>
                                        <td style="padding:4px 6px">
                                            <select wire:model="grnItems.{{ $idx }}.condition"
                                                style="width:100%;padding:6px 8px;border:1px solid #e5e7eb;border-radius:6px;font-size:12px;background:transparent">
                                                <option value="good">Good</option>
                                                <option value="damaged">Damaged</option>
                                                <option value="defective">Defective</option>
                                            </select>
                                        </td>
                                        <td style="padding:4px 6px">@if(count($grnItems) > 1)<button type="button"
                                            wire:click="removeGRNItem({{ $idx }})"
                                        style="background:none;border:none;cursor:pointer;color:#ef4444;font-size:16px">✕</button>@endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div
                        style="display:flex;justify-content:flex-end;gap:8px;padding-top:16px;border-top:1px solid #e5e7eb">
                        <button type="button" wire:click="$set('showGRNModal', false)" class="inv-btn-cancel"
                            style="padding:10px 24px;border-radius:8px;border:1px solid #d1d5db;background:white;font-size:13px;font-weight:600;cursor:pointer">Cancel</button>
                        <button type="submit"
                            style="padding:10px 24px;border-radius:8px;border:none;background:#059669;color:white;font-size:13px;font-weight:700;cursor:pointer">
                            <span wire:loading.remove wire:target="submitGRN">{!! $iInbox !!} Receive Goods</span>
                            <span wire:loading wire:target="submitGRN">Processing...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ═══════════════ STOCK TRANSFER MODAL ═══════════════ --}}
    @if($showTransferModal ?? false)
        <div style="position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.4)">
            <div class="inv-modal" style="position:absolute;inset:0;background:white;overflow-y:auto;padding:32px 48px">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
                    <div>
                        <h3 style="font-size:20px;font-weight:800;margin:0">{!! $iArrows !!} Stock Transfer</h3>
                        <div style="font-size:12px;color:#9ca3af;margin-top:2px">Move products between warehouses</div>
                    </div>
                    <button wire:click="$set('showTransferModal', false)" type="button"
                        style="background:none;border:none;font-size:24px;cursor:pointer;color:#9ca3af">&times;</button>
                </div>

                <form wire:submit="submitTransfer">
                    <div
                        style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:20px;padding-bottom:16px;border-bottom:2px solid #e5e7eb">
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">From
                                Warehouse *</label>
                            <div style="display:flex;gap:6px">
                                <div style="flex:1"><x-searchable-select :wire-model="'transferHeader.from_warehouse_id'"
                                        :options="$this->getWarehouseOptions()" placeholder="-- Select --"
                                        :required="true" /></div>
                                <button type="button" wire:click="openQuickWarehouse"
                                    style="padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;background:#f9fafb;cursor:pointer;font-size:14px;font-weight:700;color:#4f46e5"
                                    title="Add Warehouse">+</button>
                            </div>
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">To
                                Warehouse *</label>
                            <div style="display:flex;gap:6px">
                                <div style="flex:1"><x-searchable-select :wire-model="'transferHeader.to_warehouse_id'"
                                        :options="$this->getWarehouseOptions()" placeholder="-- Select --"
                                        :required="true" /></div>
                                <button type="button" wire:click="openQuickWarehouse"
                                    style="padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;background:#f9fafb;cursor:pointer;font-size:14px;font-weight:700;color:#4f46e5"
                                    title="Add Warehouse">+</button>
                            </div>
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Priority</label>
                            <select wire:model="transferHeader.priority"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                                @foreach(\App\Models\StockTransfer::$priorities as $k => $v) <option value="{{ $k }}">
                                    {{ $v }}
                                </option> @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Reason</label>
                            <input type="text" wire:model="transferHeader.reason"
                                placeholder="Project requirement, restock..."
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                        </div>
                        <div style="grid-column:span 2">
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Notes</label>
                            <input type="text" wire:model="transferHeader.notes" placeholder="Optional..."
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                        </div>
                    </div>

                    <div style="margin-bottom:16px">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
                            <label style="font-size:13px;font-weight:700">{!! $iCube !!} Transfer Items</label>
                            <button type="button" wire:click="addTransferItem"
                                style="display:inline-flex;align-items:center;gap:4px;padding:6px 14px;font-size:12px;font-weight:600;border-radius:6px;border:1px dashed #7c3aed;background:#f5f3ff;color:#7c3aed;cursor:pointer">+
                                Add Product</button>
                        </div>
                        <table class="inv-table" style="width:100%;border-collapse:collapse;font-size:13px">
                            <thead>
                                <tr style="background:#f9fafb;border-bottom:2px solid #e5e7eb">
                                    <th style="padding:8px 10px;text-align:left;font-weight:700;color:#6b7280;width:30px">#
                                    </th>
                                    <th style="padding:8px 10px;text-align:left;font-weight:700;color:#6b7280">Product</th>
                                    <th style="padding:8px 10px;text-align:right;font-weight:700;color:#6b7280;width:120px">
                                        Quantity</th>
                                    <th style="padding:8px 10px;width:40px"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transferItems as $idx => $item)
                                    <tr style="border-bottom:1px solid #f3f4f6">
                                        <td style="padding:6px 10px;color:#9ca3af;font-weight:600">{{ $idx + 1 }}</td>
                                        <td style="padding:6px 10px">
                                            <x-searchable-select :wire-model="'transferItems.'.$idx.'.product_id'"
                                                :options="$this->getProductOptions()" placeholder="-- Select --"
                                                :required="true" />
                                        </td>
                                        <td style="padding:6px 10px"><input type="number"
                                                wire:model="transferItems.{{ $idx }}.quantity" min="1" step="any"
                                                style="width:100%;padding:6px 8px;border:1px solid #e5e7eb;border-radius:6px;font-size:13px;text-align:right;background:transparent">
                                        </td>
                                        <td style="padding:6px 10px">@if(count($transferItems) > 1)<button type="button"
                                            wire:click="removeTransferItem({{ $idx }})"
                                        style="background:none;border:none;cursor:pointer;color:#ef4444;font-size:16px">✕</button>@endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div
                        style="display:flex;justify-content:flex-end;gap:8px;padding-top:16px;border-top:1px solid #e5e7eb">
                        <button type="button" wire:click="$set('showTransferModal', false)" class="inv-btn-cancel"
                            style="padding:10px 24px;border-radius:8px;border:1px solid #d1d5db;background:white;font-size:13px;font-weight:600;cursor:pointer">Cancel</button>
                        <button type="submit"
                            style="padding:10px 24px;border-radius:8px;border:none;background:#7c3aed;color:white;font-size:13px;font-weight:700;cursor:pointer">
                            <span wire:loading.remove wire:target="submitTransfer">{!! $iArrows !!} Complete Transfer</span>
                            <span wire:loading wire:target="submitTransfer">Processing...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ═══════════════ STOCK ADJUSTMENT MODAL ═══════════════ --}}
    @if($showAdjustmentModal ?? false)
        <div
            style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.5)">
            <div class="inv-modal"
                style="background:white;border-radius:16px;padding:28px;width:100%;max-width:560px;box-shadow:0 20px 60px rgba(0,0,0,.25)">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
                    <div>
                        <h3 style="font-size:20px;font-weight:800;margin:0">{!! $iScale !!} Stock Adjustment</h3>
                        <div style="font-size:12px;color:#9ca3af;margin-top:2px">Record physical count, damage, or
                            correction</div>
                    </div>
                    <button wire:click="$set('showAdjustmentModal', false)" type="button"
                        style="background:none;border:none;font-size:24px;cursor:pointer;color:#9ca3af">&times;</button>
                </div>

                <form wire:submit="submitAdjustment">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px">
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Warehouse
                                *</label>
                            <div style="display:flex;gap:6px">
                                <div style="flex:1"><x-searchable-select :wire-model="'adjustmentForm.warehouse_id'"
                                        :options="$this->getWarehouseOptions()" placeholder="-- Select --"
                                        :required="true" /></div>
                                <button type="button" wire:click="openQuickWarehouse"
                                    style="padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;background:#f9fafb;cursor:pointer;font-size:14px;font-weight:700;color:#4f46e5"
                                    title="Add Warehouse">+</button>
                            </div>
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Product
                                *</label>
                            <div style="display:flex;gap:6px">
                                <div style="flex:1"><x-searchable-select :wire-model="'adjustmentForm.product_id'"
                                        :options="$this->getProductOptions()" placeholder="-- Select Product --"
                                        :required="true" /></div>
                                <button type="button" wire:click="openQuickProduct"
                                    style="padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;background:#f9fafb;cursor:pointer;font-size:14px;font-weight:700;color:#4f46e5;height:38px"
                                    title="Add Product">+</button>
                            </div>
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Type
                                *</label>
                            <select wire:model="adjustmentForm.type" required
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                                @foreach(\App\Models\StockAdjustment::$types as $k => $v) <option value="{{ $k }}">{{ $v }}
                                </option> @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">New
                                Quantity *</label>
                            <input type="number" wire:model="adjustmentForm.new_quantity" min="0" step="any" required
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;font-weight:700">
                        </div>
                        <div style="grid-column:span 2">
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Reason
                                *</label>
                            <input type="text" wire:model="adjustmentForm.reason" required
                                placeholder="Reason for adjustment..."
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                        </div>
                        <div style="grid-column:span 2">
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Notes</label>
                            <input type="text" wire:model="adjustmentForm.notes" placeholder="Additional details..."
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                        </div>
                    </div>

                    <div
                        style="display:flex;justify-content:flex-end;gap:8px;padding-top:16px;border-top:1px solid #e5e7eb">
                        <button type="button" wire:click="$set('showAdjustmentModal', false)" class="inv-btn-cancel"
                            style="padding:10px 24px;border-radius:8px;border:1px solid #d1d5db;background:white;font-size:13px;font-weight:600;cursor:pointer">Cancel</button>
                        <button type="submit"
                            style="padding:10px 24px;border-radius:8px;border:none;background:#dc2626;color:white;font-size:13px;font-weight:700;cursor:pointer">
                            <span wire:loading.remove wire:target="submitAdjustment">{!! $iScale !!} Apply Adjustment</span>
                            <span wire:loading wire:target="submitAdjustment">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ═══════════════ PO BUILDER MODAL (Invoice-Style) ═══════════════ --}}
    @if($showPOModal ?? false)
        <div style="position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.4)">
            <div class="inv-modal" style="position:absolute;inset:0;background:white;overflow-y:auto;padding:32px 48px">

                {{-- Title --}}
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
                    <div>
                        <h3 style="font-size:20px;font-weight:800;margin:0">{!! $iCart !!}
                            {{ $editingPOId ? 'Edit' : 'New' }} Purchase
                            Order
                        </h3>
                        <div style="font-size:12px;color:#9ca3af;margin-top:2px">Add items like a modern invoice</div>
                    </div>
                    <button wire:click="$set('showPOModal', false)" type="button"
                        style="background:none;border:none;font-size:24px;cursor:pointer;color:#9ca3af">&times;</button>
                </div>

                <form wire:submit="submitPO">
                    {{-- Header Fields --}}
                    <div
                        style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:20px;padding-bottom:16px;border-bottom:2px solid #e5e7eb">
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">PO
                                Number *</label>
                            <input type="text" wire:model="poHeader.po_number" required
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;font-weight:700">
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Supplier</label>
                            <div style="display:flex;gap:6px">
                                <div style="flex:1"><x-searchable-select :wire-model="'poHeader.supplier_id'"
                                        :options="$this->getSupplierOptions()" placeholder="-- Select Supplier --" /></div>
                                <button type="button" wire:click="openQuickSupplier"
                                    style="padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;background:#f9fafb;cursor:pointer;font-size:14px;font-weight:700;color:#4f46e5"
                                    title="Add Supplier">+</button>
                            </div>
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Status</label>
                            <select wire:model="poHeader.status"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                                @foreach(\App\Models\PurchaseOrder::$statuses as $k => $v) <option value="{{ $k }}">{{ $v }}
                                </option> @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Order
                                Date</label>
                            <input type="date" wire:model="poHeader.order_date"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Expected
                                Delivery</label>
                            <input type="date" wire:model="poHeader.expected_date"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Notes</label>
                            <input type="text" wire:model="poHeader.notes" placeholder="Optional notes..."
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                        </div>
                    </div>

                    {{-- Line Items Table --}}
                    <div style="margin-bottom:16px">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
                            <label style="font-size:13px;font-weight:700">{!! $iCube !!} Line Items</label>
                            <button type="button" wire:click="addPOItem"
                                style="display:inline-flex;align-items:center;gap:4px;padding:6px 14px;font-size:12px;font-weight:600;border-radius:6px;border:1px dashed #6366f1;background:#eef2ff;color:#4f46e5;cursor:pointer">
                                + Add Line
                            </button>
                        </div>

                        <table class="inv-table" style="width:100%;border-collapse:collapse;font-size:13px">
                            <thead>
                                <tr style="background:#f9fafb;border-bottom:2px solid #e5e7eb">
                                    <th style="padding:8px 10px;text-align:left;font-weight:700;color:#6b7280;width:30px">#
                                    </th>
                                    <th style="padding:8px 10px;text-align:left;font-weight:700;color:#6b7280">Product</th>
                                    <th style="padding:8px 10px;text-align:left;font-weight:700;color:#6b7280">Description
                                    </th>
                                    <th style="padding:8px 10px;text-align:right;font-weight:700;color:#6b7280;width:80px">
                                        Qty</th>
                                    <th style="padding:8px 10px;text-align:right;font-weight:700;color:#6b7280;width:110px">
                                        Unit Price</th>
                                    <th style="padding:8px 10px;text-align:right;font-weight:700;color:#6b7280;width:100px">
                                        Total</th>
                                    <th style="padding:8px 10px;width:40px"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($poItems as $idx => $item)
                                    <tr style="border-bottom:1px solid #f3f4f6">
                                        <td style="padding:6px 10px;color:#9ca3af;font-weight:600">{{ $idx + 1 }}</td>
                                        <td style="padding:6px 10px">
                                            <x-searchable-select :wire-model="'poItems.'.$idx.'.product_id'"
                                                :options="$this->getProductOptions()" placeholder="-- Optional --" />
                                        </td>
                                        <td style="padding:6px 10px">
                                            <input type="text" wire:model="poItems.{{ $idx }}.description"
                                                placeholder="Description..."
                                                style="width:100%;padding:6px 8px;border:1px solid #e5e7eb;border-radius:6px;font-size:13px;background:transparent">
                                        </td>
                                        <td style="padding:6px 10px">
                                            <input type="number" wire:model.live="poItems.{{ $idx }}.quantity" min="0"
                                                step="any"
                                                style="width:100%;padding:6px 8px;border:1px solid #e5e7eb;border-radius:6px;font-size:13px;text-align:right;background:transparent">
                                        </td>
                                        <td style="padding:6px 10px">
                                            <input type="number" wire:model.live="poItems.{{ $idx }}.unit_price" min="0"
                                                step="0.01"
                                                style="width:100%;padding:6px 8px;border:1px solid #e5e7eb;border-radius:6px;font-size:13px;text-align:right;background:transparent">
                                        </td>
                                        <td style="padding:6px 10px;text-align:right;font-weight:700;font-family:monospace">
                                            {{ \App\Support\CurrencyHelper::format(((float) ($item['quantity'] ?? 0)) * ((float) ($item['unit_price'] ?? 0)), 2) }}
                                        </td>
                                        <td style="padding:6px 10px">
                                            @if(count($poItems) > 1)
                                                <button type="button" wire:click="removePOItem({{ $idx }})"
                                                    style="background:none;border:none;cursor:pointer;color:#ef4444;font-size:16px"
                                                    title="Remove">✕</button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Totals --}}
                    <div style="display:flex;justify-content:flex-end;margin-bottom:16px">
                        <div style="width:300px;background:#f9fafb;border-radius:10px;padding:14ox;border:1px solid #e5e7eb"
                            class="inv-card">
                            <div style="padding:14px">
                                @php
                                    $subtotal = $this->getPOSubtotal();
                                    $total = $this->getPOTotal();
                                @endphp
                                <div style="display:flex;justify-content:space-between;padding:4px 0;font-size:13px">
                                    <span class="inv-label" style="color:#6b7280">Subtotal</span>
                                    <span
                                        style="font-weight:600;font-family:monospace">{{ \App\Support\CurrencyHelper::format($subtotal, 2) }}</span>
                                </div>
                                <div
                                    style="display:flex;justify-content:space-between;padding:4px 0;font-size:13px;align-items:center">
                                    <span class="inv-label" style="color:#6b7280">Tax</span>
                                    <input type="number" wire:model.live="poHeader.tax_amount" step="0.01" min="0"
                                        style="width:100px;padding:4px 8px;border:1px solid #e5e7eb;border-radius:6px;font-size:13px;text-align:right;background:transparent">
                                </div>
                                <div
                                    style="display:flex;justify-content:space-between;padding:4px 0;font-size:13px;align-items:center">
                                    <span class="inv-label" style="color:#6b7280">Shipping</span>
                                    <input type="number" wire:model.live="poHeader.shipping_cost" step="0.01" min="0"
                                        style="width:100px;padding:4px 8px;border:1px solid #e5e7eb;border-radius:6px;font-size:13px;text-align:right;background:transparent">
                                </div>
                                <div
                                    style="display:flex;justify-content:space-between;padding:8px 0 0;margin-top:6px;border-top:2px solid #4f46e5;font-size:16px;font-weight:800">
                                    <span>Total</span>
                                    <span
                                        style="color:#4f46e5;font-family:monospace">{{ \App\Support\CurrencyHelper::format($total, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div
                        style="display:flex;justify-content:flex-end;gap:8px;padding-top:16px;border-top:1px solid #e5e7eb">
                        <button type="button" wire:click="$set('showPOModal', false)" class="inv-btn-cancel"
                            style="padding:10px 24px;border-radius:8px;border:1px solid #d1d5db;background:white;font-size:13px;font-weight:600;cursor:pointer">Cancel</button>
                        <button type="submit"
                            style="padding:10px 24px;border-radius:8px;border:none;background:#4f46e5;color:white;font-size:13px;font-weight:700;cursor:pointer">
                            <span wire:loading.remove
                                wire:target="submitPO">{!! $editingPOId ? $iSave . ' Update PO' : $iCart . ' Create PO' !!}</span>
                            <span wire:loading wire:target="submitPO">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ═══════════════ QUICK CREATE WAREHOUSE MODAL ═══════════════ --}}
    @if($showQuickWarehouseModal ?? false)
        <div style="position:fixed;inset:0;z-index:10000;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.5)"
            wire:click.self="$set('showQuickWarehouseModal', false)">
            <div class="inv-modal"
                style="background:white;border-radius:16px;padding:24px;width:100%;max-width:440px;box-shadow:0 20px 60px rgba(0,0,0,.3)">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
                    <h3 style="font-size:18px;font-weight:800;margin:0">{!! $iStore !!} New Warehouse</h3>
                    <button wire:click="$set('showQuickWarehouseModal', false)" type="button"
                        style="background:none;border:none;font-size:22px;cursor:pointer;color:#9ca3af">&times;</button>
                </div>
                <form wire:submit="submitQuickWarehouse">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px">
                        <div style="grid-column:span 2">
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Name
                                *</label>
                            <input type="text" wire:model="quickWarehouseForm.name" required placeholder="Main Warehouse"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Code</label>
                            <input type="text" wire:model="quickWarehouseForm.code" placeholder="WH-01"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">City</label>
                            <input type="text" wire:model="quickWarehouseForm.city" placeholder="Kampala"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                        </div>
                        <div style="grid-column:span 2">
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Address</label>
                            <input type="text" wire:model="quickWarehouseForm.address" placeholder="Optional address..."
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                        </div>
                    </div>
                    <div
                        style="display:flex;justify-content:flex-end;gap:8px;padding-top:12px;border-top:1px solid #e5e7eb">
                        <button type="button" wire:click="$set('showQuickWarehouseModal', false)" class="inv-btn-cancel"
                            style="padding:8px 20px;border-radius:8px;border:1px solid #d1d5db;background:white;font-size:13px;font-weight:600;cursor:pointer">Cancel</button>
                        <button type="submit"
                            style="padding:8px 20px;border-radius:8px;border:none;background:#4f46e5;color:white;font-size:13px;font-weight:700;cursor:pointer">
                            <span wire:loading.remove wire:target="submitQuickWarehouse">{!! $iStore !!} Create</span>
                            <span wire:loading wire:target="submitQuickWarehouse">Creating...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ═══════════════ QUICK CREATE SUPPLIER MODAL ═══════════════ --}}
    @if($showQuickSupplierModal ?? false)
        <div style="position:fixed;inset:0;z-index:10000;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.5)"
            wire:click.self="$set('showQuickSupplierModal', false)">
            <div class="inv-modal"
                style="background:white;border-radius:16px;padding:24px;width:100%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,.3)">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
                    <h3 style="font-size:18px;font-weight:800;margin:0">
                        {!! $ico('M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z', 18) !!}
                        New Supplier
                    </h3>
                    <button wire:click="$set('showQuickSupplierModal', false)" type="button"
                        style="background:none;border:none;font-size:22px;cursor:pointer;color:#9ca3af">&times;</button>
                </div>
                <form wire:submit="submitQuickSupplier">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px">
                        <div style="grid-column:span 2">
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Supplier
                                Name *</label>
                            <input type="text" wire:model="quickSupplierForm.name" required placeholder="ABC Supplies Ltd"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Contact
                                Person</label>
                            <input type="text" wire:model="quickSupplierForm.contact_person" placeholder="John Doe"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Phone</label>
                            <input type="text" wire:model="quickSupplierForm.phone" placeholder="+256 700 000000"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                        </div>
                        <div style="grid-column:span 2">
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Email</label>
                            <input type="email" wire:model="quickSupplierForm.email" placeholder="supplier@example.com"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                        </div>
                    </div>
                    <div
                        style="display:flex;justify-content:flex-end;gap:8px;padding-top:12px;border-top:1px solid #e5e7eb">
                        <button type="button" wire:click="$set('showQuickSupplierModal', false)" class="inv-btn-cancel"
                            style="padding:8px 20px;border-radius:8px;border:1px solid #d1d5db;background:white;font-size:13px;font-weight:600;cursor:pointer">Cancel</button>
                        <button type="submit"
                            style="padding:8px 20px;border-radius:8px;border:none;background:#4f46e5;color:white;font-size:13px;font-weight:700;cursor:pointer">
                            <span wire:loading.remove wire:target="submitQuickSupplier">{!! $iPlus !!} Create</span>
                            <span wire:loading wire:target="submitQuickSupplier">Creating...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ═══════════ QUICK-ADD PRODUCT MODAL ═══════════ --}}
    @if($showQuickProductModal ?? false)
        <div style="position:fixed;inset:0;z-index:10000;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.5)"
            wire:click.self="$set('showQuickProductModal', false)">
            <div class="inv-modal"
                style="background:white;border-radius:16px;padding:24px;width:100%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,.3)">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
                    <h3 style="font-size:18px;font-weight:800;margin:0">
                        {!! $ico('M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z', 18) !!}
                        New Product
                    </h3>
                    <button wire:click="$set('showQuickProductModal', false)" type="button"
                        style="background:none;border:none;font-size:22px;cursor:pointer;color:#9ca3af">&times;</button>
                </div>
                <form wire:submit="submitQuickProduct">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px">
                        <div style="grid-column:span 2">
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Product
                                Name *</label>
                            <input type="text" wire:model="quickProductForm.name" required
                                placeholder="Portland Cement 50kg"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;box-sizing:border-box">
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">SKU</label>
                            <input type="text" wire:model="quickProductForm.sku" placeholder="CEM-50KG"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;box-sizing:border-box">
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Unit</label>
                            <select wire:model="quickProductForm.unit_of_measure"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;box-sizing:border-box">
                                @foreach(\App\Models\Product::$units as $k => $v)
                                    <option value="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="grid-column:span 2">
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Cost
                                Price</label>
                            <input type="number" wire:model="quickProductForm.cost_price" step="0.01" min="0"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;box-sizing:border-box">
                        </div>
                    </div>
                    <div
                        style="display:flex;justify-content:flex-end;gap:8px;padding-top:12px;border-top:1px solid #e5e7eb">
                        <button type="button" wire:click="$set('showQuickProductModal', false)" class="inv-btn-cancel"
                            style="padding:8px 20px;border-radius:8px;border:1px solid #d1d5db;background:white;font-size:13px;font-weight:600;cursor:pointer">Cancel</button>
                        <button type="submit"
                            style="padding:8px 20px;border-radius:8px;border:none;background:#4f46e5;color:white;font-size:13px;font-weight:700;cursor:pointer">
                            <span wire:loading.remove wire:target="submitQuickProduct">{!! $iPlus !!} Create</span>
                            <span wire:loading wire:target="submitQuickProduct">Creating...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ═══════════ ASSET DETAIL / TIMELINE MODAL ═══════════ --}}
    @if($showAssetDetailModal && $assetDetail)
        <div style="position:fixed;inset:0;z-index:10000;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.5)"
            wire:click.self="$set('showAssetDetailModal', false)">
            <div class="inv-modal"
                style="background:white;border-radius:16px;padding:28px;width:100%;max-width:700px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.3)">
                {{-- Header --}}
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px">
                    <div>
                        <h3 style="font-size:20px;font-weight:800;margin:0">{{ $assetDetail['_display_name'] }}</h3>
                        <div style="font-size:12px;color:#9ca3af;font-family:monospace;margin-top:2px">
                            {{ $assetDetail['asset_tag'] ?? '' }}
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px">
                        <img src="{{ $assetDetail['_qr_url'] }}" alt="QR"
                            style="width:60px;height:60px;border-radius:6px;border:1px solid #e5e7eb">
                        <button wire:click="$set('showAssetDetailModal', false)" type="button"
                            style="background:none;border:none;font-size:24px;cursor:pointer;color:#9ca3af">&times;</button>
                    </div>
                </div>

                {{-- Info Grid --}}
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:20px">
                    @php
                        $sc = ['available' => ['#dcfce7', '#16a34a'], 'assigned' => ['#dbeafe', '#2563eb'], 'maintenance' => ['#fef3c7', '#d97706'], 'retired' => ['#f3f4f6', '#6b7280'], 'lost' => ['#fee2e2', '#ef4444'], 'disposed' => ['#fecaca', '#dc2626']];
                        $s = $sc[$assetDetail['status'] ?? ''] ?? ['#f3f4f6', '#6b7280'];
                    @endphp
                    <div style="background:#f9fafb;border-radius:10px;padding:12px">
                        <div
                            style="font-size:10px;text-transform:uppercase;color:#9ca3af;letter-spacing:.5px;font-weight:700;margin-bottom:4px">
                            Status</div>
                        <span
                            style="padding:3px 10px;border-radius:6px;font-size:12px;font-weight:600;background:{{ $s[0] }};color:{{ $s[1] }}">{{ $assetDetail['_status_label'] }}</span>
                    </div>
                    <div style="background:#f9fafb;border-radius:10px;padding:12px">
                        <div
                            style="font-size:10px;text-transform:uppercase;color:#9ca3af;letter-spacing:.5px;font-weight:700;margin-bottom:4px">
                            Condition</div>
                        <div style="font-weight:600;font-size:14px">{{ $assetDetail['_condition_label'] }}</div>
                    </div>
                    <div style="background:#f9fafb;border-radius:10px;padding:12px">
                        <div
                            style="font-size:10px;text-transform:uppercase;color:#9ca3af;letter-spacing:.5px;font-weight:700;margin-bottom:4px">
                            Book Value</div>
                        <div style="font-weight:700;font-size:16px;font-family:monospace">
                            {{ \App\Support\CurrencyHelper::format($assetDetail['_book_value'], 0) }}
                        </div>
                    </div>
                    <div style="background:#f9fafb;border-radius:10px;padding:12px">
                        <div
                            style="font-size:10px;text-transform:uppercase;color:#9ca3af;letter-spacing:.5px;font-weight:700;margin-bottom:4px">
                            Holder</div>
                        <div style="font-weight:600;font-size:13px">{{ $assetDetail['_holder_name'] ?? '—' }}</div>
                    </div>
                    <div style="background:#f9fafb;border-radius:10px;padding:12px">
                        <div
                            style="font-size:10px;text-transform:uppercase;color:#9ca3af;letter-spacing:.5px;font-weight:700;margin-bottom:4px">
                            Location</div>
                        <div style="font-weight:600;font-size:13px">
                            {{ $assetDetail['current_location'] ?? $assetDetail['_warehouse_name'] ?? '—' }}
                        </div>
                    </div>
                    <div style="background:#f9fafb;border-radius:10px;padding:12px">
                        <div
                            style="font-size:10px;text-transform:uppercase;color:#9ca3af;letter-spacing:.5px;font-weight:700;margin-bottom:4px">
                            Product</div>
                        <div style="font-weight:600;font-size:13px">{{ $assetDetail['_product_name'] ?? '—' }}</div>
                    </div>
                </div>

                {{-- Purchase / Depreciation --}}
                <div
                    style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:8px;font-size:12px;margin-bottom:20px;padding:12px;background:#f9fafb;border-radius:10px">
                    <div><span style="color:#9ca3af">Purchase:</span>
                        <strong>{{ \App\Support\CurrencyHelper::format($assetDetail['purchase_cost'] ?? 0, 0) }}</strong>
                    </div>
                    <div><span style="color:#9ca3af">Date:</span>
                        <strong>{{ $assetDetail['purchase_date'] ? \Carbon\Carbon::parse($assetDetail['purchase_date'])->format('M d, Y') : '—' }}</strong>
                    </div>
                    <div><span style="color:#9ca3af">Useful Life:</span>
                        <strong>{{ $assetDetail['useful_life_years'] ?? '—' }} yrs</strong>
                    </div>
                    <div><span style="color:#9ca3af">Salvage:</span>
                        <strong>{{ \App\Support\CurrencyHelper::format($assetDetail['salvage_value'] ?? 0, 0) }}</strong>
                    </div>
                </div>

                {{-- Activity Timeline --}}
                <div style="margin-bottom:12px">
                    <h4 style="font-size:14px;font-weight:700;margin:0 0 12px 0;display:flex;align-items:center;gap:6px">
                        {!! $iClock !!}
                        Activity Timeline <span
                            style="font-size:11px;font-weight:500;color:#9ca3af">({{ count($assetTimeline) }} events)</span>
                    </h4>
                    <div style="max-height:300px;overflow-y:auto;border:1px solid #f3f4f6;border-radius:10px">
                        @forelse($assetTimeline as $event)
                            <div style="padding:10px 14px;border-bottom:1px solid #f9fafb;display:flex;gap:10px;align-items:flex-start;font-size:12px"
                                onmouseenter="this.style.background='#f9fafb'" onmouseleave="this.style.background=''">
                                @php
                                    $eventIcons = ['checkout' => $iCheckout, 'checkin' => $iCheckin, 'transfer' => $iTransfer, 'maintenance' => $iWrench, 'retire' => $iPause, 'lost' => $iWarn, 'dispose' => $iTrash];
                                @endphp
                                <span
                                    style="flex-shrink:0;color:#6b7280">{!! $eventIcons[$event['icon']] ?? $iClipboard !!}</span>
                                <div style="flex:1;min-width:0">
                                    <div style="font-weight:600;color:#1e293b">{{ $event['action'] }}
                                        @if($event['to']) <span style="color:#6b7280;font-weight:400">→
                                        {{ $event['to'] }}</span> @endif
                                    </div>
                                    @if($event['location'])
                                    <div style="color:#9ca3af">{!! $iMapPin !!} {{ $event['location'] }}</div> @endif
                                    @if($event['notes'])
                                    <div style="color:#6b7280;margin-top:2px">{{ Str::limit($event['notes'], 80) }}</div> @endif
                                    @if(isset($event['cost']) && $event['cost'] > 0)
                                        <span
                                            style="color:#d97706;font-weight:600">{{ \App\Support\CurrencyHelper::format($event['cost'], 0) }}</span>
                                    @endif
                                </div>
                                <div style="text-align:right;flex-shrink:0;white-space:nowrap">
                                    <div style="color:#6b7280;font-weight:600">{{ $event['date'] ?? '—' }}</div>
                                    <div style="color:#9ca3af;font-size:11px">{{ $event['by'] }}</div>
                                </div>
                            </div>
                        @empty
                            <div style="padding:24px;text-align:center;color:#9ca3af">No activity recorded yet</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ═══════════ TRANSFER ASSET MODAL ═══════════ --}}
    @if($showTransferAssetModal ?? false)
        <div style="position:fixed;inset:0;z-index:10000;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.5)"
            wire:click.self="$set('showTransferAssetModal', false)">
            <div class="inv-modal"
                style="background:white;border-radius:16px;padding:24px;width:100%;max-width:440px;box-shadow:0 20px 60px rgba(0,0,0,.3)">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
                    <h3 style="font-size:18px;font-weight:800;margin:0">{!! $iTransfer !!} Transfer Asset</h3>
                    <button wire:click="$set('showTransferAssetModal', false)" type="button"
                        style="background:none;border:none;font-size:22px;cursor:pointer;color:#9ca3af">&times;</button>
                </div>
                <form wire:submit="submitTransferAsset">
                    <div style="display:grid;gap:12px;margin-bottom:16px">
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Warehouse</label>
                            <x-searchable-select :wire-model="'transferAssetForm.warehouse_id'"
                                :options="$this->getWarehouseOptions()" placeholder="-- Select Warehouse --" />
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Location
                                / Area</label>
                            <input type="text" wire:model="transferAssetForm.location"
                                placeholder="e.g. Building A, Floor 3"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;box-sizing:border-box">
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Notes</label>
                            <textarea wire:model="transferAssetForm.notes" rows="2" placeholder="Reason for transfer..."
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;box-sizing:border-box;resize:vertical"></textarea>
                        </div>
                    </div>
                    <div
                        style="display:flex;justify-content:flex-end;gap:8px;padding-top:12px;border-top:1px solid #e5e7eb">
                        <button type="button" wire:click="$set('showTransferAssetModal', false)"
                            style="padding:8px 20px;border-radius:8px;border:1px solid #d1d5db;background:white;font-size:13px;font-weight:600;cursor:pointer">Cancel</button>
                        <button type="submit"
                            style="padding:8px 20px;border-radius:8px;border:none;background:#4f46e5;color:white;font-size:13px;font-weight:700;cursor:pointer">
                            <span wire:loading.remove wire:target="submitTransferAsset">{!! $iTransfer !!} Transfer</span>
                            <span wire:loading wire:target="submitTransferAsset">Transferring...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ═══════════ DISPOSE / RETIRE / LOST MODAL ═══════════ --}}
    @if($showDisposeAssetModal ?? false)
        <div style="position:fixed;inset:0;z-index:10000;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.5)"
            wire:click.self="$set('showDisposeAssetModal', false)">
            @php
                $dColors = ['retire' => ['#d97706', '#fef3c7', $ico('M14.25 9v6m-4.5 0V9M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 14), 'Retire Asset'], 'dispose' => ['#dc2626', '#fee2e2', $ico('M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0', 14), 'Dispose Asset'], 'lost' => ['#ef4444', '#fee2e2', $ico('M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z', 14), 'Report Lost']];
                $dc = $dColors[$disposeAssetForm['action'] ?? 'retire'] ?? $dColors['retire'];
            @endphp
            <div class="inv-modal"
                style="background:white;border-radius:16px;padding:24px;width:100%;max-width:440px;box-shadow:0 20px 60px rgba(0,0,0,.3)">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
                    <h3
                        style="font-size:18px;font-weight:800;margin:0;color:{{ $dc[0] }};display:flex;align-items:center;gap:6px">
                        {!! $dc[2] !!} {{ $dc[3] }}
                    </h3>
                    <button wire:click="$set('showDisposeAssetModal', false)" type="button"
                        style="background:none;border:none;font-size:22px;cursor:pointer;color:#9ca3af">&times;</button>
                </div>
                <div
                    style="background:{{ $dc[1] }};border-radius:8px;padding:10px 14px;font-size:12px;color:{{ $dc[0] }};margin-bottom:16px;font-weight:500">
                    {!! $iWarn !!} This action will change the asset status. This is recorded in the audit trail.
                </div>
                <form wire:submit="submitDisposeAsset">
                    <div style="display:grid;gap:12px;margin-bottom:16px">
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Reason
                                *</label>
                            <textarea wire:model="disposeAssetForm.reason" rows="2" required
                                placeholder="Explain why this asset is being {{ $disposeAssetForm['action'] ?? 'retired' }}..."
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;box-sizing:border-box;resize:vertical"></textarea>
                        </div>
                        @if(($disposeAssetForm['action'] ?? '') === 'dispose')
                            <div>
                                <label
                                    style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Disposal
                                    Method</label>
                                <select wire:model="disposeAssetForm.disposal_method"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;box-sizing:border-box">
                                    <option value="">-- Select --</option>
                                    @foreach(\App\Filament\App\Resources\CdeProjectResource\Pages\Modules\InventoryPage::$disposalMethods as $k => $v)
                                        <option value="{{ $k }}">{{ $v }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Additional
                                Notes</label>
                            <textarea wire:model="disposeAssetForm.notes" rows="2" placeholder="Any additional details..."
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;box-sizing:border-box;resize:vertical"></textarea>
                        </div>
                    </div>
                    <div
                        style="display:flex;justify-content:flex-end;gap:8px;padding-top:12px;border-top:1px solid #e5e7eb">
                        <button type="button" wire:click="$set('showDisposeAssetModal', false)"
                            style="padding:8px 20px;border-radius:8px;border:1px solid #d1d5db;background:white;font-size:13px;font-weight:600;cursor:pointer">Cancel</button>
                        <button type="submit"
                            style="padding:8px 20px;border-radius:8px;border:none;background:{{ $dc[0] }};color:white;font-size:13px;font-weight:700;cursor:pointer">
                            <span wire:loading.remove wire:target="submitDisposeAsset">{!! $dc[2] !!} Confirm</span>
                            <span wire:loading wire:target="submitDisposeAsset">Processing...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ═══════════ UPDATE CONDITION MODAL ═══════════ --}}
    @if($showConditionModal ?? false)
        <div style="position:fixed;inset:0;z-index:10000;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.5)"
            wire:click.self="$set('showConditionModal', false)">
            <div class="inv-modal"
                style="background:white;border-radius:16px;padding:24px;width:100%;max-width:400px;box-shadow:0 20px 60px rgba(0,0,0,.3)">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
                    <h3 style="font-size:18px;font-weight:800;margin:0">{!! $iClipboard !!} Update Condition</h3>
                    <button wire:click="$set('showConditionModal', false)" type="button"
                        style="background:none;border:none;font-size:22px;cursor:pointer;color:#9ca3af">&times;</button>
                </div>
                <form wire:submit="submitConditionUpdate">
                    <div style="display:grid;gap:12px;margin-bottom:16px">
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Condition
                                *</label>
                            <div style="display:flex;flex-wrap:wrap;gap:6px">
                                @foreach(\App\Models\Asset::$conditions as $k => $v)
                                    <label
                                        style="display:flex;align-items:center;gap:6px;padding:8px 14px;border:2px solid {{ ($conditionForm['condition'] ?? '') === $k ? '#4f46e5' : '#e5e7eb' }};border-radius:8px;cursor:pointer;font-size:13px;font-weight:{{ ($conditionForm['condition'] ?? '') === $k ? '700' : '500' }};background:{{ ($conditionForm['condition'] ?? '') === $k ? '#eef2ff' : 'white' }};transition:all .15s">
                                        <input type="radio" wire:model.live="conditionForm.condition" value="{{ $k }}"
                                            style="display:none">
                                        {{ $v }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Meter
                                / Hour Reading</label>
                            <input type="number" wire:model="conditionForm.meter_reading" step="0.1" min="0"
                                placeholder="Current reading"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;box-sizing:border-box">
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Notes</label>
                            <textarea wire:model="conditionForm.notes" rows="2" placeholder="Inspection notes..."
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;box-sizing:border-box;resize:vertical"></textarea>
                        </div>
                    </div>
                    <div
                        style="display:flex;justify-content:flex-end;gap:8px;padding-top:12px;border-top:1px solid #e5e7eb">
                        <button type="button" wire:click="$set('showConditionModal', false)"
                            style="padding:8px 20px;border-radius:8px;border:1px solid #d1d5db;background:white;font-size:13px;font-weight:600;cursor:pointer">Cancel</button>
                        <button type="submit"
                            style="padding:8px 20px;border-radius:8px;border:none;background:#4f46e5;color:white;font-size:13px;font-weight:700;cursor:pointer">
                            <span wire:loading.remove wire:target="submitConditionUpdate">Update</span>
                            <span wire:loading wire:target="submitConditionUpdate">Updating...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ═══════════ EDIT ASSET MODAL ═══════════ --}}
    @if($showEditAssetModal ?? false)
        <div style="position:fixed;inset:0;z-index:10000;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.5)"
            wire:click.self="$set('showEditAssetModal', false)">
            <div class="inv-modal"
                style="background:white;border-radius:16px;padding:24px;width:100%;max-width:520px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.3)">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
                    <h3 style="font-size:18px;font-weight:800;margin:0">{!! $iPencil !!} Edit Asset</h3>
                    <button wire:click="$set('showEditAssetModal', false)" type="button"
                        style="background:none;border:none;font-size:22px;cursor:pointer;color:#9ca3af">&times;</button>
                </div>
                <form wire:submit="submitEditAsset">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px">
                        <div style="grid-column:span 2">
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Asset
                                Name</label>
                            <input type="text" wire:model="editAssetForm.name" placeholder="e.g. CAT 320 Excavator"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;box-sizing:border-box">
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Serial
                                Number</label>
                            <input type="text" wire:model="editAssetForm.serial_number"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;box-sizing:border-box">
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Purchase
                                Cost ($)</label>
                            <input type="number" wire:model="editAssetForm.purchase_cost" step="0.01" min="0"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;box-sizing:border-box">
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Purchase
                                Date</label>
                            <input type="date" wire:model="editAssetForm.purchase_date"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;box-sizing:border-box">
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Warranty
                                Expiry</label>
                            <input type="date" wire:model="editAssetForm.warranty_expiry"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;box-sizing:border-box">
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Useful
                                Life (Years)</label>
                            <input type="number" wire:model="editAssetForm.useful_life_years" min="1"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;box-sizing:border-box">
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Salvage
                                Value ($)</label>
                            <input type="number" wire:model="editAssetForm.salvage_value" step="0.01" min="0"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;box-sizing:border-box">
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Meter
                                Reading</label>
                            <input type="number" wire:model="editAssetForm.meter_reading" step="0.1" min="0"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;box-sizing:border-box">
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Meter
                                Unit</label>
                            <select wire:model="editAssetForm.meter_unit"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;box-sizing:border-box">
                                <option value="">-- None --</option>
                                @foreach(\App\Models\Asset::$meterUnits as $k => $v) <option value="{{ $k }}">{{ $v }}
                                </option> @endforeach
                            </select>
                        </div>
                        <div style="grid-column:span 2">
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Notes</label>
                            <textarea wire:model="editAssetForm.notes" rows="2"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;box-sizing:border-box;resize:vertical"></textarea>
                        </div>
                    </div>
                    <div
                        style="display:flex;justify-content:flex-end;gap:8px;padding-top:12px;border-top:1px solid #e5e7eb">
                        <button type="button" wire:click="$set('showEditAssetModal', false)"
                            style="padding:8px 20px;border-radius:8px;border:1px solid #d1d5db;background:white;font-size:13px;font-weight:600;cursor:pointer">Cancel</button>
                        <button type="submit"
                            style="padding:8px 20px;border-radius:8px;border:none;background:#4f46e5;color:white;font-size:13px;font-weight:700;cursor:pointer">
                            <span wire:loading.remove wire:target="submitEditAsset">{!! $iSave !!} Save Changes</span>
                            <span wire:loading wire:target="submitEditAsset">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ═══════════ REPLACE ASSET MODAL ═══════════ --}}
    @if($showReplaceAssetModal ?? false)
        <div style="position:fixed;inset:0;z-index:10000;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.5)"
            wire:click.self="$set('showReplaceAssetModal', false)">
            <div class="inv-modal"
                style="background:white;border-radius:16px;padding:24px;width:100%;max-width:520px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.3)">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
                    <h3 style="font-size:18px;font-weight:800;margin:0;display:flex;align-items:center;gap:6px">
                        {!! $iTransfer !!} Replace Asset
                    </h3>
                    <button wire:click="$set('showReplaceAssetModal', false)" type="button"
                        style="background:none;border:none;font-size:22px;cursor:pointer;color:#9ca3af">&times;</button>
                </div>
                <div
                    style="background:#dbeafe;border-radius:8px;padding:10px 14px;font-size:12px;color:#1e40af;margin-bottom:16px;font-weight:500">
                    {!! $iEye !!} This will retire the current asset and create a new replacement asset linked to it.
                </div>
                <form wire:submit="submitReplaceAsset">
                    <div style="display:grid;gap:12px;margin-bottom:16px">
                        {{-- Reason --}}
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Reason
                                for Replacement *</label>
                            <textarea wire:model="replaceAssetForm.reason" rows="2" required
                                placeholder="Why is this asset being replaced?"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;box-sizing:border-box;resize:vertical"></textarea>
                        </div>
                        <div style="border-top:1px solid #e5e7eb;padding-top:12px">
                            <div style="font-size:12px;font-weight:700;color:#374151;margin-bottom:8px">New Replacement
                                Asset</div>
                        </div>
                        {{-- Name + Serial --}}
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                            <div>
                                <label
                                    style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Asset
                                    Name *</label>
                                <input type="text" wire:model="replaceAssetForm.new_asset_name" required
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;box-sizing:border-box">
                            </div>
                            <div>
                                <label
                                    style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Serial
                                    Number</label>
                                <input type="text" wire:model="replaceAssetForm.new_serial_number"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;box-sizing:border-box">
                            </div>
                        </div>
                        {{-- Cost + Date --}}
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                            <div>
                                <label
                                    style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Purchase
                                    Cost ($)</label>
                                <input type="number" wire:model="replaceAssetForm.new_purchase_cost" step="0.01" min="0"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;box-sizing:border-box">
                            </div>
                            <div>
                                <label
                                    style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Purchase
                                    Date</label>
                                <input type="date" wire:model="replaceAssetForm.new_purchase_date"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;box-sizing:border-box">
                            </div>
                        </div>
                        {{-- Warranty + Condition --}}
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                            <div>
                                <label
                                    style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Warranty
                                    Expiry</label>
                                <input type="date" wire:model="replaceAssetForm.new_warranty_expiry"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;box-sizing:border-box">
                            </div>
                            <div>
                                <label
                                    style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Condition</label>
                                <select wire:model="replaceAssetForm.new_condition"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;box-sizing:border-box">
                                    @foreach(\App\Models\Asset::$conditions as $k => $v) <option value="{{ $k }}">{{ $v }}
                                    </option> @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Additional
                                Notes</label>
                            <textarea wire:model="replaceAssetForm.notes" rows="2" placeholder="Any additional details..."
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;box-sizing:border-box;resize:vertical"></textarea>
                        </div>
                    </div>
                    <div
                        style="display:flex;justify-content:flex-end;gap:8px;padding-top:12px;border-top:1px solid #e5e7eb">
                        <button type="button" wire:click="$set('showReplaceAssetModal', false)"
                            style="padding:8px 20px;border-radius:8px;border:1px solid #d1d5db;background:white;font-size:13px;font-weight:600;cursor:pointer">Cancel</button>
                        <button type="submit"
                            style="padding:8px 20px;border-radius:8px;border:none;background:#4f46e5;color:white;font-size:13px;font-weight:700;cursor:pointer">
                            <span wire:loading.remove wire:target="submitReplaceAsset">{!! $iTransfer !!} Replace & Create
                                New</span>
                            <span wire:loading wire:target="submitReplaceAsset">Processing...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ═══════════════ DELIVERY NOTES TAB ═══════════════ --}}
    @if($activeInventoryTab === 'delivery_notes')
        <div style="display:flex; justify-content:flex-end; margin-bottom:12px;">
            <button wire:click="initNewDN"
                style="display:inline-flex; align-items:center; gap:6px; padding:8px 16px; font-size:13px; font-weight:600; border-radius:8px; border:none; background:#4f46e5; color:white; cursor:pointer;">
                {!! $iPlus !!} New Delivery Note
            </button>
        </div>

        <table class="inv-table" style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="background:#f8fafc;border-bottom:2px solid #e5e7eb;">
                    <th style="text-align:left;padding:10px 12px;font-size:11px;font-weight:700;text-transform:uppercase;color:#6b7280;">DN #</th>
                    <th style="text-align:left;padding:10px 12px;font-size:11px;font-weight:700;text-transform:uppercase;color:#6b7280;">Destination</th>
                    <th style="text-align:left;padding:10px 12px;font-size:11px;font-weight:700;text-transform:uppercase;color:#6b7280;">Vehicle</th>
                    <th style="text-align:left;padding:10px 12px;font-size:11px;font-weight:700;text-transform:uppercase;color:#6b7280;">Driver</th>
                    <th style="text-align:center;padding:10px 12px;font-size:11px;font-weight:700;text-transform:uppercase;color:#6b7280;">Items</th>
                    <th style="text-align:left;padding:10px 12px;font-size:11px;font-weight:700;text-transform:uppercase;color:#6b7280;">Status</th>
                    <th style="text-align:left;padding:10px 12px;font-size:11px;font-weight:700;text-transform:uppercase;color:#6b7280;">Dispatch</th>
                    <th style="text-align:left;padding:10px 12px;font-size:11px;font-weight:700;text-transform:uppercase;color:#6b7280;">Milestone</th>
                    <th style="text-align:center;padding:10px 12px;font-size:11px;font-weight:700;text-transform:uppercase;color:#6b7280;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->getDeliveryNotes() as $dn)
                    @php
                        $statusColors = ['draft' => '#6b7280', 'dispatched' => '#f59e0b', 'in_transit' => '#3b82f6', 'delivered' => '#10b981', 'partial' => '#d97706'];
                    @endphp
                    <tr style="border-bottom:1px solid #f3f4f6;">
                        <td style="padding:10px 12px;font-weight:700;color:#4f46e5">{{ $dn->dn_number }}</td>
                        <td style="padding:10px 12px;">
                            <div style="font-weight:600">{{ $dn->destination ?? '—' }}</div>
                            @if($dn->destination_contact)<div style="font-size:11px;color:#9ca3af">{{ $dn->destination_contact }}</div>@endif
                        </td>
                        <td style="padding:10px 12px;">{{ $dn->vehicle_number ?? '—' }}</td>
                        <td style="padding:10px 12px;">{{ $dn->driver_name ?? '—' }}</td>
                        <td style="padding:10px 12px;text-align:center;">
                            <span style="background:#eef2ff;color:#4f46e5;padding:2px 8px;border-radius:99px;font-size:11px;font-weight:700;">{{ $dn->items->count() }}</span>
                        </td>
                        <td style="padding:10px 12px;">
                            <span style="padding:3px 10px;border-radius:99px;font-size:10px;font-weight:700;color:white;background:{{ $statusColors[$dn->status] ?? '#6b7280' }}">
                                {{ \App\Models\DeliveryNote::$statuses[$dn->status] ?? $dn->status }}
                            </span>
                        </td>
                        <td style="padding:10px 12px;font-size:12px;">{{ $dn->dispatch_date?->format('M d, Y') ?? '—' }}</td>
                        <td style="padding:10px 12px;font-size:12px;">{{ $dn->milestone?->name ?? '—' }}</td>
                        <td style="padding:10px 12px;text-align:center;">
                            @if($dn->status !== 'delivered')
                                <button wire:click="markDNDelivered({{ $dn->id }})" wire:confirm="Mark this delivery note as delivered?"
                                    style="padding:4px 10px;font-size:11px;font-weight:600;border-radius:6px;border:none;background:#10b981;color:white;cursor:pointer;">
                                    ✓ Delivered
                                </button>
                            @else
                                <span style="font-size:11px;color:#10b981;font-weight:600">✓ Completed</span>
                            @endif
                        </td>
                    </tr>
                    @if($dn->items->count())
                        <tr style="border-bottom:1px solid #e5e7eb;">
                            <td colspan="9" style="padding:0 12px 10px 40px;">
                                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                                    @foreach($dn->items as $di)
                                        <span style="background:#f3f4f6;padding:3px 10px;border-radius:6px;font-size:11px;">
                                            {{ $di->product?->name ?? $di->description }} × {{ number_format($di->quantity_dispatched) }} {{ $di->unit }}
                                            @if($dn->status === 'delivered' && $di->condition !== 'good')
                                                <span style="color:#ef4444;font-weight:600;">⚠ {{ $di->condition }}</span>
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="9" style="text-align:center;padding:40px;color:#9ca3af;">
                            <div style="margin-bottom:8px">{!! $iTruck !!}</div>
                            <div style="font-weight:600;">No Delivery Notes</div>
                            <div style="font-size:12px;">Create delivery notes to track dispatches to site.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    {{-- ═══════════════ STOCK MONITOR TAB ═══════════════ --}}
    @if($activeInventoryTab === 'stock_monitor')
        @php $storesSummary = $this->getAllStoresStockSummary(); @endphp

        {{-- Store-level summary cards --}}
        <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:16px; margin-bottom:24px;">
            @forelse($storesSummary as $ws)
                <div style="background:white;border:1px solid #e5e7eb;border-radius:12px;padding:16px;cursor:pointer;transition:all .2s;"
                     wire:click="openStockMonitor({{ $ws['id'] }})"
                     onmouseover="this.style.borderColor='#4f46e5';this.style.boxShadow='0 4px 12px rgba(79,70,229,0.1)'"
                     onmouseout="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                        <div style="width:42px;height:42px;border-radius:10px;background:linear-gradient(135deg,#dbeafe,#e0e7ff);display:flex;align-items:center;justify-content:center;">
                            {!! $ico('M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35', 24) !!}
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:15px">{{ $ws['name'] }}</div>
                            <div style="font-size:11px;color:#9ca3af">{{ $ws['code'] ?? '—' }}</div>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:12px;">
                        <div><span style="color:#9ca3af">Products:</span> <strong>{{ number_format($ws['total_products']) }}</strong></div>
                        <div><span style="color:#9ca3af">Total Qty:</span> <strong>{{ number_format($ws['total_quantity']) }}</strong></div>
                        <div><span style="color:#9ca3af">Value:</span> <strong>{{ \App\Support\CurrencyHelper::formatCompact($ws['total_value']) }}</strong></div>
                        <div>
                            @if($ws['low_stock'] > 0)
                                <span style="color:#f59e0b;font-weight:700">⚠ {{ $ws['low_stock'] }} low</span>
                            @elseif($ws['out_of_stock'] > 0)
                                <span style="color:#ef4444;font-weight:700">✕ {{ $ws['out_of_stock'] }} out</span>
                            @else
                                <span style="color:#10b981;font-weight:600">✓ Healthy</span>
                            @endif
                        </div>
                    </div>

                    <div style="text-align:center;margin-top:12px;padding-top:10px;border-top:1px solid #f3f4f6;">
                        <span style="font-size:11px;color:#4f46e5;font-weight:600;">Click to view full stock details →</span>
                    </div>
                </div>
            @empty
                <div style="grid-column:1/-1;text-align:center;padding:40px;color:#9ca3af;">
                    <div style="margin-bottom:8px">{!! $iSignal !!}</div>
                    <div style="font-weight:600;">No Stores to Monitor</div>
                    <div style="font-size:12px;">Add stores in the Stores tab first.</div>
                </div>
            @endforelse
        </div>

        {{-- Stock Monitor Modal (full store detail) --}}
        @if($showStockMonitorModal)
            @php $smd = $this->getStockMonitorData(); @endphp
            <div style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;display:flex;align-items:center;justify-content:center;padding:20px;"
                 wire:click.self="$set('showStockMonitorModal', false)">
                <div style="background:white;border-radius:16px;width:100%;max-width:1000px;max-height:90vh;overflow-y:auto;padding:24px;" wire:click.stop>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                        <h3 style="font-size:18px;font-weight:800;margin:0;">📊 Stock Monitor — {{ $smd['warehouse']->name ?? '' }}</h3>
                        <button wire:click="$set('showStockMonitorModal', false)" style="background:none;border:none;cursor:pointer;font-size:20px;">✕</button>
                    </div>

                    @if(!empty($smd['summary']))
                        {{-- Summary cards --}}
                        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:12px;margin-bottom:20px;">
                            @foreach([
                                    ['label' => 'Products', 'value' => $smd['summary']['total_items'], 'color' => '#4f46e5'],
                                    ['label' => 'On Hand', 'value' => number_format($smd['summary']['total_on_hand']), 'color' => '#3b82f6'],
                                    ['label' => 'Reserved', 'value' => number_format($smd['summary']['total_reserved']), 'color' => '#f59e0b'],
                                    ['label' => 'Available', 'value' => number_format($smd['summary']['total_available']), 'color' => '#10b981'],
                                    ['label' => 'Total Value', 'value' => \App\Support\CurrencyHelper::formatCompact($smd['summary']['total_value']), 'color' => '#7c3aed'],
                                    ['label' => 'Low Stock', 'value' => $smd['summary']['low_stock_count'], 'color' => $smd['summary']['low_stock_count'] > 0 ? '#f59e0b' : '#10b981'],
                                    ['label' => 'Out of Stock', 'value' => $smd['summary']['out_of_stock_count'], 'color' => $smd['summary']['out_of_stock_count'] > 0 ? '#ef4444' : '#10b981'],
                                ] as $card)
                                            <div style="background:#f9fafb;border-radius:10px;padding:12px;text-align:center;border:1px solid #e5e7eb;">
                                                <div style="font-size:10px;text-transform:uppercase;font-weight:700;color:#9ca3af;letter-spacing:.05em">{{ $card['label'] }}</div>
                                                <div style="font-size:20px;font-weight:800;color:{{ $card['color'] }};margin-top:4px;">{{ $card['value'] }}</div>
                                            </div>
                            @endforeach
                        </div>

                        {{-- Item-by-item table --}}
                        <table style="width:100%;border-collapse:collapse;font-size:12px;">
                            <thead>
                                <tr style="background:#f8fafc;border-bottom:2px solid #e5e7eb;">
                                    <th style="text-align:left;padding:8px;font-size:10px;font-weight:700;text-transform:uppercase;color:#6b7280;">Product</th>
                                    <th style="text-align:left;padding:8px;font-size:10px;font-weight:700;text-transform:uppercase;color:#6b7280;">SKU</th>
                                    <th style="text-align:center;padding:8px;font-size:10px;font-weight:700;text-transform:uppercase;color:#6b7280;">On Hand</th>
                                    <th style="text-align:center;padding:8px;font-size:10px;font-weight:700;text-transform:uppercase;color:#6b7280;">Reserved</th>
                                    <th style="text-align:center;padding:8px;font-size:10px;font-weight:700;text-transform:uppercase;color:#6b7280;">Available</th>
                                    <th style="text-align:center;padding:8px;font-size:10px;font-weight:700;text-transform:uppercase;color:#6b7280;">Reorder</th>
                                    <th style="text-align:right;padding:8px;font-size:10px;font-weight:700;text-transform:uppercase;color:#6b7280;">Value</th>
                                    <th style="text-align:left;padding:8px;font-size:10px;font-weight:700;text-transform:uppercase;color:#6b7280;">Bin</th>
                                    <th style="text-align:center;padding:8px;font-size:10px;font-weight:700;text-transform:uppercase;color:#6b7280;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($smd['items'] as $si)
                                    <tr style="border-bottom:1px solid #f3f4f6; {{ $si['is_out'] ? 'background:#fef2f2;' : ($si['is_low'] ? 'background:#fffbeb;' : '') }}">
                                        <td style="padding:8px;font-weight:600;">{{ $si['product_name'] }}</td>
                                        <td style="padding:8px;color:#6b7280;">{{ $si['sku'] }}</td>
                                        <td style="padding:8px;text-align:center;font-weight:700;">{{ number_format($si['on_hand']) }}</td>
                                        <td style="padding:8px;text-align:center;">{{ number_format($si['reserved']) }}</td>
                                        <td style="padding:8px;text-align:center;font-weight:600;color:#10b981;">{{ number_format($si['available']) }}</td>
                                        <td style="padding:8px;text-align:center;color:#9ca3af;">{{ number_format($si['reorder_level']) }}</td>
                                        <td style="padding:8px;text-align:right;font-weight:600;">{{ \App\Support\CurrencyHelper::format($si['stock_value'], 0) }}</td>
                                        <td style="padding:8px;color:#6b7280;">{{ $si['bin_location'] }}</td>
                                        <td style="padding:8px;text-align:center;">
                                            @if($si['is_out'])
                                                <span style="padding:2px 8px;border-radius:99px;font-size:10px;font-weight:700;background:#fee2e2;color:#ef4444;">Out</span>
                                            @elseif($si['is_low'])
                                                <span style="padding:2px 8px;border-radius:99px;font-size:10px;font-weight:700;background:#fef3c7;color:#d97706;">Low</span>
                                            @else
                                                <span style="padding:2px 8px;border-radius:99px;font-size:10px;font-weight:700;background:#dcfce7;color:#16a34a;">OK</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="9" style="text-align:center;padding:20px;color:#9ca3af;">No stock items in this store.</td></tr>
                                @endforelse
                            </tbody>
                        </table>

                        @if(count($smd['low_stock']) > 0)
                            <div style="margin-top:16px;padding:12px;background:#fffbeb;border:1px solid #fef3c7;border-radius:10px;">
                                <div style="font-weight:700;font-size:13px;color:#d97706;margin-bottom:8px;">⚠ Low Stock Alerts</div>
                                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                                    @foreach($smd['low_stock'] as $ls)
                                        <span style="background:white;border:1px solid #fcd34d;padding:4px 12px;border-radius:8px;font-size:12px;">
                                            <strong>{{ $ls['product_name'] }}</strong>: {{ $ls['on_hand'] }}/{{ $ls['reorder_level'] }} (need {{ $ls['deficit'] }} more)
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        @endif
    @endif

    {{-- ═══════════════ PRODUCT TRACKING TAB ═══════════════ --}}
    @if($activeInventoryTab === 'tracking')
        @php $trackingSummary = $this->getProductTrackingSummary(); @endphp

        <div style="margin-bottom:16px;">
            <div style="font-size:13px;color:#6b7280;margin-bottom:12px;">Track products through their lifecycle: Ordered → Received → Stored → Issued → In Transit → Delivered → Installed</div>

            {{-- Stage legend --}}
            <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:20px;">
                @foreach(\App\Models\ProductTracking::$stages as $sk => $sl)
                    <span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:99px;font-size:11px;font-weight:600;background:{{ \App\Models\ProductTracking::$stageColors[$sk] }}20;color:{{ \App\Models\ProductTracking::$stageColors[$sk] }};">
                        <span style="width:8px;height:8px;border-radius:50%;background:{{ \App\Models\ProductTracking::$stageColors[$sk] }}"></span>
                        {{ $sl }}
                    </span>
                @endforeach
            </div>
        </div>

        <table class="inv-table" style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="background:#f8fafc;border-bottom:2px solid #e5e7eb;">
                    <th style="text-align:left;padding:10px 12px;font-size:11px;font-weight:700;text-transform:uppercase;color:#6b7280;">Product</th>
                    <th style="text-align:left;padding:10px 12px;font-size:11px;font-weight:700;text-transform:uppercase;color:#6b7280;">SKU</th>
                    <th style="text-align:center;padding:10px 12px;font-size:11px;font-weight:700;text-transform:uppercase;color:#6b7280;">Current Stage</th>
                    <th style="text-align:left;padding:10px 12px;font-size:11px;font-weight:700;text-transform:uppercase;color:#6b7280;">Location</th>
                    <th style="text-align:center;padding:10px 12px;font-size:11px;font-weight:700;text-transform:uppercase;color:#6b7280;">Events</th>
                    <th style="text-align:left;padding:10px 12px;font-size:11px;font-weight:700;text-transform:uppercase;color:#6b7280;">Last Updated</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trackingSummary as $tp)
                    <tr style="border-bottom:1px solid #f3f4f6;">
                        <td style="padding:10px 12px;font-weight:600;">{{ $tp['name'] }}</td>
                        <td style="padding:10px 12px;color:#6b7280;">{{ $tp['sku'] ?? '—' }}</td>
                        <td style="padding:10px 12px;text-align:center;">
                            <span style="padding:3px 12px;border-radius:99px;font-size:11px;font-weight:700;color:white;background:{{ $tp['latest_stage_color'] }}">
                                {{ $tp['latest_stage_label'] }}
                            </span>
                        </td>
                        <td style="padding:10px 12px;font-size:12px;">{{ $tp['latest_location'] }}</td>
                        <td style="padding:10px 12px;text-align:center;">
                            <span style="background:#f3f4f6;padding:2px 8px;border-radius:99px;font-size:11px;font-weight:600;">{{ $tp['total_events'] }}</span>
                        </td>
                        <td style="padding:10px 12px;font-size:12px;color:#6b7280;">{{ $tp['latest_date'] ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:40px;color:#9ca3af;">
                            <div style="margin-bottom:8px">{!! $iRoute !!}</div>
                            <div style="font-weight:600;">No Product Tracking Data</div>
                            <div style="font-size:12px;">Product movements will appear here as POs are received, goods are issued, and delivery notes dispatched.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    {{-- ═══════════════ DELIVERY NOTE MODAL ═══════════════ --}}
    @if($showDNModal)
        <div style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;display:flex;align-items:center;justify-content:center;padding:20px;"
             wire:click.self="$set('showDNModal', false)">
            <div style="background:white;border-radius:16px;width:100%;max-width:900px;max-height:90vh;overflow-y:auto;padding:24px;" wire:click.stop>
                <h3 style="font-size:18px;font-weight:800;margin:0 0 20px;">🚚 New Delivery Note</h3>

                <form wire:submit.prevent="submitDN">
                    {{-- Header fields --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:16px;">
                        <div>
                            <label style="font-size:11px;font-weight:700;color:#6b7280;display:block;margin-bottom:4px;">Destination / Site</label>
                            <input type="text" wire:model="dnHeader.destination" placeholder="e.g. Karuma Dam Site A"
                                style="width:100%;padding:8px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;">
                        </div>
                        <div>
                            <label style="font-size:11px;font-weight:700;color:#6b7280;display:block;margin-bottom:4px;">Contact Person</label>
                            <input type="text" wire:model="dnHeader.destination_contact" placeholder="Receiver name"
                                style="width:100%;padding:8px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;">
                        </div>
                        <div>
                            <label style="font-size:11px;font-weight:700;color:#6b7280;display:block;margin-bottom:4px;">Contact Phone</label>
                            <input type="text" wire:model="dnHeader.destination_phone"
                                style="width:100%;padding:8px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;">
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:16px;">
                        <div>
                            <label style="font-size:11px;font-weight:700;color:#6b7280;display:block;margin-bottom:4px;">Vehicle Number</label>
                            <input type="text" wire:model="dnHeader.vehicle_number" placeholder="e.g. UAA 123B"
                                style="width:100%;padding:8px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;">
                        </div>
                        <div>
                            <label style="font-size:11px;font-weight:700;color:#6b7280;display:block;margin-bottom:4px;">Driver Name</label>
                            <input type="text" wire:model="dnHeader.driver_name"
                                style="width:100%;padding:8px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;">
                        </div>
                        <div>
                            <label style="font-size:11px;font-weight:700;color:#6b7280;display:block;margin-bottom:4px;">Driver Phone</label>
                            <input type="text" wire:model="dnHeader.driver_phone"
                                style="width:100%;padding:8px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;">
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:16px;">
                        <div>
                            <label style="font-size:11px;font-weight:700;color:#6b7280;display:block;margin-bottom:4px;">From Warehouse</label>
                            <select wire:model="dnHeader.warehouse_id" style="width:100%;padding:8px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;">
                                <option value="">— Select —</option>
                                @foreach($this->getWarehouseOptions() as $wid => $wname)
                                    <option value="{{ $wid }}">{{ $wname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="font-size:11px;font-weight:700;color:#6b7280;display:block;margin-bottom:4px;">Linked Milestone</label>
                            <select wire:model="dnHeader.milestone_id" style="width:100%;padding:8px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;">
                                <option value="">— None —</option>
                                @foreach($this->getMilestoneOptions() as $mid => $mname)
                                    <option value="{{ $mid }}">{{ $mname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="font-size:11px;font-weight:700;color:#6b7280;display:block;margin-bottom:4px;">Dispatch Date</label>
                            <input type="date" wire:model="dnHeader.dispatch_date"
                                style="width:100%;padding:8px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;">
                        </div>
                    </div>

                    {{-- Line items --}}
                    <div style="margin-bottom:16px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                            <label style="font-size:13px;font-weight:700;">Items to Deliver</label>
                            <button type="button" wire:click="addDNItem"
                                style="padding:4px 12px;font-size:12px;font-weight:600;border-radius:6px;border:1px solid #4f46e5;background:white;color:#4f46e5;cursor:pointer;">
                                {!! $iPlus !!} Add Item
                            </button>
                        </div>
                        <table style="width:100%;border-collapse:collapse;font-size:12px;">
                            <thead>
                                <tr style="background:#f8fafc;border-bottom:1px solid #e5e7eb;">
                                    <th style="text-align:left;padding:6px 8px;font-weight:600;">Product</th>
                                    <th style="text-align:left;padding:6px 8px;font-weight:600;">Description</th>
                                    <th style="text-align:center;padding:6px 8px;font-weight:600;width:80px;">Qty</th>
                                    <th style="text-align:center;padding:6px 8px;font-weight:600;width:90px;">Unit</th>
                                    <th style="width:40px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dnItems as $idx => $di)
                                    <tr style="border-bottom:1px solid #f3f4f6;">
                                        <td style="padding:4px 8px;">
                                            <select wire:model="dnItems.{{ $idx }}.product_id"
                                                style="width:100%;padding:6px 8px;border:1px solid #e5e7eb;border-radius:6px;font-size:12px;">
                                                <option value="">— Select —</option>
                                                @foreach($this->getProductOptions() as $pid => $pname)
                                                    <option value="{{ $pid }}">{{ $pname }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td style="padding:4px 8px;">
                                            <input type="text" wire:model="dnItems.{{ $idx }}.description" placeholder="Description"
                                                style="width:100%;padding:6px 8px;border:1px solid #e5e7eb;border-radius:6px;font-size:12px;">
                                        </td>
                                        <td style="padding:4px 8px;">
                                            <input type="number" wire:model="dnItems.{{ $idx }}.quantity_dispatched" min="1"
                                                style="width:100%;padding:6px 8px;border:1px solid #e5e7eb;border-radius:6px;font-size:12px;text-align:center;">
                                        </td>
                                        <td style="padding:4px 8px;">
                                            <select wire:model="dnItems.{{ $idx }}.unit"
                                                style="width:100%;padding:6px 8px;border:1px solid #e5e7eb;border-radius:6px;font-size:12px;">
                                                @foreach(\App\Models\Product::$units as $uk => $ul)
                                                    <option value="{{ $uk }}">{{ $ul }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td style="padding:4px 8px;">
                                            @if(count($dnItems) > 1)
                                                <button type="button" wire:click="removeDNItem({{ $idx }})"
                                                    style="background:none;border:none;cursor:pointer;color:#ef4444;font-size:16px">✕</button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Notes --}}
                    <div style="margin-bottom:16px;">
                        <label style="font-size:11px;font-weight:700;color:#6b7280;display:block;margin-bottom:4px;">Notes</label>
                        <textarea wire:model="dnHeader.notes" rows="2"
                            style="width:100%;padding:8px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;"></textarea>
                    </div>

                    <div style="display:flex;justify-content:flex-end;gap:8px;">
                        <button type="button" wire:click="$set('showDNModal', false)"
                            style="padding:8px 20px;font-size:13px;font-weight:600;border-radius:8px;border:1px solid #e5e7eb;background:white;cursor:pointer;">
                            Cancel
                        </button>
                        <button type="submit"
                            style="padding:8px 20px;font-size:13px;font-weight:600;border-radius:8px;border:none;background:#4f46e5;color:white;cursor:pointer;">
                            <span wire:loading.remove wire:target="submitDN">Create & Dispatch</span>
                            <span wire:loading wire:target="submitDN">Processing...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</x-filament-panels::page>