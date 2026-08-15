@php use App\Support\Formatters as F; @endphp
<div>
    <x-page-header title="Payroll & Overtime" subtitle="Payroll cost trend, overtime variance by department, and payroll efficiency by outlet" />

    <x-kpi-row>
        <x-stat-tile label="Payroll Cost" :value="F::myr($payrollCost)" :sublabel="$cm->label" />
        <x-stat-tile label="Payroll % of Revenue" :value="F::pct($pctRevenue)" :sublabel="$cm->label" />
        <x-stat-tile label="Overtime Cost" :value="F::myr($ot['pay'])" :sublabel="F::num($ot['hours']).' hrs'" />
        <x-stat-tile label="OT vs Budget" :value="($otVariancePct >= 0 ? '+' : '').number_format($otVariancePct, 1).'%'" sublabel="actual vs budgeted hours"
            :deltaGood="$otVariancePct <= 0" :delta="$otVariancePct <= 0 ? 'Within budget' : 'Over budget'" />
    </x-kpi-row>

    <x-chart-grid :cols="2">
        <x-chart-card title="Payroll Cost Trend" subtitle="Base + overtime + statutory, trailing 12 months"
            :legend="view('components.legend', ['items' => [['label' => 'Base pay', 'color' => 'var(--series-1)'], ['label' => 'Overtime', 'color' => 'var(--series-2)'], ['label' => 'Statutory', 'color' => 'var(--series-4)']]])">
            <x-chart :config="$trendChart" :height="280" />
        </x-chart-card>

        <x-chart-card title="Overtime by Department: Actual vs Budget" :subtitle="$cm->label"
            :legend="view('components.legend', ['items' => [['label' => 'Actual hrs', 'color' => 'var(--series-1)'], ['label' => 'Budget hrs', 'color' => 'var(--baseline)']]])">
            <x-chart :config="$otChart" :height="280" />
        </x-chart-card>
    </x-chart-grid>

    <div class="mt-4">
        <x-chart-card title="Payroll % of Revenue by Outlet" :subtitle="$cm->label">
            <x-chart :config="$pctChart" :height="max(220, count($pctByOutlet) * 28)" />
        </x-chart-card>
    </div>
</div>
