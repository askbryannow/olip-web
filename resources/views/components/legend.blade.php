@props(['items'])
<div class="flex flex-wrap items-center gap-x-3 gap-y-1">
    @foreach ($items as $it)
        <div class="flex items-center gap-1.5">
            <span class="inline-block h-2 w-2 rounded-full" style="background: {{ $it['color'] }}"></span>
            <span class="text-[11px] text-[var(--text-secondary)]">{{ $it['label'] }}</span>
        </div>
    @endforeach
</div>
