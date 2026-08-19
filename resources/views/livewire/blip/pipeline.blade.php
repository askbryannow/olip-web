<div>
    <x-page-header title="Leadership Pipeline" subtitle="Where every registered employee stands on the BLIP passport ladder." />

    <x-kpi-row>
        <x-stat-tile label="Total registered" :value="$totalRegistered" />
        <x-stat-tile label="Active Travellers" :value="$activeTravellers" sublabel="Applicant, Traveller or Voyager" />
        <x-stat-tile label="Endorsed" :value="$endorsedAwaitingReview->count()" sublabel="Passport full, awaiting review" />
        <x-stat-tile label="Promotion-ready" :value="$promotionReady->count()" sublabel="On the succession slate" />
    </x-kpi-row>

    <x-chart-grid :cols="1">
        <x-chart-card title="Passport funnel">
            <x-funnel :stages="$funnel" />
        </x-chart-card>
    </x-chart-grid>

    @if ($endorsedAwaitingReview->isNotEmpty())
        <div class="mt-5">
            <h2 class="mb-2 text-[14px] font-semibold text-[var(--text-primary)]">Endorsed — ready for succession review</h2>
            <div class="overflow-x-auto rounded-lg border border-[var(--border-hairline)] bg-[var(--surface-card)] scrollbar-thin">
                <table class="w-full min-w-[600px] border-collapse">
                    <thead>
                        <tr class="border-b border-[var(--border-hairline)]">
                            <th class="px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Employee</th>
                            <th class="px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Outlet</th>
                            <th class="px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Stamps</th>
                            <th class="px-3 py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($endorsedAwaitingReview as $enrollment)
                            <tr class="border-b border-[var(--border-hairline)] last:border-0 hover:bg-[var(--surface-2)]">
                                <td class="px-3 py-2 text-[12px] text-[var(--text-primary)] whitespace-nowrap">{{ $enrollment->employee->name }}</td>
                                <td class="px-3 py-2 text-[12px] text-[var(--text-secondary)] whitespace-nowrap">{{ $enrollment->employee->outlet->name ?? $enrollment->employee->outlet_id }}</td>
                                <td class="px-3 py-2 text-[12px] text-[var(--text-secondary)] tabular">{{ $enrollment->stampsEarnedCount() }} / 5</td>
                                <td class="px-3 py-2 text-right whitespace-nowrap">
                                    <button wire:click="markPromotionReady({{ $enrollment->id }})" class="text-[12px] font-medium text-[var(--series-1)] hover:underline">Mark Promotion-Ready</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if ($promotionReady->isNotEmpty())
        <div class="mt-5">
            <h2 class="mb-2 text-[14px] font-semibold text-[var(--text-primary)]">Promotion-Ready — pipeline register</h2>
            <div class="overflow-x-auto rounded-lg border border-[var(--border-hairline)] bg-[var(--surface-card)] scrollbar-thin">
                <table class="w-full min-w-[500px] border-collapse">
                    <thead>
                        <tr class="border-b border-[var(--border-hairline)]">
                            <th class="px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Employee</th>
                            <th class="px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Outlet</th>
                            <th class="px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Ready since</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($promotionReady as $enrollment)
                            <tr class="border-b border-[var(--border-hairline)] last:border-0 hover:bg-[var(--surface-2)]">
                                <td class="px-3 py-2 text-[12px] text-[var(--text-primary)] whitespace-nowrap">{{ $enrollment->employee->name }}</td>
                                <td class="px-3 py-2 text-[12px] text-[var(--text-secondary)] whitespace-nowrap">{{ $enrollment->employee->outlet->name ?? $enrollment->employee->outlet_id }}</td>
                                <td class="px-3 py-2 text-[12px] text-[var(--text-secondary)] whitespace-nowrap">{{ $enrollment->promotion_ready_at->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
