<x-layouts.admin title="Deals Pipeline | Admin">
    <section class="dg-admin-page">
        <div class="dg-admin-page-head">
            <div>
                <h1 class="dg-page-title">Deals Pipeline</h1>
                <p class="dg-page-subtitle">Manage deal progression from qualification to won/lost outcomes.</p>
            </div>
            <div class="dg-admin-toolbar">
                <x-ui.button variant="secondary" :href="route('admin.dashboard')">Dashboard</x-ui.button>
                <x-ui.button variant="secondary" :href="route('admin.quotes.index')">Quotes</x-ui.button>
            </div>
        </div>

        <x-ui.card class="dg-panel">
            <form method="GET" action="{{ route('admin.deals.index') }}" class="dg-form-row">
                <x-ui.input
                    name="search"
                    :value="$search"
                    placeholder="Search by lead, company, product, tracking code..."
                    class="dg-col-fill"
                />
                <select name="stage" class="dg-select dg-select-md">
                    <option value="">All Stages</option>
                    @foreach ($stages as $stageOption)
                        <option value="{{ $stageOption }}" @selected($stage === $stageOption)>{{ $stageOption }}</option>
                    @endforeach
                </select>
                <x-ui.button type="submit">Apply</x-ui.button>
            </form>
        </x-ui.card>
    </section>

    <section class="dg-admin-page">
        <div class="dg-pipeline-grid">
            @foreach ($stages as $pipelineStage)
                <x-ui.card class="dg-pipeline-column">
                    <div class="dg-admin-head">
                        <h2 class="dg-title-sm">{{ $pipelineStage }}</h2>
                        <x-ui.badge>{{ $pipeline[$pipelineStage]->count() }}</x-ui.badge>
                    </div>
                    <div class="dg-pipeline-cards">
                        @forelse ($pipeline[$pipelineStage] as $dealItem)
                            <x-ui.card class="dg-summary-card">
                                <p class="dg-muted-sm"><strong>#{{ $dealItem->id }}</strong> {{ $dealItem->lead?->customer_name ?: '-' }}</p>
                                <p class="dg-muted-sm">{{ $dealItem->lead?->product_type ?: '-' }}</p>
                                <p class="dg-muted-sm">Value: {{ $dealItem->value_estimate ? '$'.number_format((float) $dealItem->value_estimate, 2) : '-' }}</p>
                                <x-ui.button variant="secondary" :href="route('admin.deals.show', ['deal' => $dealItem->id])">Open</x-ui.button>
                            </x-ui.card>
                        @empty
                            <p class="dg-muted-sm">No deals in this stage.</p>
                        @endforelse
                    </div>
                </x-ui.card>
            @endforeach
        </div>
    </section>

    <section class="dg-admin-page">
        <x-ui.card class="dg-panel">
            <div class="dg-admin-head">
                <h2 class="dg-title-sm">All Deals</h2>
                <x-ui.badge>{{ $deals->total() }} Total</x-ui.badge>
            </div>
            <div class="dg-table-wrap">
                <table class="dg-table">
                    <thead>
                        <tr>
                            <th>Deal</th>
                            <th>Lead</th>
                            <th>Stage</th>
                            <th>Priority</th>
                            <th>Value</th>
                            <th>Assigned</th>
                            <th>Updated</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($deals as $deal)
                            <tr>
                                <td>#{{ $deal->id }}</td>
                                <td>{{ $deal->lead?->customer_name ?: '-' }}</td>
                                <td><span class="dg-status-pill dg-status-pill-{{ $deal->stage }}">{{ $deal->stage }}</span></td>
                                <td>{{ $deal->priority }}</td>
                                <td>{{ $deal->value_estimate ? '$'.number_format((float) $deal->value_estimate, 2) : '-' }}</td>
                                <td>{{ $deal->assignedUser?->name ?: '-' }}</td>
                                <td>{{ $deal->updated_at?->format('M d, Y H:i') ?: '-' }}</td>
                                <td><x-ui.button variant="secondary" :href="route('admin.deals.show', ['deal' => $deal->id])">View</x-ui.button></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">No deals found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="dg-hero-actions">
                {{ $deals->links() }}
            </div>
        </x-ui.card>
    </section>
</x-layouts.admin>
