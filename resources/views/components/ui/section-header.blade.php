@props([
    'title',
    'subtitle' => null,
])

<div {{ $attributes->class(['dg-section-head']) }}>
    <div>
        <h2 class="dg-section-title">{{ $title }}</h2>
        @if ($subtitle)
            <p class="dg-section-copy">{{ $subtitle }}</p>
        @endif
    </div>

    @isset($action)
        <div>
            {{ $action }}
        </div>
    @endisset
</div>
