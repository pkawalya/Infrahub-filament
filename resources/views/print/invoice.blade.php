<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    @php $brandColor = $company->primary_color ?? '#4f46e5'; @endphp
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1f2937;
            font-size: 13px;
            line-height: 1.6;
            background: #fff;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
        }

        /* ── Header ── */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
            border-bottom: 3px solid
                {{ $brandColor }}
            ;
            padding-bottom: 24px;
        }

        .company-info h1 {
            font-size: 24px;
            color:
                {{ $brandColor }}
            ;
            margin-bottom: 4px;
        }

        .company-info p {
            color: #6b7280;
            font-size: 12px;
        }

        .invoice-badge {
            text-align: right;
        }

        .invoice-badge h2 {
            font-size: 32px;
            font-weight: 800;
            color:
                {{ $brandColor }}
            ;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .invoice-badge .inv-number {
            font-size: 16px;
            color: #6b7280;
            margin-top: 4px;
            font-weight: 600;
        }

        /* ── Status ── */
        .status-badge {
            display: inline-block;
            padding: 4px 16px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 8px;
        }

        .status-draft {
            background: #f3f4f6;
            color: #6b7280;
        }

        .status-sent {
            background: #dbeafe;
            color: #2563eb;
        }

        .status-partially_paid {
            background: #fef3c7;
            color: #d97706;
        }

        .status-paid {
            background: #d1fae5;
            color: #059669;
        }

        .status-overdue {
            background: #fee2e2;
            color: #dc2626;
        }

        .status-cancelled {
            background: #f3f4f6;
            color: #9ca3af;
        }

        /* ── Meta Grid ── */
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 32px;
        }

        .meta-box {
            background: #f9fafb;
            border-radius: 10px;
            padding: 20px;
            border: 1px solid #e5e7eb;
        }

        .meta-box h3 {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #6b7280;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .meta-box p {
            font-size: 13px;
            margin-bottom: 2px;
        }

        .meta-box .name {
            font-weight: 700;
            font-size: 15px;
            color: #111827;
        }

        /* ── Dates row ── */
        .dates-row {
            display: flex;
            gap: 32px;
            margin-bottom: 32px;
        }

        .date-item {
            flex: 1;
        }

        .date-item .label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6b7280;
            font-weight: 700;
        }

        .date-item .value {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
        }

        /* ── Items Table ── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .items-table thead th {
            background:
                {{ $brandColor }}
            ;
            color: white;
            padding: 12px 16px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
        }

        .items-table thead th:first-child {
            border-radius: 8px 0 0 0;
        }

        .items-table thead th:last-child {
            border-radius: 0 8px 0 0;
            text-align: right;
        }

        .items-table thead th.right {
            text-align: right;
        }

        .items-table thead th.center {
            text-align: center;
        }

        .items-table tbody td {
            padding: 12px 16px;
            border-bottom: 1px solid #f3f4f6;
        }

        .items-table tbody tr:hover {
            background: #f9fafb;
        }

        .items-table tbody td.right {
            text-align: right;
        }

        .items-table tbody td.center {
            text-align: center;
        }

        .items-table tbody td.desc {
            font-weight: 500;
        }

        .items-table tfoot td {
            padding: 10px 16px;
            font-weight: 600;
        }

        .items-table tfoot td.right {
            text-align: right;
        }

        .items-table tfoot .total-row td {
            font-size: 16px;
            font-weight: 800;
            color:
                {{ $brandColor }}
            ;
            border-top: 2px solid
                {{ $brandColor }}
            ;
        }

        /* ── Summary ── */
        .summary-grid {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 32px;
        }

        .summary-box {
            width: 300px;
        }

        .summary-line {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
            font-size: 13px;
        }

        .summary-line.total {
            border-top: 2px solid
                {{ $brandColor }}
            ;
            border-bottom: none;
            font-size: 18px;
            font-weight: 800;
            color:
                {{ $brandColor }}
            ;
            padding: 12px 0;
        }

        .summary-line.balance {
            background: #fef2f2;
            padding: 10px 12px;
            border-radius: 8px;
            border: none;
            color: #dc2626;
            font-weight: 700;
        }

        .summary-line.balance.paid {
            background: #d1fae5;
            color: #059669;
        }

        /* ── Notes ── */
        .notes-section {
            background: #f9fafb;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 24px;
            border: 1px solid #e5e7eb;
        }

        .notes-section h3 {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6b7280;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .notes-section p {
            color: #374151;
            font-size: 12px;
        }

        /* ── Footer ── */
        .invoice-footer {
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
            text-align: center;
            color: #9ca3af;
            font-size: 11px;
        }

        .invoice-footer p {
            margin-bottom: 4px;
        }

        /* ── Print ── */
        @media print {
            body {
                font-size: 12px;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .invoice-container {
                padding: 15mm 12mm 20mm 12mm;
                max-width: 100%;
            }

            .no-print {
                display: none !important;
            }

            .items-table thead th {
                background:
                    {{ $brandColor }}
                    !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .status-badge {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    <div class="no-print"
        style="background: #f3f4f6; padding: 12px; text-align: center; border-bottom: 1px solid #e5e7eb;">
        <button onclick="window.print()"
            style="background: {{ $brandColor }}; color: white; border: none; padding: 10px 28px; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 14px; margin-right: 8px;">🖨️
            Print Invoice</button>
        <button onclick="window.history.back()"
            style="background: #6b7280; color: white; border: none; padding: 10px 28px; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 14px;">←
            Back</button>
    </div>

    <div class="invoice-container">
        {{-- Header --}}
        <div class="invoice-header">
            <div class="company-info">
                @if($company->getLogoUrl())
                    <img src="{{ $company->getLogoUrl() }}" alt="{{ $company->name }}"
                        style="max-height:48px;max-width:200px;margin-bottom:8px;">
                @endif
                <h1 style="color: {{ $brandColor }};">{{ $company->name ?? config('app.name') }}</h1>
                @if($company->address ?? false)
                <p>{{ $company->address }}</p>@endif
                @if($company->email ?? false)
                <p>{{ $company->email }}</p>@endif
                @if($company->phone ?? false)
                <p>{{ $company->phone }}</p>@endif
            </div>
            <div class="invoice-badge">
                <h2>Invoice</h2>
                <div class="inv-number"># {{ $invoice->invoice_number }}</div>
                <span
                    class="status-badge status-{{ $invoice->status }}">{{ \App\Models\Invoice::$statuses[$invoice->status] ?? $invoice->status }}</span>
            </div>
        </div>

        {{-- Client + Project Meta --}}
        <div class="meta-grid">
            <div class="meta-box">
                <h3>Bill To</h3>
                @if($invoice->client)
                    <p class="name">{{ $invoice->client->name }}</p>
                    @if($invoice->client->company_name)
                    <p>{{ $invoice->client->company_name }}</p>@endif
                    @if($invoice->client->email)
                    <p>{{ $invoice->client->email }}</p>@endif
                    @if($invoice->client->phone)
                    <p>{{ $invoice->client->phone }}</p>@endif
                    @if($invoice->client->address)
                    <p>{{ $invoice->client->address }}</p>@endif
                @else
                    <p class="name">—</p>
                @endif
            </div>
            <div class="meta-box">
                <h3>Project Details</h3>
                @if($invoice->cdeProject)
                    <p class="name">{{ $invoice->cdeProject->name }}</p>
                    @if($invoice->cdeProject->code)
                    <p>Code: {{ $invoice->cdeProject->code }}</p>@endif
                @endif
                @if($invoice->workOrder)
                    <p>WO: {{ $invoice->workOrder->wo_number }} — {{ $invoice->workOrder->title }}</p>
                @endif
            </div>
        </div>

        {{-- Dates --}}
        <div class="dates-row">
            <div class="date-item">
                <div class="label">Issue Date</div>
                <div class="value">{{ $invoice->issue_date?->format('M d, Y') ?? '—' }}</div>
            </div>
            <div class="date-item">
                <div class="label">Due Date</div>
                <div class="value">{{ $invoice->due_date?->format('M d, Y') ?? '—' }}</div>
            </div>
            <div class="date-item">
                <div class="label">Payment Terms</div>
                <div class="value">
                    {{ $invoice->due_date && $invoice->issue_date ? $invoice->issue_date->diffInDays($invoice->due_date) . ' days' : '—' }}
                </div>
            </div>
        </div>

        {{-- Line Items --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 45%;">Description</th>
                    <th class="center" style="width: 10%;">Qty</th>
                    <th class="center" style="width: 10%;">Unit</th>
                    <th class="right" style="width: 15%;">Unit Price</th>
                    <th class="right" style="width: 15%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoice->items as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="desc">{{ $item->description }}</td>
                        <td class="center">{{ $item->quantity }}</td>
                        <td class="center">{{ $item->unit ?? '—' }}</td>
                        <td class="right">{{ \App\Support\CurrencyHelper::format($item->unit_price) }}</td>
                        <td class="right">{{ \App\Support\CurrencyHelper::format($item->amount) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 24px; color: #9ca3af;">No line items added — see
                            totals below</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Totals Summary --}}
        <div class="summary-grid">
            <div class="summary-box">
                <div class="summary-line">
                    <span>Subtotal</span>
                    <span>{{ \App\Support\CurrencyHelper::format($invoice->subtotal) }}</span>
                </div>
                @if($invoice->tax_rate > 0)
                    <div class="summary-line">
                        <span>Tax ({{ $invoice->tax_rate }}%)</span>
                        <span>{{ \App\Support\CurrencyHelper::format($invoice->tax_amount) }}</span>
                    </div>
                @endif
                @if($invoice->discount_amount > 0)
                    <div class="summary-line">
                        <span>Discount</span>
                        <span>-{{ \App\Support\CurrencyHelper::format($invoice->discount_amount) }}</span>
                    </div>
                @endif
                <div class="summary-line total">
                    <span>Total</span>
                    <span>{{ \App\Support\CurrencyHelper::format($invoice->total_amount) }}</span>
                </div>
                <div class="summary-line">
                    <span>Amount Paid</span>
                    <span>{{ \App\Support\CurrencyHelper::format($invoice->amount_paid) }}</span>
                </div>
                <div class="summary-line balance {{ $invoice->balance_due <= 0 ? 'paid' : '' }}">
                    <span>Balance Due</span>
                    <span>{{ \App\Support\CurrencyHelper::format($invoice->balance_due) }}</span>
                </div>
            </div>
        </div>

        {{-- Payment History --}}
        @if($invoice->payments->count() > 0)
            <div class="notes-section" style="margin-bottom: 24px;">
                <h3>Payment History</h3>
                <table style="width: 100%; border-collapse: collapse; margin-top: 8px;">
                    <thead>
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <th style="text-align: left; padding: 6px 0; font-size: 11px; color: #6b7280;">Date</th>
                            <th style="text-align: left; padding: 6px 0; font-size: 11px; color: #6b7280;">Method</th>
                            <th style="text-align: left; padding: 6px 0; font-size: 11px; color: #6b7280;">Ref</th>
                            <th style="text-align: right; padding: 6px 0; font-size: 11px; color: #6b7280;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->payments as $payment)
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 6px 0;">{{ $payment->payment_date?->format('M d, Y') ?? '—' }}</td>
                                <td style="padding: 6px 0;">
                                    {{ ucwords(str_replace('_', ' ', $payment->payment_method ?? '—')) }}
                                </td>
                                <td style="padding: 6px 0;">{{ $payment->reference ?? '—' }}</td>
                                <td style="text-align: right; padding: 6px 0; font-weight: 600; color: #059669;">
                                    {{ \App\Support\CurrencyHelper::format($payment->amount) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Notes --}}
        @if($invoice->notes)
            <div class="notes-section">
                <h3>Notes</h3>
                <p>{{ $invoice->notes }}</p>
            </div>
        @endif

        {{-- Terms --}}
        @if($invoice->terms_and_conditions)
            <div class="notes-section">
                <h3>Terms & Conditions</h3>
                <p>{{ $invoice->terms_and_conditions }}</p>
            </div>
        @endif

        {{-- Footer --}}
        <div class="invoice-footer">
            <p><strong>Thank you for your business!</strong></p>
            <p>{{ $company->name ?? config('app.name') }} {{ $company->phone ? '· ' . $company->phone : '' }}
                {{ $company->email ? '· ' . $company->email : '' }}
            </p>
        </div>
    </div>
</body>

</html>