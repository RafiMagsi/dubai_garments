@props([
    'variant' => 'primary',
    'href' => null,
    'type' => 'button',
])

@php
    $themeClass = $variant === 'secondary' ? 'dg-btn-secondary' : 'dg-btn-primary';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class([$themeClass]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class([$themeClass]) }}>
        {{ $slot }}
    </button>
@endif
