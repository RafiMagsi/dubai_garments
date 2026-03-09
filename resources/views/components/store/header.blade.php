@props([
    'categories' => [],
    'search' => '',
])

<div class="dg-topbar">
    <div class="dg-container dg-topbar-inner">
        <p>Bulk Orders Support: +92 300 0000000</p>
        <p>Email: sales@dubaigarments.ai | Delivery: UAE, KSA, Pakistan</p>
    </div>
</div>

<header class="dg-header">
    <div class="dg-container">
        <x-ui.card class="dg-header-inner">
            <div>
                <p class="dg-brand-subtitle">Dubai Garments</p>
                <a href="{{ route('storefront.home') }}" class="dg-brand-title">Bulk Garment Store</a>
            </div>

            <form action="{{ route('products.index') }}" method="GET" class="dg-search-wrap">
                <x-ui.input type="search" name="search" :value="$search" placeholder="Search products, categories, fabrics..." />
            </form>

            <div class="dg-header-actions">
                <x-ui.button variant="secondary" :href="route('portal.index')">Customer Portal</x-ui.button>
                <x-ui.button :href="route('quote-requests.create')">Request Bulk Quote</x-ui.button>
            </div>
        </x-ui.card>

        <nav class="dg-card dg-nav">
            <div class="dg-nav-inner">
                @foreach ($categories as $category)
                    <a href="{{ route('products.index', ['category' => $category['slug']]) }}" class="dg-nav-link">{{ $category['name'] }}</a>
                @endforeach
                <a href="{{ route('products.index') }}" class="dg-nav-link">All Products</a>
                <a href="#" class="dg-nav-link">Contact</a>
            </div>
        </nav>
    </div>
</header>
