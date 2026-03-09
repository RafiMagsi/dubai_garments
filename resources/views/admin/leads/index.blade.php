<x-layouts.storefront title="Admin Leads | Dubai Garments AI">
    <main class="dg-main">
        <section class="dg-section">
            <div class="dg-container">
                <x-ui.card class="dg-info-card">
                    <div class="dg-admin-head">
                        <div>
                            <h1 class="dg-section-title">Lead Management</h1>
                            <p class="dg-section-copy">Track quote requests, qualify leads, and update pipeline status.</p>
                        </div>
                        <div class="dg-actions-wrap">
                            <x-ui.button variant="secondary" :href="route('admin.users.index')">Users</x-ui.button>
                            <x-ui.button variant="secondary" :href="route('storefront.home')">Store</x-ui.button>
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <x-ui.button variant="secondary" type="submit">Logout</x-ui.button>
                            </form>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('admin.leads.index') }}" class="dg-form-row">
                        <x-ui.input
                            name="search"
                            :value="$search"
                            placeholder="Search by name, company, email, tracking code..."
                        />
                        <select name="status" class="dg-select dg-select-md">
                            <option value="">All Statuses</option>
                            @foreach ($statuses as $statusOption)
                                <option value="{{ $statusOption }}" @selected($status === $statusOption)>{{ $statusOption }}</option>
                            @endforeach
                        </select>
                        <x-ui.button type="submit">Filter</x-ui.button>
                    </form>

                    <div class="dg-table-wrap">
                        <table class="dg-table">
                            <thead>
                                <tr>
                                    <th>Lead</th>
                                    <th>Tracking Code</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($leads as $lead)
                                    <tr>
                                        <td>
                                            <div>{{ $lead->customer_name ?: '-' }}</div>
                                            <div class="dg-help">{{ $lead->email ?: '-' }}</div>
                                        </td>
                                        <td>{{ $lead->tracking_code ?: '-' }}</td>
                                        <td>{{ $lead->product_type ?: '-' }}</td>
                                        <td>{{ $lead->quantity ? $lead->quantity.' pcs' : '-' }}</td>
                                        <td>
                                            <span class="dg-status-pill dg-status-pill-{{ $lead->status }}">{{ $lead->status }}</span>
                                        </td>
                                        <td>{{ $lead->created_at?->format('M d, Y H:i') ?: '-' }}</td>
                                        <td>
                                            <x-ui.button variant="secondary" :href="route('admin.leads.show', ['lead' => $lead->id])">
                                                View
                                            </x-ui.button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">No leads found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="dg-hero-actions">
                        {{ $leads->links() }}
                    </div>
                </x-ui.card>
            </div>
        </section>
    </main>
</x-layouts.storefront>
