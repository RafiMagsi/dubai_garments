@props([
    'name',
    'description',
])

<x-ui.card class="dg-category-card">
    <h3 class="dg-title-sm">{{ $name }}</h3>
    <p class="dg-muted-sm">{{ $description }}</p>
    <div class="dg-card-links">
        <a href="#" class="dg-link-primary">Explore</a>
        <a href="#" class="dg-link-muted">Request Quote</a>
    </div>
</x-ui.card>
