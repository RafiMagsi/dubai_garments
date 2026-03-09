<x-layouts.storefront title="Lead #{{ $lead->id }} | Admin">
    <main class="dg-main">
        <section class="dg-section">
            <div class="dg-container dg-two-col-grid">
                <x-ui.card class="dg-info-card">
                    <div class="dg-admin-head">
                        <h1 class="dg-section-title">Lead Detail</h1>
                        <div class="dg-actions-wrap">
                            <x-ui.button variant="secondary" :href="route('admin.leads.index')">Back to Leads</x-ui.button>
                            <x-ui.button variant="secondary" :href="route('admin.users.index')">Users</x-ui.button>
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <x-ui.button variant="secondary" type="submit">Logout</x-ui.button>
                            </form>
                        </div>
                    </div>

                    @if (session('status'))
                        <div class="dg-alert-success">{{ session('status') }}</div>
                    @endif

                    <p class="dg-muted-sm"><strong>Tracking Code:</strong> {{ $lead->tracking_code ?: '-' }}</p>
                    <p class="dg-muted-sm"><strong>Name:</strong> {{ $lead->customer_name ?: '-' }}</p>
                    <p class="dg-muted-sm"><strong>Company:</strong> {{ $lead->company ?: '-' }}</p>
                    <p class="dg-muted-sm"><strong>Email:</strong> {{ $lead->email ?: '-' }}</p>
                    <p class="dg-muted-sm"><strong>Phone:</strong> {{ $lead->phone ?: '-' }}</p>
                    <p class="dg-muted-sm"><strong>Product:</strong> {{ $lead->product_type ?: '-' }}</p>
                    <p class="dg-muted-sm"><strong>Quantity:</strong> {{ $lead->quantity ? $lead->quantity.' pcs' : '-' }}</p>
                    <p class="dg-muted-sm"><strong>Required Delivery:</strong> {{ $lead->required_delivery_date?->format('M d, Y') ?: '-' }}</p>
                    <p class="dg-muted-sm"><strong>Status:</strong> <span class="dg-status-pill dg-status-pill-{{ $lead->status }}">{{ $lead->status }}</span></p>
                    <p class="dg-muted-sm"><strong>AI Score:</strong> {{ $lead->ai_score ?? '-' }}</p>
                    <p class="dg-muted-sm"><strong>Classification:</strong> {{ $lead->classification ?? '-' }}</p>

                    @php
                        $aiMeta = $lead->meta['ai'] ?? [];
                    @endphp
                    @if (! empty($aiMeta))
                        <x-ui.card class="dg-summary-card">
                            <h2 class="dg-title-sm">AI Processing</h2>
                            <p class="dg-muted-sm"><strong>Provider:</strong> {{ $aiMeta['provider'] ?? '-' }}</p>
                            <p class="dg-muted-sm"><strong>Fallback Used:</strong> {{ !empty($aiMeta['fallback_used']) ? 'Yes' : 'No' }}</p>
                            <p class="dg-muted-sm"><strong>Processed At:</strong> {{ $aiMeta['processed_at'] ?? '-' }}</p>
                            @if (! empty($aiMeta['extracted']) && is_array($aiMeta['extracted']))
                                <div class="dg-summary-list">
                                    @foreach ($aiMeta['extracted'] as $key => $value)
                                        <p><strong>{{ ucfirst((string) $key) }}:</strong> {{ is_scalar($value) ? $value : json_encode($value) }}</p>
                                    @endforeach
                                </div>
                            @endif
                        </x-ui.card>
                    @endif

                    @if ($lead->design_file_path)
                        <p class="dg-muted-sm">
                            <strong>Design File:</strong>
                            <a class="dg-link-primary" href="{{ asset('storage/'.$lead->design_file_path) }}" target="_blank" rel="noopener noreferrer">View Upload</a>
                        </p>
                    @endif

                    <x-ui.card class="dg-summary-card">
                        <h2 class="dg-title-sm">Customer Message</h2>
                        <p class="dg-section-copy">{{ $lead->message ?: 'No message submitted.' }}</p>
                    </x-ui.card>
                </x-ui.card>

                <x-ui.card class="dg-info-card">
                    <h2 class="dg-section-title">Update Lead Status</h2>
                    <p class="dg-section-copy">Move this lead through your sales pipeline stages.</p>
                    <form method="POST" action="{{ route('admin.leads.update-status', ['lead' => $lead->id]) }}" class="dg-config-form">
                        @csrf
                        @method('PATCH')
                        <div class="dg-field">
                            <label class="dg-label" for="status">Status</label>
                            <select id="status" name="status" class="dg-select" required>
                                @foreach ($statuses as $statusOption)
                                    <option value="{{ $statusOption }}" @selected($lead->status === $statusOption)>{{ $statusOption }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="dg-hero-actions">
                            <x-ui.button type="submit">Update Status</x-ui.button>
                        </div>
                    </form>
                </x-ui.card>
            </div>
        </section>
    </main>
</x-layouts.storefront>
