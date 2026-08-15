@props(['stages'])
@php
    $max = max(array_map(fn ($s) => $s['value'], $stages) ?: [1]) ?: 1;
@endphp
<div class="flex flex-col gap-2">
    @foreach ($stages as $i => $s)
        @php
            $pct = $max > 0 ? ($s['value'] / $max) * 100 : 0;
            $barPct = max($pct, 4);
            $labelOutside = $barPct < 12;
            $convFromPrev = $i > 0 && $stages[$i - 1]['value'] > 0 ? ($s['value'] / $stages[$i - 1]['value']) * 100 : null;
        @endphp
        <div class="flex items-center gap-3">
            <div class="w-28 shrink-0 text-[12px] text-[var(--text-secondary)]">{{ $s['label'] }}</div>
            <div class="flex-1 h-7 rounded bg-[var(--surface-2)] relative">
                <div class="h-full rounded flex items-center {{ $labelOutside ? '' : 'justify-end pr-2' }}"
                     style="width: {{ $barPct }}%; background: var(--series-1)">
                    @unless ($labelOutside)
                        <span class="text-[11px] font-medium text-white tabular">{{ number_format($s['value']) }}</span>
                    @endunless
                </div>
                @if ($labelOutside)
                    <span class="absolute top-1/2 -translate-y-1/2 text-[11px] font-medium text-[var(--text-primary)] tabular"
                          style="left: calc({{ $barPct }}% + 6px)">
                        {{ number_format($s['value']) }}
                    </span>
                @endif
            </div>
            <div class="w-14 shrink-0 text-[11px] text-[var(--text-muted)] tabular text-right">
                {{ $convFromPrev !== null ? round($convFromPrev).'%' : '' }}
            </div>
        </div>
    @endforeach
</div>
