@props([
    'product',
])

<article class="dg-product-card">
    <div class="dg-product-image dg-quick-card">
        <span class="dg-product-tag">{{ $product['category'] }}</span>
    </div>
    <div class="dg-product-body">
        <h3 class="dg-product-name">{{ $product['name'] }}</h3>
        <p class="dg-product-meta">MOQ: {{ $product['moq'] }} pcs</p>
        <p class="dg-product-meta">Lead Time: {{ $product['lead_time'] }}</p>
        <p class="dg-product-meta">Fabric: {{ $product['fabric'] }}</p>
        <p class="dg-product-meta">Customization: {{ $product['customization'] }}</p>
        <div class="dg-product-actions">
            <x-ui.button variant="secondary" :href="route('products.show', ['slug' => $product['slug']])" class="dg-col-fill">Details</x-ui.button>
            <x-ui.button href="#" class="dg-col-fill">Quote</x-ui.button>
        </div>
    </div>
</article>
