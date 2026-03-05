<x-filament-panels::page>
    @include('filament.app.pages.modules.partials.stat-cards', ['stats' => $this->getStats()])

    @push('styles')
        <style>
            .boq-help-strip {
                background: #eff6ff;
                border: 1px solid #bfdbfe;
                border-radius: 6px;
                padding: 6px 14px;
                margin-bottom: 0.75rem;
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 11px;
                color: #1e40af;
            }

            .dark .boq-help-strip {
                background: rgba(30, 64, 175, .08);
                border-color: rgba(59, 130, 246, .15);
                color: #93c5fd;
            }

            .boq-help-strip kbd {
                background: rgba(30, 64, 175, .08);
                border: 1px solid rgba(30, 64, 175, .12);
                border-radius: 3px;
                padding: 0 4px;
                font-size: 10px;
                font-family: monospace;
            }

            .dark .boq-help-strip kbd {
                background: rgba(96, 165, 250, .1);
                border-color: rgba(96, 165, 250, .15);
            }
        </style>
    @endpush

    {{-- Compact help strip --}}
    <div class="boq-help-strip">
        <span>📋</span>
        <span><strong>Import Items:</strong> Click <kbd>⋯</kbd> on any BOQ → <strong>Bulk Add (Paste)</strong> to paste
            from
            Excel, or <strong>Bulk Upload (File)</strong> to upload a CSV file. Format: Code, Description, Unit, Qty,
            Rate</span>
    </div>

    {{ $this->table }}
</x-filament-panels::page>