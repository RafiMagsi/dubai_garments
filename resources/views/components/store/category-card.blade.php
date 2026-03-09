@props([
    'name',
    'description',
    'slug',
])

<x-ui.card class="dg-category-card">
    <h3 class="dg-title-sm">{{ $name }}</h3>
    <p class="dg-muted-sm">{{ $description }}</p>
    <div class="dg-card-links">
        <a href="{{ route('products.index', ['category' => $slug]) }}" class="dg-link-primary">Explore</a>
        <a href="{{ route('quote-requests.create') }}" class="dg-link-muted">Request Quote</a>
    </div>
</x-ui.card>
