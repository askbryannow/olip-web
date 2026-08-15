@props(['title', 'subtitle' => null, 'legend' => null])
<div {{ $attributes->merge(['class' => 'flex flex-col gap-3 rounded-lg border border-[var(--border-hairline)] bg-[var(--surface-card)] p-4 min-w-0']) }}>
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <h3 class="text-[13px] font-semibold text-[var(--text-primary)] truncate">{{ $title }}</h3>
            @if ($subtitle)
                <p class="text-[12px] text-[var(--text-muted)] mt-0.5">{{ $subtitle }}</p>
            @endif
        </div>
        @if ($legend)
            <div class="shrink-0">{!! $legend !!}</div>
        @endif
    </div>
    <div class="min-w-0">{{ $slot }}</div>
</div>
