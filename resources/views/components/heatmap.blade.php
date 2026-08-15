@props(['rows', 'cols', 'cells', 'suffix' => ''])
@php
    use App\Support\Colors;
    $values = [];
    foreach ($rows as $r) {
        foreach ($cols as $c) {
            $values[] = $cells[$r][$c] ?? 0;
        }
    }
    $min = $values ? min($values) : 0;
    $max = $values ? max($values) : 0;
@endphp
<div class="overflow-x-auto scrollbar-thin">
    <table class="border-collapse w-full min-w-[520px]">
        <thead>
            <tr>
                <th class="text-left text-[11px] font-medium text-[var(--text-muted)] pb-1 pr-2 sticky left-0 bg-[var(--surface-card)]"></th>
                @foreach ($cols as $c)
                    <th class="text-[11px] font-medium text-[var(--text-muted)] pb-1 px-1 text-center whitespace-nowrap">{{ $c }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $r)
                <tr>
                    <td class="text-[11px] text-[var(--text-secondary)] pr-2 py-0.5 whitespace-nowrap sticky left-0 bg-[var(--surface-card)]">{{ $r }}</td>
                    @foreach ($cols as $c)
                        @php
                            $v = $cells[$r][$c] ?? 0;
                            $bg = Colors::seqStep((float) $v, (float) $min, (float) $max);
                            $light = Colors::seqStepIsLight((float) $v, (float) $min, (float) $max);
                        @endphp
                        <td class="p-0.5">
                            <div class="h-7 min-w-[34px] rounded-sm flex items-center justify-center text-[10px] font-medium tabular"
                                 style="background: {{ $bg }}; color: {{ $light ? '#0b0b0b' : '#ffffff' }}"
                                 title="{{ $r }} · {{ $c }}: {{ $v }}{{ $suffix }}">
                                {{ $v }}{{ $suffix }}
                            </div>
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
