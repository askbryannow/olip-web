<div class="flex flex-wrap items-end gap-3 border-b border-[var(--border-hairline)] bg-[var(--surface-1)] px-5 py-3 sticky top-0 z-20">
    <label class="flex flex-col gap-1 min-w-0">
        <span class="text-[10px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Month (as of)</span>
        <select wire:model.live="monthKey" class="text-[12px] rounded-md border border-[var(--border-hairline)] bg-[var(--surface-card)] px-2 py-1.5 text-[var(--text-primary)] min-w-0 max-w-[180px] truncate focus:outline-none focus:ring-2 focus:ring-[var(--series-1)]">
            <option value="ALL">Latest</option>
            @foreach ($months as $m)
                <option value="{{ $m->key }}">{{ $m->label }}</option>
            @endforeach
        </select>
    </label>

    <label class="flex flex-col gap-1 min-w-0">
        <span class="text-[10px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Business Unit</span>
        <select wire:model.live="bu" class="text-[12px] rounded-md border border-[var(--border-hairline)] bg-[var(--surface-card)] px-2 py-1.5 text-[var(--text-primary)] min-w-0 max-w-[180px] truncate focus:outline-none focus:ring-2 focus:ring-[var(--series-1)]">
            <option value="ALL">All BUs</option>
            @foreach ($businessUnits as $b)
                <option value="{{ $b }}">{{ $b }}</option>
            @endforeach
        </select>
    </label>

    <label class="flex flex-col gap-1 min-w-0">
        <span class="text-[10px] font-medium uppercase tracking-wide text-[var(--text-muted)]">State</span>
        <select wire:model.live="state" class="text-[12px] rounded-md border border-[var(--border-hairline)] bg-[var(--surface-card)] px-2 py-1.5 text-[var(--text-primary)] min-w-0 max-w-[180px] truncate focus:outline-none focus:ring-2 focus:ring-[var(--series-1)]">
            <option value="ALL">All States</option>
            @foreach ($states as $s)
                <option value="{{ $s }}">{{ $s }}</option>
            @endforeach
        </select>
    </label>

    <label class="flex flex-col gap-1 min-w-0">
        <span class="text-[10px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Outlet</span>
        <select wire:model.live="outletId" class="text-[12px] rounded-md border border-[var(--border-hairline)] bg-[var(--surface-card)] px-2 py-1.5 text-[var(--text-primary)] min-w-0 max-w-[180px] truncate focus:outline-none focus:ring-2 focus:ring-[var(--series-1)]">
            <option value="ALL">All Outlets</option>
            @foreach ($outlets as $o)
                <option value="{{ $o->id }}">{{ $o->name }}</option>
            @endforeach
        </select>
    </label>

    <label class="flex flex-col gap-1 min-w-0">
        <span class="text-[10px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Employment Type</span>
        <select wire:model.live="employmentType" class="text-[12px] rounded-md border border-[var(--border-hairline)] bg-[var(--surface-card)] px-2 py-1.5 text-[var(--text-primary)] min-w-0 max-w-[180px] truncate focus:outline-none focus:ring-2 focus:ring-[var(--series-1)]">
            <option value="ALL">All Types</option>
            @foreach ($employmentTypes as $t)
                <option value="{{ $t }}">{{ $t }}</option>
            @endforeach
        </select>
    </label>

    @if ($this->isFiltered())
        <button wire:click="clear" class="text-[12px] font-medium text-[var(--series-1)] hover:underline ml-1 mb-1.5">Clear filters</button>
    @endif
</div>
