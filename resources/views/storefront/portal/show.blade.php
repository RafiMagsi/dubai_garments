<x-layouts.storefront title="Request {{ $lead->tracking_code }} | Customer Portal">
    <x-store.header :categories="$categories" search="" />

    <main class="dg-main">
        <section class="dg-section">
            <div class="dg-container dg-two-col-grid">
                <x-ui.card class="dg-info-card">
                    <x-ui.badge>Tracking Code: {{ $lead->tracking_code }}</x-ui.badge>
                    <h1 class="dg-title-lg">{{ $lead->product_type ?? 'Bulk Quote Request' }}</h1>
                    <p class="dg-muted-sm">Status: {{ $lead->status }}</p>
                    <p class="dg-muted-sm">Requested by: {{ $lead->customer_name }}</p>
                    <p class="dg-muted-sm">Email: {{ $lead->email }}</p>
                    @if ($lead->company)
                        <p class="dg-muted-sm">Company: {{ $lead->company }}</p>
                    @endif
                    @if ($lead->quantity)
                        <p class="dg-muted-sm">Quantity: {{ $lead->quantity }} pcs</p>
                    @endif
                    @if ($lead->required_delivery_date)
                        <p class="dg-muted-sm">Required delivery: {{ $lead->required_delivery_date->format('M d, Y') }}</p>
                    @endif
                    @if ($lead->design_file_path)
                        <p class="dg-muted-sm">
                            Design file:
                            <a class="dg-link-primary" href="{{ asset('storage/'.$lead->design_file_path) }}" target="_blank" rel="noopener noreferrer">
                                View Upload
                            </a>
                        </p>
                    @endif
                </x-ui.card>

                <x-ui.card class="dg-info-card">
                    <h2 class="dg-section-title">Progress</h2>
                    <div class="dg-status-list">
                        @foreach ($statusSteps as $step)
                            <div class="dg-status-item dg-status-{{ $step['state'] }}">
                                <span class="dg-status-dot"></span>
                                <span>{{ $step['name'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </x-ui.card>
            </div>
        </section>

        <section class="dg-section">
            <div class="dg-container">
                <x-ui.card class="dg-info-card">
                    <h2 class="dg-section-title">Submitted Requirements</h2>
                    <p class="dg-section-copy">{{ $lead->message ?: 'No additional message provided.' }}</p>
                    <div class="dg-hero-actions">
                        <x-ui.button variant="secondary" :href="route('products.index')">Review Other Products</x-ui.button>
                        <x-ui.button :href="route('quote-requests.create')">Submit Another Request</x-ui.button>
                    </div>
                </x-ui.card>
            </div>
        </section>
    </main>

    <x-store.footer />
</x-layouts.storefront>
