<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quote {{ $quote->quote_number }}</title>
    @php
        $invoiceCss = file_get_contents(resource_path('css/pdf-invoice.css'));
    @endphp
    <style>
        {!! $invoiceCss !!}
    </style>
</head>
<body>
    <div class="invoice-shell">
        <div class="topbar">
            <table class="topbar-table">
                <tr>
                    <td>
                        <div class="brand">Dubai Garments</div>
                        <div class="brand-sub">Bulk Apparel and Uniform Solutions</div>
                    </td>
                    <td>
                        <div class="doc-title">QUOTE</div>
                        <div class="doc-number">{{ $quote->quote_number }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <table class="meta-grid">
            <tr>
                <td>
                    <p class="meta-title">Billed To</p>
                    <p class="meta-line">{{ $quote->deal?->lead?->customer_name ?: '-' }}</p>
                    <p class="meta-line">{{ $quote->deal?->lead?->company ?: '-' }}</p>
                    <p class="meta-line">{{ $quote->deal?->lead?->email ?: '-' }}</p>
                    <p class="meta-line">{{ $quote->deal?->lead?->phone ?: '-' }}</p>
                </td>
                <td>
                    <p class="meta-title">Quote Details</p>
                    <p class="meta-line"><strong>Status:</strong> {{ $quote->status }}</p>
                    <p class="meta-line"><strong>Date:</strong> {{ $quote->created_at?->format('M d, Y') ?: '-' }}</p>
                    <p class="meta-line"><strong>Expires:</strong> {{ $quote->expires_at?->format('M d, Y') ?: '-' }}</p>
                    <p class="meta-line"><strong>Currency:</strong> {{ $quote->currency }}</p>
                </td>
            </tr>
        </table>

        <p class="section-title">Line Items</p>
        <table class="items">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ((array) $quote->items_json as $item)
                    <tr>
                        <td>{{ $item['name'] ?? '-' }}</td>
                        <td class="text-right">{{ $item['quantity'] ?? '-' }}</td>
                        <td class="text-right">{{ $quote->currency }} {{ number_format((float) ($item['unit_price'] ?? 0), 2) }}</td>
                        <td class="text-right">{{ $quote->currency }} {{ number_format((float) ($item['line_total'] ?? 0), 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals-wrap">
            <table class="totals">
                <tr>
                    <td class="label">Subtotal</td>
                    <td class="text-right">{{ $quote->currency }} {{ number_format((float) $quote->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">Discount</td>
                    <td class="text-right">{{ $quote->currency }} {{ number_format((float) $quote->discount, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td>Total</td>
                    <td class="text-right">{{ $quote->currency }} {{ number_format((float) $quote->total_price, 2) }}</td>
                </tr>
            </table>
        </div>

        @if ($quote->notes)
            <div class="notes">
                <p class="notes-title">Notes</p>
                <p class="notes-copy">{{ $quote->notes }}</p>
            </div>
        @endif

        <p class="footer">
            Generated from Dubai Garments CRM on {{ now()->format('M d, Y H:i') }}.
        </p>
    </div>
</body>
</html>
