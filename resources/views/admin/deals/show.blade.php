<x-layouts.admin title="Deal #{{ $deal->id }} | Admin">
    <section class="dg-admin-page">
        <div class="dg-admin-page-head">
            <div>
                <h1 class="dg-page-title">Deal #{{ $deal->id }}</h1>
                <p class="dg-page-subtitle">Update stage, ownership, value, and generate related quotes.</p>
            </div>
            <div class="dg-admin-toolbar">
                <x-ui.button variant="secondary" :href="route('admin.deals.index')">Back to Deals</x-ui.button>
                <x-ui.button variant="secondary" :href="route('admin.quotes.index')">Quotes</x-ui.button>
                <x-ui.button variant="secondary" :href="route('admin.leads.show', ['lead' => $deal->lead_id])">Lead</x-ui.button>
            </div>
        </div>

        @if (session('status'))
            <div class="dg-alert-success">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="dg-alert-error">
                <ul class="dg-error-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </section>

    <section class="dg-admin-page">
        <div class="dg-two-col-grid">
            <x-ui.card class="dg-panel">
                <h2 class="dg-title-sm">Deal Snapshot</h2>
                <div class="dg-detail-list">
                    <div class="dg-detail-item"><span>Stage</span><span class="dg-status-pill dg-status-pill-{{ $deal->stage }}">{{ $deal->stage }}</span></div>
                    <div class="dg-detail-item"><span>Priority</span><strong>{{ $deal->priority }}</strong></div>
                    <div class="dg-detail-item"><span>Value Estimate</span><strong>{{ $deal->value_estimate ? '$'.number_format((float) $deal->value_estimate, 2) : '-' }}</strong></div>
                    <div class="dg-detail-item"><span>Assigned User</span><strong>{{ $deal->assignedUser?->name ?: '-' }}</strong></div>
                    <div class="dg-detail-item"><span>Created</span><strong>{{ $deal->created_at?->format('M d, Y H:i') ?: '-' }}</strong></div>
                    <div class="dg-detail-item"><span>Updated</span><strong>{{ $deal->updated_at?->format('M d, Y H:i') ?: '-' }}</strong></div>
                </div>

                <x-ui.card class="dg-summary-card">
                    <h3 class="dg-title-sm">Lead Context</h3>
                    <div class="dg-detail-list">
                        <div class="dg-detail-item"><span>Tracking Code</span><strong>{{ $deal->lead?->tracking_code ?: '-' }}</strong></div>
                        <div class="dg-detail-item"><span>Customer</span><strong>{{ $deal->lead?->customer_name ?: '-' }}</strong></div>
                        <div class="dg-detail-item"><span>Company</span><strong>{{ $deal->lead?->company ?: '-' }}</strong></div>
                        <div class="dg-detail-item"><span>Product</span><strong>{{ $deal->lead?->product_type ?: '-' }}</strong></div>
                        <div class="dg-detail-item"><span>Quantity</span><strong>{{ $deal->lead?->quantity ? $deal->lead?->quantity.' pcs' : '-' }}</strong></div>
                    </div>
                </x-ui.card>

                <x-ui.card class="dg-summary-card">
                    <h3 class="dg-title-sm">Deal Notes</h3>
                    <p class="dg-section-copy">{{ $deal->notes ?: 'No notes available.' }}</p>
                </x-ui.card>

                <x-ui.card class="dg-summary-card">
                    <h3 class="dg-title-sm">Related Quotes</h3>
                    @if ($deal->quotes->isNotEmpty())
                        <div class="dg-list">
                            @foreach ($deal->quotes as $quoteItem)
                                <div class="dg-list-row">
                                    <div class="dg-list-main">
                                        <p class="dg-list-title">{{ $quoteItem->quote_number }}</p>
                                        <p class="dg-list-meta">{{ $quoteItem->status }} • {{ $quoteItem->currency }} {{ number_format((float) $quoteItem->total_price, 2) }}</p>
                                    </div>
                                    <x-ui.button variant="secondary" :href="route('admin.quotes.show', ['quote' => $quoteItem->id])">Open</x-ui.button>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="dg-muted-sm">No quotes created yet.</p>
                    @endif
                </x-ui.card>
            </x-ui.card>

            <div class="dg-side-stack">
                <x-ui.card class="dg-panel">
                    <h2 class="dg-title-sm">Update Deal</h2>
                    <form method="POST" action="{{ route('admin.deals.update', ['deal' => $deal->id]) }}" class="dg-config-form">
                        @csrf
                        @method('PATCH')

                        <div class="dg-config-grid">
                            <div class="dg-field">
                                <label class="dg-label" for="stage">Stage</label>
                                <select id="stage" name="stage" class="dg-select" required>
                                    @foreach ($stages as $stageOption)
                                        <option value="{{ $stageOption }}" @selected($deal->stage === $stageOption)>{{ $stageOption }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="dg-field">
                                <label class="dg-label" for="priority">Priority</label>
                                <select id="priority" name="priority" class="dg-select" required>
                                    @foreach ($priorities as $priorityOption)
                                        <option value="{{ $priorityOption }}" @selected($deal->priority === $priorityOption)>{{ $priorityOption }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="dg-field">
                                <label class="dg-label" for="value_estimate">Value Estimate</label>
                                <x-ui.input id="value_estimate" name="value_estimate" type="number" step="0.01" min="0" :value="$deal->value_estimate" />
                            </div>

                            <div class="dg-field">
                                <label class="dg-label" for="assigned_user_id">Assign User</label>
                                <select id="assigned_user_id" name="assigned_user_id" class="dg-select">
                                    <option value="">Unassigned</option>
                                    @foreach ($assignableUsers as $user)
                                        <option value="{{ $user->id }}" @selected((int) $deal->assigned_user_id === (int) $user->id)>
                                            {{ $user->name }} ({{ $user->role }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="dg-field">
                            <label class="dg-label" for="notes">Notes</label>
                            <textarea id="notes" name="notes" class="dg-textarea" rows="5">{{ $deal->notes }}</textarea>
                        </div>

                        <x-ui.button type="submit">Save Deal</x-ui.button>
                    </form>
                </x-ui.card>

                <x-ui.card class="dg-panel">
                    <h2 class="dg-title-sm">Create Quote</h2>
                    <p class="dg-muted-sm">Use one line per item: Item Name, Quantity, Unit Price</p>
                    <form method="POST" action="{{ route('admin.deals.create-quote', ['deal' => $deal->id]) }}" class="dg-config-form">
                        @csrf
                        <div class="dg-config-grid">
                            <div class="dg-field">
                                <label class="dg-label" for="currency">Currency</label>
                                <x-ui.input id="currency" name="currency" value="AED" required />
                            </div>
                            <div class="dg-field">
                                <label class="dg-label" for="discount">Discount</label>
                                <x-ui.input id="discount" name="discount" type="number" step="0.01" min="0" value="0" />
                            </div>
                            <div class="dg-field">
                                <label class="dg-label" for="expires_at">Expires At</label>
                                <x-ui.input id="expires_at" name="expires_at" type="date" />
                            </div>
                        </div>
                        <div class="dg-field">
                            <label class="dg-label" for="items_text">Items</label>
                            <textarea id="items_text" name="items_text" class="dg-textarea" rows="6" required>{{ $deal->lead?->product_type ?: 'Product' }}, {{ $deal->lead?->quantity ?: 1 }}, 0</textarea>
                        </div>
                        <div class="dg-field">
                            <label class="dg-label" for="quote_notes">Notes</label>
                            <textarea id="quote_notes" name="notes" class="dg-textarea" rows="3"></textarea>
                        </div>
                        <x-ui.button type="submit">Create Quote</x-ui.button>
                    </form>
                </x-ui.card>
            </div>
        </div>
    </section>
</x-layouts.admin>
