<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation {{ $quotation->quotation_number }}</title>
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

        .quote-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
        }

        /* ── Header ── */
        .quote-header {
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

        .quote-badge {
            text-align: right;
        }

        .quote-badge h2 {
            font-size: 28px;
            font-weight: 800;
            color:
                {{ $brandColor }}
            ;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .quote-badge .quote-number {
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

        .status-viewed {
            background: #e0e7ff;
            color: #4338ca;
        }

        .status-accepted {
            background: #d1fae5;
            color: #059669;
        }

        .status-rejected {
            background: #fee2e2;
            color: #dc2626;
        }

        .status-expired {
            background: #fef3c7;
            color: #d97706;
        }

        .status-invoiced {
            background: #dbeafe;
            color: #1d4ed8;
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

        /* ── Validity Banner ── */
        .validity-banner {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 24px;
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .validity-banner .icon {
            font-size: 24px;
        }

        .validity-banner .text {
            color: #1e40af;
            font-size: 13px;
            font-weight: 500;
        }

        .validity-banner.expired {
            background: #fef2f2;
            border-color: #fecaca;
        }

        .validity-banner.expired .text {
            color: #dc2626;
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
            white-space: pre-line;
        }

        /* ── Footer ── */
        .quote-footer {
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
            text-align: center;
            color: #9ca3af;
            font-size: 11px;
        }

        .quote-footer p {
            margin-bottom: 4px;
        }

        /* ── Print ── */
        @media print {
            body {
                font-size: 12px;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .quote-container {
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
            Print Quotation</button>
        <button onclick="window.history.back()"
            style="background: #6b7280; color: white; border: none; padding: 10px 28px; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 14px;">←
            Back</button>
    </div>

    <div class="quote-container">
        {{-- Header --}}
        <div class="quote-header">
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
            <div class="quote-badge">
                <h2>Quotation</h2>
                <div class="quote-number"># {{ $quotation->quotation_number }}</div>
                <span
                    class="status-badge status-{{ $quotation->status }}">{{ \App\Models\Quotation::$statuses[$quotation->status] ?? $quotation->status }}</span>
            </div>
        </div>

        {{-- Client + Project Meta --}}
        <div class="meta-grid">
            <div class="meta-box">
                <h3>Prepared For</h3>
                @if($quotation->client)
                    <p class="name">{{ $quotation->client->name }}</p>
                    @if($quotation->client->company_name)
                    <p>{{ $quotation->client->company_name }}</p>@endif
                    @if($quotation->client->email)
                    <p>{{ $quotation->client->email }}</p>@endif
                    @if($quotation->client->phone)
                    <p>{{ $quotation->client->phone }}</p>@endif
                    @if($quotation->client->address)
                    <p>{{ $quotation->client->address }}</p>@endif
                @else
                    <p class="name">—</p>
                @endif
            </div>
            <div class="meta-box">
                <h3>Project Details</h3>
                @if($quotation->project)
                    <p class="name">{{ $quotation->project->name }}</p>
                    @if($quotation->project->code)
                    <p>Code: {{ $quotation->project->code }}</p>@endif
                @endif
                @if($quotation->reference)
                    <p>Ref: {{ $quotation->reference }}</p>
                @endif
                @if($quotation->title)
                    <p style="margin-top:8px;font-weight:600;">{{ $quotation->title }}</p>
                @endif
            </div>
        </div>

        {{-- Dates --}}
        <div class="dates-row">
            <div class="date-item">
                <div class="label">Issue Date</div>
                <div class="value">{{ $quotation->issue_date?->format('M d, Y') ?? '—' }}</div>
            </div>
            <div class="date-item">
                <div class="label">Valid Until</div>
                <div class="value" style="{{ $quotation->isExpired() ? 'color:#dc2626;' : '' }}">
                    {{ $quotation->valid_until?->format('M d, Y') ?? '—' }}
                    @if($quotation->isExpired())
                        <span style="font-size:11px;color:#dc2626;"> (EXPIRED)</span>
                    @endif
                </div>
            </div>
            <div class="date-item">
                <div class="label">Prepared By</div>
                <div class="value">{{ $quotation->creator?->name ?? '—' }}</div>
            </div>
        </div>

        {{-- Validity Banner --}}
        @if($quotation->valid_until)
            <div class="validity-banner {{ $quotation->isExpired() ? 'expired' : '' }}">
                <span class="icon">{{ $quotation->isExpired() ? '⚠️' : '📋' }}</span>
                <span class="text">
                    @if($quotation->isExpired())
                        This quotation expired on {{ $quotation->valid_until->format('M d, Y') }}.
                    @elseif($quotation->status === 'accepted')
                        This quotation was
                        accepted{{ $quotation->accepted_at ? ' on ' . $quotation->accepted_at->format('M d, Y') : '' }}.
                    @else
                        This quotation is valid until {{ $quotation->valid_until->format('M d, Y') }}
                        ({{ now()->diffInDays($quotation->valid_until, false) }} days remaining).
                    @endif
                </span>
            </div>
        @endif

        {{-- Scope of Work --}}
        @if($quotation->scope_of_work)
            <div class="notes-section">
                <h3>Scope of Work</h3>
                <p>{{ $quotation->scope_of_work }}</p>
            </div>
        @endif

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
                @forelse($quotation->items as $i => $item)
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
                        <td colspan="6" style="text-align: center; padding: 24px; color: #9ca3af;">No line items</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Totals Summary --}}
        <div class="summary-grid">
            <div class="summary-box">
                <div class="summary-line">
                    <span>Subtotal</span>
                    <span>{{ \App\Support\CurrencyHelper::format($quotation->subtotal) }}</span>
                </div>
                @if($quotation->tax_rate > 0)
                    <div class="summary-line">
                        <span>Tax ({{ $quotation->tax_rate }}%)</span>
                        <span>{{ \App\Support\CurrencyHelper::format($quotation->tax_amount) }}</span>
                    </div>
                @endif
                @if($quotation->discount_amount > 0)
                    <div class="summary-line">
                        <span>Discount</span>
                        <span>-{{ \App\Support\CurrencyHelper::format($quotation->discount_amount) }}</span>
                    </div>
                @endif
                <div class="summary-line total">
                    <span>Total</span>
                    <span>{{ \App\Support\CurrencyHelper::format($quotation->total_amount) }}</span>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        @if($quotation->notes)
            <div class="notes-section">
                <h3>Notes</h3>
                <p>{{ $quotation->notes }}</p>
            </div>
        @endif

        {{-- Terms --}}
        @if($quotation->terms_and_conditions)
            <div class="notes-section">
                <h3>Terms & Conditions</h3>
                <p>{{ $quotation->terms_and_conditions }}</p>
            </div>
        @endif

        {{-- Footer --}}
        <div class="quote-footer">
            <p><strong>Thank you for considering our services!</strong></p>
            <p>{{ $company->name ?? config('app.name') }} {{ $company->phone ? '· ' . $company->phone : '' }}
                {{ $company->email ? '· ' . $company->email : '' }}
            </p>
        </div>
    </div>
</body>

</html>