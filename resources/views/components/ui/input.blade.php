@props([
    'type' => 'text',
    'name' => null,
    'value' => null,
])

<input
    type="{{ $type }}"
    @if ($name) name="{{ $name }}" @endif
    @if (! is_null($value) || $name) value="{{ old($name, $value) }}" @endif
    {{ $attributes->class(['dg-input']) }}
/>
