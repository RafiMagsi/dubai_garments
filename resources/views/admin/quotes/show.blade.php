<x-layouts.admin title="Quote {{ $quote->quote_number }} | Admin">
    <section class="dg-admin-page">
        <div class="dg-admin-page-head">
            <div>
                <h1 class="dg-page-title">Quote {{ $quote->quote_number }}</h1>
                <p class="dg-page-subtitle">Update quote pricing, validity, status, and line items.</p>
            </div>
            <div class="dg-admin-toolbar">
                <x-ui.button variant="secondary" :href="route('admin.quotes.index')">Back to Quotes</x-ui.button>
                <x-ui.button variant="secondary" :href="route('admin.deals.show', ['deal' => $quote->deal_id])">Deal</x-ui.button>
                <x-ui.button variant="secondary" :href="route('admin.quotes.pdf', ['quote' => $quote->id])">Download PDF</x-ui.button>
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
                <h2 class="dg-title-sm">Quote Summary</h2>
                <div class="dg-detail-list">
                    <div class="dg-detail-item"><span>Deal</span><strong>#{{ $quote->deal_id }}</strong></div>
                    <div class="dg-detail-item"><span>Status</span><span class="dg-status-pill">{{ $quote->status }}</span></div>
                    <div class="dg-detail-item"><span>Currency</span><strong>{{ $quote->currency }}</strong></div>
                    <div class="dg-detail-item"><span>Subtotal</span><strong>{{ $quote->currency }} {{ number_format((float) $quote->subtotal, 2) }}</strong></div>
                    <div class="dg-detail-item"><span>Discount</span><strong>{{ $quote->currency }} {{ number_format((float) $quote->discount, 2) }}</strong></div>
                    <div class="dg-detail-item"><span>Total</span><strong>{{ $quote->currency }} {{ number_format((float) $quote->total_price, 2) }}</strong></div>
                    <div class="dg-detail-item"><span>Expires</span><strong>{{ $quote->expires_at?->format('M d, Y') ?: '-' }}</strong></div>
                    <div class="dg-detail-item"><span>Sent At</span><strong>{{ $quote->sent_at?->format('M d, Y H:i') ?: '-' }}</strong></div>
                </div>

                <x-ui.card class="dg-summary-card">
                    <h3 class="dg-title-sm">Customer Context</h3>
                    <div class="dg-detail-list">
                        <div class="dg-detail-item"><span>Customer</span><strong>{{ $quote->deal?->lead?->customer_name ?: '-' }}</strong></div>
                        <div class="dg-detail-item"><span>Company</span><strong>{{ $quote->deal?->lead?->company ?: '-' }}</strong></div>
                        <div class="dg-detail-item"><span>Tracking Code</span><strong>{{ $quote->deal?->lead?->tracking_code ?: '-' }}</strong></div>
                    </div>
                </x-ui.card>
            </x-ui.card>

            <x-ui.card class="dg-panel">
                <h2 class="dg-title-sm">Edit Quote</h2>
                <p class="dg-muted-sm">Use one line per item: `Item Name, Quantity, Unit Price`</p>

                <form method="POST" action="{{ route('admin.quotes.update', ['quote' => $quote->id]) }}" class="dg-config-form">
                    @csrf
                    @method('PATCH')

                    <div class="dg-config-grid">
                        <div class="dg-field">
                            <label class="dg-label" for="status">Status</label>
                            <select id="status" name="status" class="dg-select" required>
                                @foreach ($statuses as $statusOption)
                                    <option value="{{ $statusOption }}" @selected($quote->status === $statusOption)>{{ $statusOption }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="dg-field">
                            <label class="dg-label" for="currency">Currency</label>
                            <x-ui.input id="currency" name="currency" :value="$quote->currency" required />
                        </div>

                        <div class="dg-field">
                            <label class="dg-label" for="discount">Discount</label>
                            <x-ui.input id="discount" name="discount" type="number" step="0.01" min="0" :value="$quote->discount" />
                        </div>

                        <div class="dg-field">
                            <label class="dg-label" for="expires_at">Expires At</label>
                            <x-ui.input id="expires_at" name="expires_at" type="date" :value="$quote->expires_at?->toDateString()" />
                        </div>
                    </div>

                    <div class="dg-field">
                        <label class="dg-label" for="items_text">Quote Items</label>
                        <textarea id="items_text" name="items_text" class="dg-textarea" rows="8" required>{{ old('items_text', $itemsText) }}</textarea>
                    </div>

                    <div class="dg-field">
                        <label class="dg-label" for="notes">Notes</label>
                        <textarea id="notes" name="notes" class="dg-textarea" rows="4">{{ old('notes', $quote->notes) }}</textarea>
                    </div>

                    <x-ui.button type="submit">Save Quote</x-ui.button>
                </form>

                <x-ui.card class="dg-summary-card">
                    <h3 class="dg-title-sm">Email Communication</h3>
                    <form method="POST" action="{{ route('admin.quotes.send-email', ['quote' => $quote->id]) }}" class="dg-config-form">
                        @csrf
                        <div class="dg-field">
                            <label class="dg-label" for="recipient_email">Recipient Email</label>
                            <x-ui.input id="recipient_email" name="recipient_email" type="email" :value="$quote->deal?->lead?->email" required />
                        </div>
                        <div class="dg-field">
                            <label class="dg-label" for="subject">Subject</label>
                            <x-ui.input id="subject" name="subject" :value="'Quote '.$quote->quote_number.' from Dubai Garments'" required />
                        </div>
                        <div class="dg-field">
                            <label class="dg-label" for="message">Message</label>
                            <textarea id="message" name="message" class="dg-textarea" rows="5" required>Hello {{ $quote->deal?->lead?->customer_name ?: 'Customer' }},{{ "\n\n" }}Please find your quote {{ $quote->quote_number }} details in your customer portal. Let us know if you need revisions.{{ "\n\n" }}Regards,{{ "\n" }}Dubai Garments Sales Team</textarea>
                        </div>
                        <x-ui.button type="submit">Send Email</x-ui.button>
                    </form>
                </x-ui.card>

                <x-ui.card class="dg-summary-card">
                    <h3 class="dg-title-sm">Recent Communications</h3>
                    @if ($communications->isNotEmpty())
                        <div class="dg-list">
                            @foreach ($communications as $communication)
                                <div class="dg-list-row">
                                    <div class="dg-list-main">
                                        <p class="dg-list-title">{{ $communication->subject }}</p>
                                        <p class="dg-list-meta">{{ $communication->recipient_email }} • {{ strtoupper($communication->status) }} • {{ $communication->created_at?->format('M d, Y H:i') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="dg-muted-sm">No communication logs yet.</p>
                    @endif
                </x-ui.card>

                <x-ui.card class="dg-summary-card">
                    <h3 class="dg-title-sm">Follow-up Automation Queue</h3>
                    @if ($followups->isNotEmpty())
                        <div class="dg-list">
                            @foreach ($followups as $followup)
                                <div class="dg-list-row">
                                    <div class="dg-list-main">
                                        <p class="dg-list-title">{{ $followup->step ?: 'follow-up' }}</p>
                                        <p class="dg-list-meta">
                                            {{ strtoupper($followup->status) }} •
                                            {{ $followup->next_run?->format('M d, Y H:i') ?: '-' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="dg-muted-sm">No follow-ups scheduled yet. Set quote status to SENT to schedule sequence.</p>
                    @endif
                </x-ui.card>
            </x-ui.card>
        </div>
    </section>
</x-layouts.admin>
