@props(['cols' => 3])
@php
    $colClass = match ($cols) {
        1 => 'grid-cols-1',
        2 => 'grid-cols-1 lg:grid-cols-2',
        default => 'grid-cols-1 lg:grid-cols-3',
    };
@endphp
<div class="grid {{ $colClass }} gap-4">
    {{ $slot }}
</div>
