<div>
    <x-page-header title="BLIP Registrations" subtitle="Endorse Interest Forms, then confirm eligibility and place Travellers into a cohort." />

    <div class="overflow-x-auto rounded-lg border border-[var(--border-hairline)] bg-[var(--surface-card)] scrollbar-thin">
        <table class="w-full min-w-[760px] border-collapse">
            <thead>
                <tr class="border-b border-[var(--border-hairline)]">
                    <th class="px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Employee</th>
                    <th class="px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Outlet</th>
                    <th class="px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Reason</th>
                    <th class="px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Status</th>
                    <th class="px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Cohort</th>
                    <th class="px-3 py-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($enrollments as $enrollment)
                    <tr class="border-b border-[var(--border-hairline)] last:border-0 hover:bg-[var(--surface-2)]">
                        <td class="px-3 py-2 text-[12px] text-[var(--text-primary)] whitespace-nowrap">{{ $enrollment->employee->name }}</td>
                        <td class="px-3 py-2 text-[12px] text-[var(--text-secondary)] whitespace-nowrap">{{ $enrollment->employee->outlet->name ?? $enrollment->employee->outlet_id }}</td>
                        <td class="px-3 py-2 text-[12px] text-[var(--text-secondary)] max-w-[240px] truncate">{{ $enrollment->reason }}</td>
                        <td class="px-3 py-2 text-[12px] text-[var(--text-secondary)] whitespace-nowrap">{{ str($enrollment->status)->headline() }}</td>
                        <td class="px-3 py-2 text-[12px] text-[var(--text-secondary)] whitespace-nowrap">{{ $enrollment->cohort->name ?? '—' }}</td>
                        <td class="px-3 py-2 text-right whitespace-nowrap">
                            @if ($enrollment->status === 'interest_submitted')
                                <button wire:click="openDecision({{ $enrollment->id }}, 'endorsed')" class="text-[12px] font-medium text-[var(--series-1)] hover:underline mr-3">Endorse</button>
                                <button wire:click="openDecision({{ $enrollment->id }}, 'on_hold')" class="text-[12px] font-medium text-[var(--text-secondary)] hover:underline mr-3">Hold</button>
                                <button wire:click="openDecision({{ $enrollment->id }}, 'declined')" class="text-[12px] font-medium text-[var(--status-critical)] hover:underline">Decline</button>
                            @elseif ($enrollment->status === 'endorsed')
                                <button wire:click="openPlacement({{ $enrollment->id }})" class="text-[12px] font-medium text-[var(--series-1)] hover:underline">Confirm & place in cohort</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-3 py-6 text-center text-[12px] text-[var(--text-muted)]">No registrations yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($decidingId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 px-4">
            <form wire:submit="saveDecision" class="flex w-full max-w-md flex-col gap-4 rounded-lg border border-[var(--border-hairline)] bg-[var(--surface-card)] p-5">
                <h2 class="text-[14px] font-semibold text-[var(--text-primary)]">{{ str($decision)->headline() }}</h2>
                <label class="flex flex-col gap-1">
                    <span class="text-[11px] font-medium text-[var(--text-secondary)]">Note {{ $decision === 'endorsed' ? '(optional)' : '(reason — recorded, not silent)' }}</span>
                    <textarea wire:model="note" rows="3" class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px]"></textarea>
                </label>
                <div class="flex justify-end gap-2">
                    <button type="button" wire:click="cancel" class="rounded-md px-3 py-1.5 text-[13px] text-[var(--text-secondary)] hover:bg-[var(--surface-2)]">Cancel</button>
                    <button type="submit" class="rounded-md bg-[var(--series-1)] px-3 py-1.5 text-[13px] font-medium text-white">Save</button>
                </div>
            </form>
        </div>
    @endif

    @if ($placingId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 px-4">
            <form wire:submit="confirmPlacement" class="flex w-full max-w-md flex-col gap-4 rounded-lg border border-[var(--border-hairline)] bg-[var(--surface-card)] p-5">
                <h2 class="text-[14px] font-semibold text-[var(--text-primary)]">Confirm & place in cohort</h2>
                <label class="flex flex-col gap-1">
                    <span class="text-[11px] font-medium text-[var(--text-secondary)]">Cohort</span>
                    <select wire:model="cohortId" class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px]">
                        <option value="">Select cohort…</option>
                        @foreach ($cohorts as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->start_date->format('d M Y') }})</option>
                        @endforeach
                    </select>
                </label>
                @if ($error)
                    <div class="text-[12px] text-[var(--status-critical)]">{{ $error }}</div>
                @endif
                <div class="flex justify-end gap-2">
                    <button type="button" wire:click="cancel" class="rounded-md px-3 py-1.5 text-[13px] text-[var(--text-secondary)] hover:bg-[var(--surface-2)]">Cancel</button>
                    <button type="submit" class="rounded-md bg-[var(--series-1)] px-3 py-1.5 text-[13px] font-medium text-white">Confirm & issue passport</button>
                </div>
            </form>
        </div>
    @endif
</div>
