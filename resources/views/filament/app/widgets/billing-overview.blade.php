<x-filament-widgets::widget>
    @php $data = $this->getData(); @endphp

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        {{-- Active Projects --}}
        <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-2">
                <div class="rounded-lg bg-primary-50 p-2 dark:bg-primary-500/10">
                    <x-heroicon-o-building-office-2 class="h-5 w-5 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Active Projects</p>
                    <p class="text-2xl font-bold text-gray-950 dark:text-white">{{ $data['activeProjects'] }}</p>
                </div>
            </div>
        </div>

        {{-- Current Month Bill --}}
        <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-2">
                <div class="rounded-lg bg-info-50 p-2 dark:bg-info-500/10">
                    <x-heroicon-o-banknotes class="h-5 w-5 text-info-600 dark:text-info-400" />
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">This Month's Bill</p>
                    <p class="text-2xl font-bold text-gray-950 dark:text-white">
                        @if($data['currentBill'])
                            ${{ number_format($data['currentBill']->total_amount, 2) }}
                        @else
                            <span class="text-sm text-gray-400">—</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- Overdue Invoices --}}
        <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-2">
                <div
                    class="rounded-lg {{ $data['overdueInvoices'] > 0 ? 'bg-danger-50 dark:bg-danger-500/10' : 'bg-success-50 dark:bg-success-500/10' }} p-2">
                    <x-heroicon-o-exclamation-triangle
                        class="h-5 w-5 {{ $data['overdueInvoices'] > 0 ? 'text-danger-600 dark:text-danger-400' : 'text-success-600 dark:text-success-400' }}" />
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Overdue Invoices</p>
                    <p
                        class="text-2xl font-bold {{ $data['overdueInvoices'] > 0 ? 'text-danger-600 dark:text-danger-400' : 'text-gray-950 dark:text-white' }}">
                        {{ $data['overdueInvoices'] }}
                    </p>
                    @if($data['overdueAmount'] > 0)
                        <p class="text-xs text-danger-500">${{ number_format($data['overdueAmount'], 2) }} outstanding</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Unpaid Platform Bills --}}
        <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-2">
                <div
                    class="rounded-lg {{ $data['unpaidBills'] > 0 ? 'bg-warning-50 dark:bg-warning-500/10' : 'bg-success-50 dark:bg-success-500/10' }} p-2">
                    <x-heroicon-o-credit-card
                        class="h-5 w-5 {{ $data['unpaidBills'] > 0 ? 'text-warning-600 dark:text-warning-400' : 'text-success-600 dark:text-success-400' }}" />
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Unpaid Bills</p>
                    <p
                        class="text-2xl font-bold {{ $data['unpaidBills'] > 0 ? 'text-warning-600 dark:text-warning-400' : 'text-gray-950 dark:text-white' }}">
                        {{ $data['unpaidBills'] }}
                    </p>
                    @if($data['unpaidBillsAmount'] > 0)
                        <p class="text-xs text-warning-500">${{ number_format($data['unpaidBillsAmount'], 2) }} due</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>