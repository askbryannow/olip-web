<div class="mx-auto max-w-md">
    <x-portal-steps :steps="['Eligibility', 'About you', 'Review & submit']" :current="$step" />

    <div class="rounded-lg border border-[var(--border-hairline)] bg-[var(--surface-card)] p-6">
        @if ($step === 1)
            <div wire:key="step-1">
            <h1 class="text-[16px] font-semibold text-[var(--text-primary)]">Check you're eligible</h1>
            <p class="mt-1 text-[13px] text-[var(--text-muted)]">BLIP is open broadly — Ranger/crew level and above, at any eligible outstation location.</p>

            <ul class="mt-4 flex flex-col gap-2">
                <li class="flex items-center gap-2 text-[13px]">
                    <span class="{{ $this->isEligible() ? 'text-[var(--success-text)]' : 'text-[var(--status-critical)]' }}">{{ $this->isEligible() ? '✓' : '✕' }}</span>
                    <span class="text-[var(--text-secondary)]">At least {{ \App\Support\Options::BLIP_MIN_TENURE_MONTHS }} months in your current role — you have {{ $this->tenureMonths() }}.</span>
                </li>
                <li class="flex items-center gap-2 text-[13px]">
                    <span class="text-[var(--success-text)]">✓</span>
                    <span class="text-[var(--text-secondary)]">Ranger / crew level and above.</span>
                </li>
            </ul>

            <label class="mt-4 flex items-start gap-2 text-[13px] text-[var(--text-secondary)]">
                <input type="checkbox" wire:model="confirmConduct" class="mt-0.5">
                <span>I confirm I have no unresolved safety or conduct flags.</span>
            </label>

            @if ($error)
                <div class="mt-3 text-[12px] text-[var(--status-critical)]">{{ $error }}</div>
            @endif

            <button wire:click="next" class="mt-5 w-full rounded-md bg-[var(--series-1)] px-3 py-2 text-[13px] font-medium text-white">Continue</button>
            </div>
        @elseif ($step === 2)
            <div wire:key="step-2">
            <h1 class="text-[16px] font-semibold text-[var(--text-primary)]">Tell us about you</h1>
            <p class="mt-1 text-[13px] text-[var(--text-muted)]">One sentence is enough — your manager will discuss it with you.</p>

            <div class="mt-4 grid grid-cols-2 gap-3 text-[13px]">
                <div>
                    <div class="text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Name</div>
                    <div class="text-[var(--text-primary)]">{{ $employee->name }}</div>
                </div>
                <div>
                    <div class="text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Outlet</div>
                    <div class="text-[var(--text-primary)]">{{ $employee->outlet->name ?? $employee->outlet_id }}</div>
                </div>
                <div>
                    <div class="text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Role</div>
                    <div class="text-[var(--text-primary)]">{{ $employee->level }} · {{ $employee->department }}</div>
                </div>
                <div>
                    <div class="text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Tenure</div>
                    <div class="text-[var(--text-primary)]">{{ $this->tenureMonths() }} months</div>
                </div>
            </div>

            <label class="mt-4 flex flex-col gap-1">
                <span class="text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Why do you want to join BLIP?</span>
                <textarea wire:model="reason" rows="3" placeholder="One sentence is fine…"
                          class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-3 py-2 text-[13px]"></textarea>
            </label>

            @if ($error)
                <div class="mt-3 text-[12px] text-[var(--status-critical)]">{{ $error }}</div>
            @endif

            <div class="mt-5 flex gap-2">
                <button wire:click="back" class="rounded-md px-3 py-2 text-[13px] text-[var(--text-secondary)] hover:bg-[var(--surface-2)]">Back</button>
                <button wire:click="next" class="flex-1 rounded-md bg-[var(--series-1)] px-3 py-2 text-[13px] font-medium text-white">Continue</button>
            </div>
            </div>
        @else
            <div wire:key="step-3">
            <h1 class="text-[16px] font-semibold text-[var(--text-primary)]">Review your Interest Form</h1>

            <div class="mt-4 flex flex-col gap-3 rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] p-4 text-[13px]">
                <div><span class="text-[var(--text-muted)]">Name:</span> <span class="text-[var(--text-primary)]">{{ $employee->name }}</span></div>
                <div><span class="text-[var(--text-muted)]">Outlet:</span> <span class="text-[var(--text-primary)]">{{ $employee->outlet->name ?? $employee->outlet_id }}</span></div>
                <div><span class="text-[var(--text-muted)]">Tenure:</span> <span class="text-[var(--text-primary)]">{{ $this->tenureMonths() }} months</span></div>
                <div><span class="text-[var(--text-muted)]">Why:</span> <span class="text-[var(--text-primary)]">{{ $reason }}</span></div>
            </div>

            <p class="mt-4 text-[12px] text-[var(--text-muted)]">Submitting sends this to your outlet manager for endorsement. Completing BLIP does not guarantee promotion — it makes you a visible, credible candidate.</p>

            <div class="mt-5 flex gap-2">
                <button wire:click="back" class="rounded-md px-3 py-2 text-[13px] text-[var(--text-secondary)] hover:bg-[var(--surface-2)]">Back</button>
                <button wire:click="submit" class="flex-1 rounded-md bg-[var(--series-1)] px-3 py-2 text-[13px] font-medium text-white">Submit Interest Form</button>
            </div>
            </div>
        @endif
    </div>
</div>
