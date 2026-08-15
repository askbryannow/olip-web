@php use App\Support\Formatters as F; @endphp
<div>
    <x-page-header title="Executive Overview" subtitle="As of {{ $cm->label }} · workforce, cost, and safety at a glance" />

    <x-kpi-row>
        <x-stat-tile label="Headcount" :value="F::num($headcount)" :delta="($headcountDelta >= 0 ? '+' : '').$headcountDelta.' MoM'" :deltaGood="$headcountDelta >= 0" />
        <x-stat-tile label="Turnover Rate" :value="F::pct($turnoverRate)" sublabel="trailing 12mo" />
        <x-stat-tile label="Payroll Cost" :value="F::myr($payrollCost)" :sublabel="F::pct($payrollPctRevenue).' of revenue'" />
        <x-stat-tile label="Overtime Cost" :value="F::myr($overtime['pay'])" :sublabel="F::num($overtime['hours']).' hrs'" />
        <x-stat-tile label="Time to Hire" :value="round($avgTimeToHire).' days'" sublabel="avg, trailing 3mo" />
        <x-stat-tile label="Revenue / Employee" :value="F::myr($revenuePerEmployee)" sublabel="this month" />
        <x-stat-tile label="Safety Incident Rate" :value="number_format($ltifr, 1)" sublabel="LTIFR, trailing 12mo" />
    </x-kpi-row>

    <x-chart-grid :cols="3">
        <x-chart-card title="Headcount by Business Unit" :subtitle="$cm->label">
            <x-chart :config="$buChart" :height="260" />
        </x-chart-card>

        <x-chart-card title="Hires, Resignations & Net Growth" subtitle="Trailing 12 months"
            :legend="view('components.legend', ['items' => [['label' => 'Hires', 'color' => 'var(--series-3)'], ['label' => 'Resignations', 'color' => 'var(--series-8)'], ['label' => 'Net growth', 'color' => 'var(--series-1)']]])">
            <x-chart :config="$hrChart" :height="260" />
        </x-chart-card>

        <div class="flex flex-col gap-4">
            <x-chart-card title="Employment Type" :subtitle="$cm->label">
                <x-chart :config="$etChart" :height="110" />
            </x-chart-card>
            <x-chart-card title="Gender Mix" :subtitle="$cm->label">
                <div class="flex items-center gap-4">
                    <div class="flex-1 h-6 rounded-full overflow-hidden flex bg-[var(--surface-2)]">
                        <div style="width: {{ $genderData['total'] > 0 ? ($genderData['male'] / $genderData['total']) * 100 : 0 }}%; background: var(--series-1)"></div>
                        <div style="width: {{ $genderData['total'] > 0 ? ($genderData['female'] / $genderData['total']) * 100 : 0 }}%; background: var(--series-5)"></div>
                    </div>
                </div>
                <div class="flex justify-between mt-2 text-[12px]">
                    <span class="text-[var(--text-secondary)]">● Male {{ F::num($genderData['male']) }} ({{ $genderData['total'] > 0 ? round(($genderData['male'] / $genderData['total']) * 100) : 0 }}%)</span>
                    <span class="text-[var(--text-secondary)]">● Female {{ F::num($genderData['female']) }} ({{ $genderData['total'] > 0 ? round(($genderData['female'] / $genderData['total']) * 100) : 0 }}%)</span>
                </div>
            </x-chart-card>
        </div>
    </x-chart-grid>

    <div class="mt-4">
        <x-chart-card title="Headcount Trend" subtitle="Trailing 12 months">
            <x-chart :config="$hcChart" :height="200" />
        </x-chart-card>
    </div>
</div>
