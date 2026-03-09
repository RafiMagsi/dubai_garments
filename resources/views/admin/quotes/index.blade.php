<x-layouts.admin title="Quotes | Admin">
    <section class="dg-admin-page">
        <div class="dg-admin-page-head">
            <div>
                <h1 class="dg-page-title">Quote Builder</h1>
                <p class="dg-page-subtitle">Manage draft, sent, accepted, and expired quotations across all deals.</p>
            </div>
            <div class="dg-admin-toolbar">
                <x-ui.button variant="secondary" :href="route('admin.dashboard')">Dashboard</x-ui.button>
                <x-ui.button variant="secondary" :href="route('admin.deals.index')">Deals</x-ui.button>
            </div>
        </div>

        <x-ui.card class="dg-panel">
            <form method="GET" action="{{ route('admin.quotes.index') }}" class="dg-form-row">
                <x-ui.input
                    name="search"
                    :value="$search"
                    placeholder="Search by quote number, customer, company, tracking code..."
                    class="dg-col-fill"
                />
                <select name="status" class="dg-select dg-select-md">
                    <option value="">All Statuses</option>
                    @foreach ($statuses as $statusOption)
                        <option value="{{ $statusOption }}" @selected($status === $statusOption)>{{ $statusOption }}</option>
                    @endforeach
                </select>
                <x-ui.button type="submit">Apply</x-ui.button>
            </form>
        </x-ui.card>
    </section>

    <section class="dg-admin-page">
        <x-ui.card class="dg-panel">
            <div class="dg-admin-head">
                <h2 class="dg-title-sm">Quotes</h2>
                <x-ui.badge>{{ $quotes->total() }} Total</x-ui.badge>
            </div>
            <div class="dg-table-wrap">
                <table class="dg-table">
                    <thead>
                        <tr>
                            <th>Quote #</th>
                            <th>Deal</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Expires</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($quotes as $quote)
                            <tr>
                                <td>{{ $quote->quote_number ?: '-' }}</td>
                                <td>#{{ $quote->deal_id ?: '-' }}</td>
                                <td>{{ $quote->deal?->lead?->customer_name ?: '-' }}</td>
                                <td><span class="dg-status-pill">{{ $quote->status }}</span></td>
                                <td>{{ $quote->currency }} {{ number_format((float) $quote->total_price, 2) }}</td>
                                <td>{{ $quote->expires_at?->format('M d, Y') ?: '-' }}</td>
                                <td><x-ui.button variant="secondary" :href="route('admin.quotes.show', ['quote' => $quote->id])">Open</x-ui.button></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">No quotes found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="dg-hero-actions">
                {{ $quotes->links() }}
            </div>
        </x-ui.card>
    </section>
</x-layouts.admin>
