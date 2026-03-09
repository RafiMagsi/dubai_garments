<x-layouts.storefront title="Customer Portal | Dubai Garments AI">
    <x-store.header :categories="$categories" search="" />

    <main class="dg-main">
        <section class="dg-section">
            <div class="dg-container">
                <x-ui.card class="dg-config-card">
                    <h1 class="dg-section-title">Customer Portal</h1>
                    <p class="dg-section-copy">
                        Enter your email and tracking code to view request progress, quote status, and communication history.
                    </p>

                    @if ($errors->any())
                        <div class="dg-alert-error">
                            <ul class="dg-error-list">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('portal.lookup') }}" method="POST" class="dg-config-form">
                        @csrf
                        <div class="dg-config-grid">
                            <div class="dg-field">
                                <label class="dg-label" for="email">Email</label>
                                <x-ui.input id="email" type="email" name="email" :value="old('email', $prefillEmail)" required />
                            </div>
                            <div class="dg-field">
                                <label class="dg-label" for="tracking_code">Tracking Code</label>
                                <x-ui.input id="tracking_code" name="tracking_code" :value="old('tracking_code', $prefillCode)" required />
                            </div>
                        </div>
                        <div class="dg-hero-actions">
                            <x-ui.button type="submit">View My Request</x-ui.button>
                            <x-ui.button variant="secondary" :href="route('quote-requests.create')">Create New Request</x-ui.button>
                        </div>
                    </form>
                </x-ui.card>
            </div>
        </section>
    </main>

    <x-store.footer />
</x-layouts.storefront>
