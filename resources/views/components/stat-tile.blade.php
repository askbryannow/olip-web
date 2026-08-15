@props(['label', 'value', 'delta' => null, 'deltaGood' => null, 'sublabel' => null])
<div class="flex flex-col gap-1.5 rounded-lg border border-[var(--border-hairline)] bg-[var(--surface-card)] px-4 py-3.5 min-w-0">
    <div class="text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)] truncate">{{ $label }}</div>
    <div class="text-[26px] leading-none font-semibold text-[var(--text-primary)] tabular">{{ $value }}</div>
    <div class="flex items-center gap-1.5 min-h-[16px]">
        @if ($delta)
            <span class="text-[12px] font-medium tabular"
                  style="color: {{ $deltaGood === null ? 'var(--text-secondary)' : ($deltaGood ? 'var(--success-text)' : 'var(--status-critical)') }}">
                {{ $delta }}
            </span>
        @endif
        @if ($sublabel)
            <span class="text-[12px] text-[var(--text-muted)] truncate">{{ $sublabel }}</span>
        @endif
    </div>
</div>
