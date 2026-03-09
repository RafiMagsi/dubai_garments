<x-layouts.admin title="Follow-ups | Admin">
    <section class="dg-admin-page">
        <div class="dg-admin-page-head">
            <div>
                <h1 class="dg-page-title">Follow-up Automation</h1>
                <p class="dg-page-subtitle">View scheduled, sent, failed, and skipped automated follow-up emails.</p>
            </div>
            <div class="dg-admin-toolbar">
                <x-ui.button variant="secondary" :href="route('admin.quotes.index')">Quotes</x-ui.button>
            </div>
        </div>

        <x-ui.card class="dg-panel">
            <form method="GET" action="{{ route('admin.followups.index') }}" class="dg-form-row">
                <select name="status" class="dg-select dg-select-md">
                    <option value="">All Statuses</option>
                    @foreach ($statuses as $statusOption)
                        <option value="{{ $statusOption }}" @selected($status === $statusOption)>{{ strtoupper($statusOption) }}</option>
                    @endforeach
                </select>
                <x-ui.input name="quote" :value="$quoteNumber" placeholder="Search by quote number..." class="dg-col-fill" />
                <x-ui.button type="submit">Apply</x-ui.button>
            </form>
        </x-ui.card>
    </section>

    <section class="dg-admin-page">
        <x-ui.card class="dg-panel">
            <div class="dg-admin-head">
                <h2 class="dg-title-sm">Automation Queue</h2>
                <x-ui.badge>{{ $followups->total() }} Items</x-ui.badge>
            </div>

            <div class="dg-table-wrap">
                <table class="dg-table">
                    <thead>
                        <tr>
                            <th>Step</th>
                            <th>Quote</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Next Run</th>
                            <th>Sent At</th>
                            <th>Error</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($followups as $followup)
                            <tr>
                                <td>{{ $followup->step ?: '-' }}</td>
                                <td>
                                    @if ($followup->quote)
                                        <a class="dg-link-primary" href="{{ route('admin.quotes.show', ['quote' => $followup->quote->id]) }}">
                                            {{ $followup->quote->quote_number ?: '#'.$followup->quote->id }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $followup->deal?->lead?->customer_name ?: '-' }}</td>
                                <td><span class="dg-status-pill">{{ strtoupper($followup->status) }}</span></td>
                                <td>{{ $followup->next_run?->format('M d, Y H:i') ?: '-' }}</td>
                                <td>{{ $followup->sent_at?->format('M d, Y H:i') ?: '-' }}</td>
                                <td>{{ $followup->error_message ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">No follow-ups found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="dg-hero-actions">
                {{ $followups->links() }}
            </div>
        </x-ui.card>
    </section>
</x-layouts.admin>
