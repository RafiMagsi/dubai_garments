<x-layouts.storefront title="Product Catalog | Dubai Garments AI">
    <x-store.header :categories="$categories" :search="$search" />

    <main class="dg-main">
        <section class="dg-section">
            <div class="dg-container">
                <x-ui.section-header
                    title="Product Catalog"
                    subtitle="Browse garments by category and request bulk quotations with customization details."
                >
                    <x-slot:action>
                        <x-ui.badge>{{ count($products) }} Products</x-ui.badge>
                    </x-slot:action>
                </x-ui.section-header>

                <x-ui.card class="dg-filter-card">
                    <div class="dg-filter-row">
                        <a href="{{ route('products.index') }}" class="{{ $activeCategory === '' ? 'dg-chip dg-chip-active' : 'dg-chip' }}">All</a>
                        @foreach ($categories as $category)
                            <a
                                href="{{ route('products.index', ['category' => $category['slug'], 'search' => $search ?: null]) }}"
                                class="{{ $activeCategory === $category['slug'] ? 'dg-chip dg-chip-active' : 'dg-chip' }}"
                            >
                                {{ $category['name'] }}
                            </a>
                        @endforeach
                    </div>
                </x-ui.card>

                <div class="dg-product-grid">
                    @forelse ($products as $product)
                        <x-store.product-card :product="$product" />
                    @empty
                        <x-ui.card class="dg-info-card">
                            <h3 class="dg-title-sm">No products found</h3>
                            <p class="dg-muted-sm">Try another search term or clear the category filter.</p>
                            <div class="dg-hero-actions">
                                <x-ui.button :href="route('products.index')">Clear Filters</x-ui.button>
                            </div>
                        </x-ui.card>
                    @endforelse
                </div>
            </div>
        </section>
    </main>

    <x-store.footer />
</x-layouts.storefront>
