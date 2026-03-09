<x-layouts.admin title="Lead #{{ $lead->id }} | Admin">
    @php
        $aiMeta = $lead->meta['ai'] ?? [];
    @endphp

    <section class="dg-admin-page">
        <div class="dg-admin-page-head">
            <div>
                <h1 class="dg-page-title">Lead #{{ $lead->id }}</h1>
                <p class="dg-page-subtitle">Full context, AI assessment, and pipeline actions for this lead.</p>
            </div>
            <div class="dg-admin-toolbar">
                <x-ui.button variant="secondary" :href="route('admin.leads.index')">Back to Leads</x-ui.button>
                <x-ui.button variant="secondary" :href="route('admin.deals.index')">Pipeline</x-ui.button>
            </div>
        </div>

        @if (session('status'))
            <div class="dg-alert-success">{{ session('status') }}</div>
        @endif
    </section>

    <section class="dg-admin-page">
        <div class="dg-two-col-grid">
            <x-ui.card class="dg-panel">
                <h2 class="dg-title-sm">Lead Information</h2>
                <div class="dg-detail-list">
                    <div class="dg-detail-item"><span>Tracking Code</span><strong>{{ $lead->tracking_code ?: '-' }}</strong></div>
                    <div class="dg-detail-item"><span>Name</span><strong>{{ $lead->customer_name ?: '-' }}</strong></div>
                    <div class="dg-detail-item"><span>Company</span><strong>{{ $lead->company ?: '-' }}</strong></div>
                    <div class="dg-detail-item"><span>Email</span><strong>{{ $lead->email ?: '-' }}</strong></div>
                    <div class="dg-detail-item"><span>Phone</span><strong>{{ $lead->phone ?: '-' }}</strong></div>
                    <div class="dg-detail-item"><span>Product</span><strong>{{ $lead->product_type ?: '-' }}</strong></div>
                    <div class="dg-detail-item"><span>Quantity</span><strong>{{ $lead->quantity ? $lead->quantity.' pcs' : '-' }}</strong></div>
                    <div class="dg-detail-item"><span>Required Delivery</span><strong>{{ $lead->required_delivery_date?->format('M d, Y') ?: '-' }}</strong></div>
                    <div class="dg-detail-item"><span>Status</span><span class="dg-status-pill dg-status-pill-{{ $lead->status }}">{{ $lead->status }}</span></div>
                    <div class="dg-detail-item"><span>AI Score</span><strong>{{ $lead->ai_score ?? '-' }}</strong></div>
                    <div class="dg-detail-item"><span>Classification</span><strong>{{ $lead->classification ?? '-' }}</strong></div>
                </div>

                @if ($lead->design_file_path)
                    <div class="dg-summary-card">
                        <h3 class="dg-title-sm">Uploaded Design</h3>
                        <p class="dg-muted-sm">
                            <a class="dg-link-primary" href="{{ asset('storage/'.$lead->design_file_path) }}" target="_blank" rel="noopener noreferrer">Open uploaded file</a>
                        </p>
                    </div>
                @endif

                <x-ui.card class="dg-summary-card">
                    <h3 class="dg-title-sm">Customer Message</h3>
                    <p class="dg-section-copy">{{ $lead->message ?: 'No message submitted.' }}</p>
                </x-ui.card>
            </x-ui.card>

            <div class="dg-process-grid">
                <x-ui.card class="dg-panel">
                    <h2 class="dg-title-sm">Update Lead Status</h2>
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
                        <x-ui.button type="submit">Save Status</x-ui.button>
                    </form>
                </x-ui.card>

                <x-ui.card class="dg-panel">
                    <h2 class="dg-title-sm">Deal Link</h2>
                    @if ($lead->deal)
                        <div class="dg-detail-list">
                            <div class="dg-detail-item"><span>Deal ID</span><strong>#{{ $lead->deal->id }}</strong></div>
                            <div class="dg-detail-item"><span>Stage</span><strong>{{ $lead->deal->stage }}</strong></div>
                        </div>
                        <div class="dg-hero-actions">
                            <x-ui.button :href="route('admin.deals.show', ['deal' => $lead->deal->id])">Open Deal</x-ui.button>
                        </div>
                    @else
                        <p class="dg-muted-sm">No deal exists for this lead yet.</p>
                        <form method="POST" action="{{ route('admin.leads.create-deal', ['lead' => $lead->id]) }}" class="dg-config-form">
                            @csrf
                            <div class="dg-config-grid">
                                <div class="dg-field">
                                    <label class="dg-label" for="priority">Priority</label>
                                    <select id="priority" name="priority" class="dg-select">
                                        <option value="medium">medium</option>
                                        <option value="high">high</option>
                                        <option value="low">low</option>
                                    </select>
                                </div>
                                <div class="dg-field">
                                    <label class="dg-label" for="value_estimate">Value Estimate</label>
                                    <x-ui.input id="value_estimate" name="value_estimate" type="number" min="0" step="0.01" />
                                </div>
                            </div>
                            <div class="dg-field">
                                <label class="dg-label" for="notes">Notes</label>
                                <textarea id="notes" name="notes" class="dg-textarea" rows="3"></textarea>
                            </div>
                            <x-ui.button type="submit">Create Deal</x-ui.button>
                        </form>
                    @endif
                </x-ui.card>

                @if (! empty($aiMeta))
                    <x-ui.card class="dg-panel">
                        <h2 class="dg-title-sm">AI Processing</h2>
                        <div class="dg-detail-list">
                            <div class="dg-detail-item"><span>Provider</span><strong>{{ $aiMeta['provider'] ?? '-' }}</strong></div>
                            <div class="dg-detail-item"><span>Fallback Used</span><strong>{{ !empty($aiMeta['fallback_used']) ? 'Yes' : 'No' }}</strong></div>
                            <div class="dg-detail-item"><span>Processed At</span><strong>{{ $aiMeta['processed_at'] ?? '-' }}</strong></div>
                        </div>
                        @if (! empty($aiMeta['extracted']) && is_array($aiMeta['extracted']))
                            <div class="dg-pill-stack">
                                @foreach ($aiMeta['extracted'] as $key => $value)
                                    <span class="dg-status-pill">{{ ucfirst((string) $key) }}: {{ is_scalar($value) ? $value : json_encode($value) }}</span>
                                @endforeach
                            </div>
                        @endif
                    </x-ui.card>
                @endif
            </div>
        </div>
    </section>
</x-layouts.admin>
