@props([
    'quote',
    'name',
    'role',
])

<blockquote class="dg-testimonial">
    “{{ $quote }}”
    <footer class="dg-testimonial-meta">{{ $name }} · {{ $role }}</footer>
</blockquote>
