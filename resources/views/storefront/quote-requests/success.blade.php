<x-layouts.storefront title="Quote Request Submitted | Dubai Garments AI">
    <x-store.header :categories="$categories" search="" />

    <main class="dg-main">
        <section class="dg-section">
            <div class="dg-container">
                <x-ui.card class="dg-config-card">
                    <x-ui.badge>Request Submitted</x-ui.badge>
                    <h1 class="dg-title-lg">Your quote request has been submitted successfully.</h1>
                    <p class="dg-section-copy">Our team will contact you soon.</p>

                    <x-ui.card class="dg-summary-card">
                        <h2 class="dg-title-sm">Want to review other products?</h2>
                        <p class="dg-muted-sm">You can continue browsing the catalog while our sales team prepares your quotation.</p>
                        <div class="dg-hero-actions">
                            <x-ui.button :href="route('products.index')">Review Products</x-ui.button>
                            <x-ui.button variant="secondary" :href="route('storefront.home')">Back to Home</x-ui.button>
                        </div>
                    </x-ui.card>
                </x-ui.card>
            </div>
        </section>
    </main>

    <x-store.footer />
</x-layouts.storefront>
