<x-layouts.storefront title="Deals Pipeline | Admin">
    <main class="dg-main">
        <section class="dg-section">
            <div class="dg-container">
                <x-ui.card class="dg-info-card">
                    <div class="dg-admin-head">
                        <div>
                            <h1 class="dg-section-title">Deals Pipeline</h1>
                            <p class="dg-section-copy">Track deals across stages and manage sales pipeline progression.</p>
                        </div>
                        <div class="dg-actions-wrap">
                            <x-ui.button variant="secondary" :href="route('admin.leads.index')">Leads</x-ui.button>
                            <x-ui.button variant="secondary" :href="route('admin.users.index')">Users</x-ui.button>
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <x-ui.button variant="secondary" type="submit">Logout</x-ui.button>
                            </form>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('admin.deals.index') }}" class="dg-form-row">
                        <x-ui.input
                            name="search"
                            :value="$search"
                            placeholder="Search by lead name, company, product, tracking code, notes..."
                        />
                        <select name="stage" class="dg-select dg-select-md">
                            <option value="">All Stages</option>
                            @foreach ($stages as $stageOption)
                                <option value="{{ $stageOption }}" @selected($stage === $stageOption)>{{ $stageOption }}</option>
                            @endforeach
                        </select>
                        <x-ui.button type="submit">Filter</x-ui.button>
                    </form>
                </x-ui.card>
            </div>
        </section>

        <section class="dg-section">
            <div class="dg-container">
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
                                        <p class="dg-muted-sm"><strong>Deal:</strong> #{{ $dealItem->id }}</p>
                                        <p class="dg-muted-sm"><strong>Lead:</strong> {{ $dealItem->lead?->customer_name ?: '-' }}</p>
                                        <p class="dg-muted-sm"><strong>Product:</strong> {{ $dealItem->lead?->product_type ?: '-' }}</p>
                                        <p class="dg-muted-sm"><strong>Value:</strong> {{ $dealItem->value_estimate ? '$'.number_format((float) $dealItem->value_estimate, 2) : '-' }}</p>
                                        <div class="dg-hero-actions">
                                            <x-ui.button variant="secondary" :href="route('admin.deals.show', ['deal' => $dealItem->id])">Open</x-ui.button>
                                        </div>
                                    </x-ui.card>
                                @empty
                                    <p class="dg-muted-sm">No deals in this stage.</p>
                                @endforelse
                            </div>
                        </x-ui.card>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="dg-section">
            <div class="dg-container">
                <x-ui.card class="dg-info-card">
                    <h2 class="dg-title-sm">All Deals</h2>
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
                                        <td>
                                            <x-ui.button variant="secondary" :href="route('admin.deals.show', ['deal' => $deal->id])">View</x-ui.button>
                                        </td>
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
            </div>
        </section>
    </main>
</x-layouts.storefront>
