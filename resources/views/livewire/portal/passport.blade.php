@php $seriesColor = fn (int $i) => "var(--series-{$i})"; @endphp

<div>
    <div class="mb-6">
        <h1 class="text-[19px] font-semibold text-[var(--text-primary)]">My Leadership Passport</h1>
        <p class="mt-0.5 text-[13px] text-[var(--text-muted)]">{{ $employee->name }} · {{ $employee->outlet->name ?? $employee->outlet_id }}</p>
    </div>

    @if (! $enrollment)
        <div class="rounded-lg border border-[var(--border-hairline)] bg-[var(--surface-card)] p-6 text-center">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-[color-mix(in_srgb,var(--series-1)_14%,transparent)] text-[20px]">🎟️</div>
            <h2 class="mt-3 text-[15px] font-semibold text-[var(--text-primary)]">You're not registered for BLIP yet</h2>
            <p class="mx-auto mt-1 max-w-sm text-[13px] text-[var(--text-muted)]">
                A two-day visit to each of five OWG businesses. Complete all five and your Leadership Passport is endorsed — and you're on the pipeline for the next promotion.
            </p>
            <a href="{{ route('portal.enroll') }}" class="mt-4 inline-block rounded-md bg-[var(--series-1)] px-4 py-2 text-[13px] font-medium text-white">Register your interest</a>
        </div>
    @else
        <div class="grid grid-cols-2 gap-3 mb-6 sm:grid-cols-4">
            <x-stat-tile label="Stamps collected" :value="$earnedCount.' / 5'" />
            <div class="flex flex-col gap-1.5 rounded-lg border border-[var(--border-hairline)] bg-[var(--surface-card)] px-4 py-3.5 min-w-0">
                <div class="text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)] truncate">Status</div>
                <div class="text-[16px] leading-tight font-semibold text-[var(--text-primary)]">{{ $stage }}</div>
            </div>
            <x-stat-tile label="Registered" :value="$enrollment->created_at->format('d M Y')" />
            <x-stat-tile label="Cohort" :value="$enrollment->cohort->name ?? '—'" />
        </div>

        @if (! $enrollment->confirmed_at)
            <div class="rounded-lg border border-[var(--border-hairline)] bg-[var(--surface-card)] p-5">
                <h2 class="text-[14px] font-semibold text-[var(--text-primary)]">Registration in progress</h2>
                <p class="mt-2 text-[13px] text-[var(--text-secondary)]">
                    @if ($enrollment->status === 'interest_submitted')
                        Your Interest Form is with your outlet manager for endorsement.
                    @elseif ($enrollment->status === 'endorsed')
                        Your manager has endorsed you. Head of People Development will confirm your place and cohort date.
                    @elseif ($enrollment->status === 'on_hold')
                        Your registration is on hold.
                        @if ($enrollment->endorsement_note) <span class="block mt-1 text-[var(--text-muted)]">Reason: {{ $enrollment->endorsement_note }}</span> @endif
                    @elseif ($enrollment->status === 'declined')
                        Your registration was not endorsed this time.
                        @if ($enrollment->endorsement_note) <span class="block mt-1 text-[var(--text-muted)]">Reason: {{ $enrollment->endorsement_note }}</span> @endif
                    @endif
                </p>
            </div>
        @else
            <div class="rounded-lg border border-[var(--border-hairline)] bg-[var(--surface-card)] p-5">
                <h2 class="text-[14px] font-semibold text-[var(--text-primary)] mb-4">Your stamp collection</h2>
                <div class="flex flex-col gap-3">
                    @foreach ($embassies as $i => $embassy)
                        @php
                            $stamp = $stampsByEmbassy->get($embassy->id);
                            $earned = $stamp?->status === 'earned';
                            $scheduled = $stamp && ! $earned;
                            $color = $seriesColor($i + 1);
                        @endphp
                        <div class="flex items-center gap-4 rounded-md border border-[var(--border-hairline)] p-3 {{ $earned ? '' : 'opacity-70' }}">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full text-[15px] font-bold"
                                 style="{{ $earned ? "background: {$color}; color: white;" : "background: var(--surface-2); color: var(--text-muted); border: 2px dashed var(--border-hairline);" }}">
                                {{ $earned ? '✓' : $i + 1 }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-[13px] font-semibold text-[var(--text-primary)]">{{ $embassy->name }}</div>
                                <div class="text-[12px] text-[var(--text-muted)]">{{ $embassy->stamp_name }} · {{ $embassy->theme }}</div>
                            </div>
                            <div class="shrink-0 text-right text-[12px]">
                                @if ($earned)
                                    <span class="font-medium" style="color: var(--success-text)">Earned</span>
                                    <div class="text-[var(--text-muted)]">{{ $stamp->visit_date?->format('d M Y') }}</div>
                                @elseif ($scheduled)
                                    <span class="font-medium text-[var(--text-secondary)]">Scheduled</span>
                                    <div class="text-[var(--text-muted)]">{{ $stamp->visit_date?->format('d M Y') }}</div>
                                @else
                                    <span class="text-[var(--text-muted)]">Not yet visited</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            @if ($stage === 'Promotion-Ready')
                <div class="mt-4 rounded-lg border border-[var(--border-hairline)] p-4" style="background: color-mix(in srgb, var(--success-text) 8%, transparent)">
                    <div class="text-[13px] font-semibold text-[var(--text-primary)]">You're on the pipeline register 🎉</div>
                    <p class="mt-1 text-[12px] text-[var(--text-secondary)]">Your passport is fully endorsed and you've been named for the quarterly succession meeting. Completing BLIP doesn't guarantee promotion — but it makes you a visible, evidenced candidate.</p>
                </div>
            @endif
        @endif
    @endif
</div>
