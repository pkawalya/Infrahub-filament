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
        @foreach(['products' => $iCube . ' Products', 'assets' => $iTag . ' Assets', 'stores' => $iStore . ' Stores', 'purchase_orders' => $iCart . ' Purchase Orders', 'grn' => $iInbox . ' GRN', 'issuances' => $iClipboard . ' Issuances', 'transfers' => $iArrows . ' Transfers', 'adjustments' => $iScale . ' Adjustments'] as $tab => $label)
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
                        <button wire:click="viewStoreStock({{ $store->id }})"
                            style="padding:4px 10px; font-size:11px; font-weight:600; border-radius:6px; border:none; background:#4f46e5; color:white; cursor:pointer;">{!! $iChart !!}
                            Stock</button>
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
        <div style="display:flex; justify-content:flex-end; gap:8px; margin-bottom:12px;">
            <button wire:click="$set('showAssetModal', true)"
                style="display:inline-flex; align-items:center; gap:6px; padding:8px 16px; font-size:13px; font-weight:600; border-radius:8px; border:none; background:#7c3aed; color:white; cursor:pointer;">
                {!! $iPlus !!} Register Asset
            </button>
        </div>

        <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap:16px;">
            @forelse($this->getAssets() as $asset)
                @php
                    $statusColors = ['available' => 'background:#dcfce7; color:#16a34a;', 'assigned' => 'background:#dbeafe; color:#2563eb;', 'maintenance' => 'background:#fef3c7; color:#d97706;', 'retired' => 'background:#f3f4f6; color:#6b7280;', 'lost' => 'background:#fee2e2; color:#ef4444;', 'disposed' => 'background:#fecaca; color:#dc2626;'];
                @endphp
                <div class="inv-card"
                    style="background:white; border:1px solid #e5e7eb; border-radius:12px; padding:16px; transition:all .2s;"
                    onmouseenter="this.style.boxShadow='0 4px 16px rgba(0,0,0,.08)'; this.style.transform='translateY(-2px)';"
                    onmouseleave="this.style.boxShadow='none'; this.style.transform='none';">

                    {{-- Header --}}
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
                        <div>
                            <div style="font-weight:700; font-size:15px;">{{ $asset->display_name }}</div>
                            <div style="font-size:11px; color:#9ca3af; margin-top:2px; font-family:monospace;">
                                {{ $asset->asset_tag }}
                            </div>
                            @if($asset->serial_number)
                                <div style="font-size:10px; color:#9ca3af;">S/N: {{ $asset->serial_number }}</div>
                            @endif
                        </div>
                        <span
                            style="padding:3px 10px; border-radius:6px; font-size:11px; font-weight:600; {{ $statusColors[$asset->status] ?? '' }}">
                            {{ \App\Models\Asset::$statuses[$asset->status] ?? $asset->status }}
                        </span>
                    </div>

                    {{-- Details Grid --}}
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:6px; font-size:12px; margin-bottom:10px;">
                        <div><span class="inv-label" style="color:#9ca3af;">Holder:</span>
                            <strong>{{ $asset->currentHolder?->name ?? '—' }}</strong>
                        </div>
                        <div><span class="inv-label" style="color:#9ca3af;">Location:</span>
                            <strong>{{ $asset->current_location ?? $asset->warehouse?->name ?? '—' }}</strong>
                        </div>
                        <div><span class="inv-label" style="color:#9ca3af;">Condition:</span>
                            <strong
                                style="{{ $asset->condition === 'damaged' ? 'color:#ef4444;' : '' }}">{{ \App\Models\Asset::$conditions[$asset->condition] ?? $asset->condition }}</strong>
                        </div>
                        <div><span class="inv-label" style="color:#9ca3af;">Book Value:</span>
                            <strong>${{ number_format($asset->current_book_value, 0) }}</strong>
                        </div>
                        @if($asset->warranty_expiry)
                            <div style="grid-column:1/-1;">
                                <span style="color:#9ca3af;">Warranty:</span>
                                <strong style="{{ $asset->isWarrantyActive() ? 'color:#16a34a;' : 'color:#ef4444;' }}">
                                    {{ $asset->warranty_expiry->format('M d, Y') }}
                                    {{ $asset->isWarrantyActive() ? '✓' : '✗ Expired' }}
                                </strong>
                            </div>
                        @endif
                    </div>

                    {{-- Actions + QR --}}
                    <div class="inv-divider"
                        style="display:flex; align-items:center; justify-content:space-between; padding-top:10px; border-top:1px solid #f3f4f6;">
                        <div style="display:flex; gap:4px; flex-wrap:wrap;">
                            @if($asset->status === 'available')
                                <button wire:click="openCheckoutModal({{ $asset->id }})"
                                    style="padding:4px 10px; font-size:11px; font-weight:600; border-radius:6px; border:none; background:#2563eb; color:white; cursor:pointer;">📤
                                    Checkout</button>
                            @elseif($asset->status === 'assigned')
                                <button wire:click="checkinAsset({{ $asset->id }})"
                                    style="padding:4px 10px; font-size:11px; font-weight:600; border-radius:6px; border:none; background:#16a34a; color:white; cursor:pointer;">{!! $iInbox !!}
                                    Checkin</button>
                            @endif
                            <button wire:click="openMaintenanceModal({{ $asset->id }})" class="inv-btn-outline"
                                style="padding:4px 10px; font-size:11px; font-weight:600; border-radius:6px; border:1px solid #e5e7eb; background:white; cursor:pointer;">{!! $iWrench !!}</button>
                            <button onclick="window.open('{{ $asset->qr_code_url }}', '_blank')" class="inv-btn-outline"
                                style="padding:4px 10px; font-size:11px; font-weight:600; border-radius:6px; border:1px solid #e5e7eb; background:white; cursor:pointer;">{!! $ico('M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z') !!}
                                QR</button>
                        </div>
                        <img src="{{ $asset->qr_code_url }}" alt="QR"
                            style="width:44px; height:44px; border-radius:4px; border:1px solid #e5e7eb;" loading="lazy">
                    </div>

                    {{-- Assignment History (collapsed) --}}
                    @if($asset->assignments->count() > 0)
                        <details style="margin-top:10px; font-size:11px;">
                            <summary style="cursor:pointer; color:#6366f1; font-weight:600;">📜 History
                                ({{ $asset->assignments->count() }})</summary>
                            <div style="margin-top:6px; max-height:120px; overflow-y:auto;">
                                @foreach($asset->assignments->take(5) as $log)
                                    <div
                                        style="padding:4px 0; border-bottom:1px solid #f9fafb; display:flex; justify-content:space-between;">
                                        <span>{{ \App\Models\AssetAssignment::$actions[$log->action] ?? $log->action }}
                                            → {{ $log->assignedTo?->name ?? $log->assigned_to_name ?? '—' }}</span>
                                        <span style="color:#9ca3af;">{{ $log->checkout_date?->format('M d') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </details>
                    @endif
                </div>
            @empty
                <div class="inv-empty" style="grid-column:1/-1; text-align:center; padding:40px; color:#9ca3af;">
                    <div style="margin-bottom:8px;color:#9ca3af">
                        {!! $ico('M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z M6 6h.008v.008H6V6z', 32) !!}
                    </div>
                    <div style="font-weight:600;">No Assets Registered</div>
                    <div style="font-size:13px;">Register assets to track equipment, tools, and machinery.</div>
                </div>
            @endforelse
        </div>
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
                                <select wire:model="issuanceForm.issued_to"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                                    <option value="">-- Select --</option>
                                    @foreach($this->getTeamOptions() as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
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
                                <select wire:model="issuanceForm.warehouse_id" required
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                                    <option value="">-- Select --</option>
                                    @foreach($this->getStoreOptions() as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
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
                            <select wire:model="issuanceForm.product_id" required
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                                <option value="">-- Select Product --</option>
                                @foreach($this->getProductOptions() as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
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
                                <select wire:model="assetForm.product_id"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                                    <option value="">-- None --</option>
                                    @foreach($this->getProductOptions() as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
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
                                <select wire:model="assetForm.warehouse_id"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                                    <option value="">-- None --</option>
                                    @foreach($this->getStoreOptions() as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
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
                    <h3 style="font-size:18px;font-weight:700">📤 Checkout Asset</h3>
                    <button wire:click="$set('showCheckoutModal', false)" type="button"
                        style="background:none;border:none;font-size:20px;cursor:pointer">&times;</button>
                </div>
                <form wire:submit="submitCheckout">
                    <div style="display:grid;gap:12px">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Assign To
                                (User)</label>
                            <select wire:model="checkoutForm.assigned_to"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                                <option value="">-- Select --</option>
                                @foreach($this->getTeamOptions() as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
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

    {{-- ═══════════════ MAINTENANCE LOG MODAL ═══════════════ --}}
    @if($showMaintenanceModal ?? false)
        <div style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.5)"
            wire:click.self="$set('showMaintenanceModal', false)">
            <div style="background:white;border-radius:16px;padding:24px;width:100%;max-width:450px;box-shadow:0 20px 60px rgba(0,0,0,.2)"
                class="inv-modal">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
                    <h3 style="font-size:18px;font-weight:700">{!! $iWrench !!} Log Maintenance</h3>
                    <button wire:click="$set('showMaintenanceModal', false)" type="button"
                        style="background:none;border:none;font-size:20px;cursor:pointer">&times;</button>
                </div>
                <form wire:submit="submitMaintenance">
                    <div style="display:grid;gap:12px">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Type *</label>
                                <select wire:model="maintenanceForm.type" required
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                                    @foreach(\App\Models\AssetMaintenanceLog::$types as $k => $v) <option value="{{ $k }}">
                                        {{ $v }}
                                    </option> @endforeach
                                </select>
                            </div>
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Cost</label>
                                <input type="number" wire:model="maintenanceForm.cost" step="0.01"
                                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                            </div>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Title *</label>
                            <input type="text" wire:model="maintenanceForm.title" required
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Vendor</label>
                            <input type="text" wire:model="maintenanceForm.vendor"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Description</label>
                            <textarea wire:model="maintenanceForm.description" rows="2"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;resize:vertical"></textarea>
                        </div>
                    </div>
                    <div
                        style="display:flex;justify-content:flex-end;gap:8px;padding-top:16px;margin-top:16px;border-top:1px solid #e5e7eb">
                        <button type="button" wire:click="$set('showMaintenanceModal', false)" class="inv-btn-cancel"
                            style="padding:8px 20px;border-radius:8px;border:1px solid #d1d5db;background:white;font-size:13px;font-weight:600;cursor:pointer">Cancel</button>
                        <button type="submit"
                            style="padding:8px 20px;border-radius:8px;border:none;background:#d97706;color:white;font-size:13px;font-weight:600;cursor:pointer">
                            <span wire:loading.remove wire:target="submitMaintenance">Log Maintenance</span>
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
                            <select wire:model="grnHeader.purchase_order_id"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                                <option value="">-- No PO (Open Receipt) --</option>
                                @foreach($this->getAvailablePOs() as $poId => $poNum)
                                    <option value="{{ $poId }}">{{ $poNum }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Receiving
                                Warehouse *</label>
                            <div style="display:flex;gap:6px">
                                <select wire:model="grnHeader.warehouse_id" required
                                    style="flex:1;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                                    <option value="">-- Select --</option>
                                    @foreach($this->getWarehouseOptions() as $wid => $wname) <option value="{{ $wid }}">
                                        {{ $wname }}
                                    </option> @endforeach
                                </select>
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
                                            <select wire:model="grnItems.{{ $idx }}.product_id"
                                                style="width:100%;padding:6px 8px;border:1px solid #e5e7eb;border-radius:6px;font-size:13px;background:transparent">
                                                <option value="">-- Optional --</option>
                                                @foreach($this->getProductOptions() as $pid => $pname) <option
                                                value="{{ $pid }}">{{ $pname }}</option> @endforeach
                                            </select>
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
                                <select wire:model="transferHeader.from_warehouse_id" required
                                    style="flex:1;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                                    <option value="">-- Select --</option>
                                    @foreach($this->getWarehouseOptions() as $wid => $wname) <option value="{{ $wid }}">
                                        {{ $wname }}
                                    </option> @endforeach
                                </select>
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
                                <select wire:model="transferHeader.to_warehouse_id" required
                                    style="flex:1;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                                    <option value="">-- Select --</option>
                                    @foreach($this->getWarehouseOptions() as $wid => $wname) <option value="{{ $wid }}">
                                        {{ $wname }}
                                    </option> @endforeach
                                </select>
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
                                            <select wire:model="transferItems.{{ $idx }}.product_id" required
                                                style="width:100%;padding:6px 8px;border:1px solid #e5e7eb;border-radius:6px;font-size:13px;background:transparent">
                                                <option value="">-- Select --</option>
                                                @foreach($this->getProductOptions() as $pid => $pname) <option
                                                value="{{ $pid }}">{{ $pname }}</option> @endforeach
                                            </select>
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
                                <select wire:model="adjustmentForm.warehouse_id" required
                                    style="flex:1;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                                    <option value="">-- Select --</option>
                                    @foreach($this->getWarehouseOptions() as $wid => $wname) <option value="{{ $wid }}">
                                        {{ $wname }}
                                    </option> @endforeach
                                </select>
                                <button type="button" wire:click="openQuickWarehouse"
                                    style="padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;background:#f9fafb;cursor:pointer;font-size:14px;font-weight:700;color:#4f46e5"
                                    title="Add Warehouse">+</button>
                            </div>
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Product
                                *</label>
                            <select wire:model="adjustmentForm.product_id" required
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                                <option value="">-- Select --</option>
                                @foreach($this->getProductOptions() as $pid => $pname) <option value="{{ $pid }}">
                                    {{ $pname }}
                                </option> @endforeach
                            </select>
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
                            <select wire:model="poHeader.supplier_id"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px">
                                <option value="">-- Select Supplier --</option>
                                @foreach($this->getSupplierOptions() as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
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
                                            <select wire:model="poItems.{{ $idx }}.product_id"
                                                style="width:100%;padding:6px 8px;border:1px solid #e5e7eb;border-radius:6px;font-size:13px;background:transparent">
                                                <option value="">-- Optional --</option>
                                                @foreach($this->getProductOptions() as $pid => $pname)
                                                    <option value="{{ $pid }}">{{ $pname }}</option>
                                                @endforeach
                                            </select>
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
                                            ${{ number_format(((float) ($item['quantity'] ?? 0)) * ((float) ($item['unit_price'] ?? 0)), 2) }}
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
                                        style="font-weight:600;font-family:monospace">${{ number_format($subtotal, 2) }}</span>
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
                                    <span style="color:#4f46e5;font-family:monospace">${{ number_format($total, 2) }}</span>
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

</x-filament-panels::page>