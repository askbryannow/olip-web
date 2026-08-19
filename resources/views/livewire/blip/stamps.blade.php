<div>
    <x-page-header title="Award Stamps" subtitle="One row per Traveller with an issued passport. Click an Embassy to award its stamp." />

    <div class="overflow-x-auto rounded-lg border border-[var(--border-hairline)] bg-[var(--surface-card)] scrollbar-thin">
        <table class="w-full min-w-[760px] border-collapse">
            <thead>
                <tr class="border-b border-[var(--border-hairline)]">
                    <th class="px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Traveller</th>
                    @foreach ($embassies as $embassy)
                        <th class="px-3 py-2 text-center text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">{{ $embassy->name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr class="border-b border-[var(--border-hairline)] last:border-0 hover:bg-[var(--surface-2)]">
                        <td class="px-3 py-2 text-[12px] text-[var(--text-primary)] whitespace-nowrap">
                            {{ $row['enrollment']->employee->name }}
                            <div class="text-[11px] text-[var(--text-muted)]">{{ $row['enrollment']->employee->outlet->name ?? $row['enrollment']->employee->outlet_id }}</div>
                        </td>
                        @foreach ($embassies as $embassy)
                            @php $stamp = $row['stampsByEmbassy']->get($embassy->id); @endphp
                            <td class="px-3 py-2 text-center">
                                @if ($stamp?->status === 'earned')
                                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-[var(--series-1)] text-[12px] font-bold text-white" title="Earned {{ $stamp->visit_date?->format('d M Y') }}">✓</span>
                                @else
                                    <button wire:click="openAward({{ $row['enrollment']->id }}, '{{ $embassy->id }}')"
                                            class="inline-flex h-7 w-7 items-center justify-center rounded-full border border-dashed border-[var(--border-hairline)] text-[11px] text-[var(--text-muted)] hover:border-[var(--series-1)] hover:text-[var(--series-1)]">
                                        +
                                    </button>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td colspan="{{ $embassies->count() + 1 }}" class="px-3 py-6 text-center text-[12px] text-[var(--text-muted)]">No Travellers with an issued passport yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($enrollmentId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 px-4">
            <form wire:submit="award" class="flex w-full max-w-md flex-col gap-4 rounded-lg border border-[var(--border-hairline)] bg-[var(--surface-card)] p-5">
                <h2 class="text-[14px] font-semibold text-[var(--text-primary)]">Award stamp</h2>
                <label class="flex flex-col gap-1">
                    <span class="text-[11px] font-medium text-[var(--text-secondary)]">Visit date</span>
                    <input type="date" wire:model="visitDate" class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px]">
                </label>
                <label class="flex flex-col gap-1">
                    <span class="text-[11px] font-medium text-[var(--text-secondary)]">Awarded by</span>
                    <input type="text" wire:model="awardedBy" placeholder="Business Ambassador" class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px]">
                </label>
                <label class="flex flex-col gap-1">
                    <span class="text-[11px] font-medium text-[var(--text-secondary)]">Notes (optional)</span>
                    <textarea wire:model="notes" rows="2" class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px]"></textarea>
                </label>
                <div class="flex justify-end gap-2">
                    <button type="button" wire:click="cancel" class="rounded-md px-3 py-1.5 text-[13px] text-[var(--text-secondary)] hover:bg-[var(--surface-2)]">Cancel</button>
                    <button type="submit" class="rounded-md bg-[var(--series-1)] px-3 py-1.5 text-[13px] font-medium text-white">Award stamp</button>
                </div>
            </form>
        </div>
    @endif
</div>
