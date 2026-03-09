<x-layouts.admin title="Admin Dashboard | Dubai Garments AI">
    @php
        $totalLeads = (int) ($metrics['total_leads'] ?? 0);
        $hotLeads = (int) ($metrics['hot_leads'] ?? 0);
        $totalDeals = (int) ($metrics['total_deals'] ?? 0);
        $wonDeals = (int) ($metrics['won_deals'] ?? 0);
        $totalQuotes = (int) ($metrics['total_quotes'] ?? 0);
        $sentQuotes = (int) ($metrics['sent_quotes'] ?? 0);

        $hotLeadRate = $totalLeads > 0 ? (int) round(($hotLeads / $totalLeads) * 100) : 0;
        $winRate = $totalDeals > 0 ? (int) round(($wonDeals / $totalDeals) * 100) : 0;
        $quoteSendRate = $totalQuotes > 0 ? (int) round(($sentQuotes / $totalQuotes) * 100) : 0;

        $leadStatusTotal = (int) collect($leadCountsByStatus)->sum();
        $dealStageTotal = (int) collect($dealCountsByStage)->sum();
    @endphp

    <section class="dg-admin-page">
        <div class="dg-admin-page-head">
            <div>
                <h1 class="dg-page-title">Dashboard Analytics</h1>
                <p class="dg-page-subtitle">Performance overview for lead intake, pipeline progress, and quote conversion.</p>
            </div>
            <div class="dg-admin-toolbar">
                <x-ui.button variant="secondary" :href="route('admin.leads.index')">Open Leads</x-ui.button>
                <x-ui.button variant="secondary" :href="route('admin.deals.index')">Open Pipeline</x-ui.button>
                <x-ui.button variant="secondary" :href="route('admin.quotes.index')">Open Quotes</x-ui.button>
            </div>
        </div>

        <div class="dg-kpi-grid">
            <x-ui.card class="dg-kpi-card">
                <p class="dg-kpi-label">Total Leads</p>
                <p class="dg-kpi-value">{{ $totalLeads }}</p>
                <p class="dg-kpi-meta">{{ $hotLeads }} hot leads ({{ $hotLeadRate }}%)</p>
            </x-ui.card>
            <x-ui.card class="dg-kpi-card">
                <p class="dg-kpi-label">Total Deals</p>
                <p class="dg-kpi-value">{{ $totalDeals }}</p>
                <p class="dg-kpi-meta">Win rate {{ $winRate }}%</p>
            </x-ui.card>
            <x-ui.card class="dg-kpi-card">
                <p class="dg-kpi-label">Total Quotes</p>
                <p class="dg-kpi-value">{{ $totalQuotes }}</p>
                <p class="dg-kpi-meta">{{ $sentQuotes }} sent ({{ $quoteSendRate }}%)</p>
            </x-ui.card>
            <x-ui.card class="dg-kpi-card">
                <p class="dg-kpi-label">Team Users</p>
                <p class="dg-kpi-value">{{ (int) ($metrics['users'] ?? 0) }}</p>
                <p class="dg-kpi-meta">Admin and sales accounts</p>
            </x-ui.card>
        </div>
    </section>

    <section class="dg-admin-page">
        <div class="dg-analytics-grid">
            <x-ui.card class="dg-chart-card">
                <h2 class="dg-title-sm">Lead Status Breakdown</h2>
                <div class="dg-stat-bars">
                    @forelse ($leadCountsByStatus as $status => $count)
                        @php
                            $percentage = $leadStatusTotal > 0 ? (int) round((((int) $count) / $leadStatusTotal) * 100) : 0;
                        @endphp
                        <div class="dg-stat-row">
                            <span>{{ $status }}</span>
                            <progress class="dg-progress" value="{{ (int) $count }}" max="{{ max($leadStatusTotal, 1) }}"></progress>
                            <strong>{{ $percentage }}%</strong>
                        </div>
                    @empty
                        <p class="dg-muted-sm">No lead data available.</p>
                    @endforelse
                </div>
            </x-ui.card>

            <x-ui.card class="dg-chart-card">
                <h2 class="dg-title-sm">Deal Stage Breakdown</h2>
                <div class="dg-stat-bars">
                    @forelse ($dealCountsByStage as $stage => $count)
                        @php
                            $percentage = $dealStageTotal > 0 ? (int) round((((int) $count) / $dealStageTotal) * 100) : 0;
                        @endphp
                        <div class="dg-stat-row">
                            <span>{{ $stage }}</span>
                            <progress class="dg-progress" value="{{ (int) $count }}" max="{{ max($dealStageTotal, 1) }}"></progress>
                            <strong>{{ $percentage }}%</strong>
                        </div>
                    @empty
                        <p class="dg-muted-sm">No deal data available.</p>
                    @endforelse
                </div>
            </x-ui.card>
        </div>
    </section>

    <section class="dg-admin-page">
        <div class="dg-analytics-grid">
            <x-ui.card class="dg-chart-card">
                <h2 class="dg-title-sm">Recent Leads</h2>
                <div class="dg-list">
                    @forelse ($recentLeads as $lead)
                        <div class="dg-list-row">
                            <div class="dg-list-main">
                                <p class="dg-list-title">#{{ $lead->id }} {{ $lead->customer_name ?: 'Unnamed Lead' }}</p>
                                <p class="dg-list-meta">{{ $lead->status }} • {{ $lead->product_type ?: 'No product' }}</p>
                            </div>
                            <x-ui.button variant="secondary" :href="route('admin.leads.show', ['lead' => $lead->id])">Open</x-ui.button>
                        </div>
                    @empty
                        <p class="dg-muted-sm">No recent leads.</p>
                    @endforelse
                </div>
            </x-ui.card>

            <x-ui.card class="dg-chart-card">
                <h2 class="dg-title-sm">Recent Deals</h2>
                <div class="dg-list">
                    @forelse ($recentDeals as $deal)
                        <div class="dg-list-row">
                            <div class="dg-list-main">
                                <p class="dg-list-title">#{{ $deal->id }} {{ $deal->lead?->customer_name ?: 'No customer' }}</p>
                                <p class="dg-list-meta">{{ $deal->stage }} • {{ $deal->priority }} priority</p>
                            </div>
                            <x-ui.button variant="secondary" :href="route('admin.deals.show', ['deal' => $deal->id])">Open</x-ui.button>
                        </div>
                    @empty
                        <p class="dg-muted-sm">No recent deals.</p>
                    @endforelse
                </div>
            </x-ui.card>
        </div>
    </section>

    <section class="dg-admin-page">
        <x-ui.card class="dg-chart-card">
            <div class="dg-admin-head">
                <h2 class="dg-title-sm">Recent Quotes</h2>
                <x-ui.button variant="secondary" :href="route('admin.quotes.index')">View All</x-ui.button>
            </div>
            <div class="dg-table-wrap">
                <table class="dg-table">
                    <thead>
                        <tr>
                            <th>Quote</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentQuotes as $quote)
                            <tr>
                                <td>{{ $quote->quote_number ?: '#'.$quote->id }}</td>
                                <td>{{ $quote->deal?->lead?->customer_name ?: '-' }}</td>
                                <td><span class="dg-status-pill">{{ $quote->status }}</span></td>
                                <td>{{ $quote->currency }} {{ number_format((float) $quote->total_price, 2) }}</td>
                                <td>
                                    <x-ui.button variant="secondary" :href="route('admin.quotes.show', ['quote' => $quote->id])">Open</x-ui.button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">No recent quotes.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </section>
</x-layouts.admin>
