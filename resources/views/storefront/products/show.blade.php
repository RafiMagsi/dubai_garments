<x-layouts.storefront title="{{ $product['name'] }} | Dubai Garments AI">
    <x-store.header :categories="$categories" search="" />

    <main class="dg-main">
        <section class="dg-section">
            <div class="dg-container dg-detail-grid">
                <x-ui.card class="dg-detail-media">
                    <div class="dg-product-image"></div>
                </x-ui.card>

                <x-ui.card class="dg-info-card">
                    <x-ui.badge>{{ $product['category'] }}</x-ui.badge>
                    <h1 class="dg-title-lg">{{ $product['name'] }}</h1>
                    <p class="dg-muted-sm">MOQ: {{ $product['moq'] }} pcs</p>
                    <p class="dg-muted-sm">Lead Time: {{ $product['lead_time'] }}</p>
                    <p class="dg-muted-sm">Fabric: {{ $product['fabric'] }}</p>
                    <p class="dg-muted-sm">Customization: {{ $product['customization'] }}</p>
                    <div class="dg-hero-actions">
                        <x-ui.button href="#">Request Quote</x-ui.button>
                        <x-ui.button variant="secondary" :href="route('products.index', ['category' => $product['category_slug']])">More {{ $product['category'] }}</x-ui.button>
                    </div>
                </x-ui.card>
            </div>
        </section>

        <section class="dg-section">
            <div class="dg-container">
                <x-ui.section-header
                    title="Related Products"
                    subtitle="Similar options available for your bulk order requirements."
                />
                <div class="dg-product-grid">
                    @foreach ($relatedProducts as $relatedProduct)
                        <x-store.product-card :product="$relatedProduct" />
                    @endforeach
                </div>
            </div>
        </section>
    </main>

    <x-store.footer />
</x-layouts.storefront>
