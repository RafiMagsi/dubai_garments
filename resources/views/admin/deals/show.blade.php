<x-layouts.storefront title="Deal #{{ $deal->id }} | Admin">
    <main class="dg-main">
        <section class="dg-section">
            <div class="dg-container dg-two-col-grid">
                <x-ui.card class="dg-info-card">
                    <div class="dg-admin-head">
                        <h1 class="dg-section-title">Deal Detail</h1>
                        <div class="dg-actions-wrap">
                            <x-ui.button variant="secondary" :href="route('admin.deals.index')">Back to Deals</x-ui.button>
                            <x-ui.button variant="secondary" :href="route('admin.leads.show', ['lead' => $deal->lead_id])">Lead</x-ui.button>
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <x-ui.button variant="secondary" type="submit">Logout</x-ui.button>
                            </form>
                        </div>
                    </div>

                    @if (session('status'))
                        <div class="dg-alert-success">{{ session('status') }}</div>
                    @endif

                    <p class="dg-muted-sm"><strong>Deal ID:</strong> #{{ $deal->id }}</p>
                    <p class="dg-muted-sm"><strong>Stage:</strong> <span class="dg-status-pill dg-status-pill-{{ $deal->stage }}">{{ $deal->stage }}</span></p>
                    <p class="dg-muted-sm"><strong>Priority:</strong> {{ $deal->priority }}</p>
                    <p class="dg-muted-sm"><strong>Value Estimate:</strong> {{ $deal->value_estimate ? '$'.number_format((float) $deal->value_estimate, 2) : '-' }}</p>
                    <p class="dg-muted-sm"><strong>Assigned User:</strong> {{ $deal->assignedUser?->name ?: '-' }}</p>
                    <p class="dg-muted-sm"><strong>Created:</strong> {{ $deal->created_at?->format('M d, Y H:i') ?: '-' }}</p>
                    <p class="dg-muted-sm"><strong>Updated:</strong> {{ $deal->updated_at?->format('M d, Y H:i') ?: '-' }}</p>

                    <x-ui.card class="dg-summary-card">
                        <h2 class="dg-title-sm">Lead Snapshot</h2>
                        <p class="dg-muted-sm"><strong>Tracking Code:</strong> {{ $deal->lead?->tracking_code ?: '-' }}</p>
                        <p class="dg-muted-sm"><strong>Customer:</strong> {{ $deal->lead?->customer_name ?: '-' }}</p>
                        <p class="dg-muted-sm"><strong>Company:</strong> {{ $deal->lead?->company ?: '-' }}</p>
                        <p class="dg-muted-sm"><strong>Product:</strong> {{ $deal->lead?->product_type ?: '-' }}</p>
                        <p class="dg-muted-sm"><strong>Quantity:</strong> {{ $deal->lead?->quantity ? $deal->lead?->quantity.' pcs' : '-' }}</p>
                    </x-ui.card>

                    <x-ui.card class="dg-summary-card">
                        <h2 class="dg-title-sm">Deal Notes</h2>
                        <p class="dg-section-copy">{{ $deal->notes ?: 'No notes available.' }}</p>
                    </x-ui.card>
                </x-ui.card>

                <x-ui.card class="dg-info-card">
                    <h2 class="dg-section-title">Update Deal</h2>
                    <p class="dg-section-copy">Move stage, update priority/value, and assign owner.</p>
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

                        <div class="dg-hero-actions">
                            <x-ui.button type="submit">Save Deal</x-ui.button>
                        </div>
                    </form>
                </x-ui.card>
            </div>
        </section>
    </main>
</x-layouts.storefront>
