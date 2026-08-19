@props(['steps', 'current'])
<div class="mb-6 flex items-center">
    @foreach ($steps as $i => $label)
        @php $n = $i + 1; @endphp
        <div class="flex items-center {{ $loop->last ? '' : 'flex-1' }}">
            <div class="flex flex-col items-center gap-1.5">
                <div class="flex h-7 w-7 items-center justify-center rounded-full text-[12px] font-semibold
                    {{ $n < $current ? 'bg-[var(--series-1)] text-white' : ($n === $current ? 'bg-[var(--series-1)] text-white ring-4 ring-[color-mix(in_srgb,var(--series-1)_18%,transparent)]' : 'bg-[var(--surface-2)] text-[var(--text-muted)]') }}">
                    @if ($n < $current) &check; @else {{ $n }} @endif
                </div>
                <span class="w-max text-[11px] {{ $n <= $current ? 'font-medium text-[var(--text-primary)]' : 'text-[var(--text-muted)]' }}">{{ $label }}</span>
            </div>
            @unless ($loop->last)
                <div class="mx-2 h-0.5 flex-1 {{ $n < $current ? 'bg-[var(--series-1)]' : 'bg-[var(--surface-2)]' }}"></div>
            @endunless
        </div>
    @endforeach
</div>
