@php use App\Support\Formatters as F; @endphp
<div>
    <x-page-header title="People & Business Performance" subtitle="Revenue and footfall against headcount and labour cost — the exec tie-out" />

    <x-kpi-row>
        <x-stat-tile label="Revenue" :value="F::myr($latest['revenue'] ?? 0)" :sublabel="$latest['month'] ?? ''" />
        <x-stat-tile label="Visitors" :value="F::num($latest['visitors'] ?? 0)" :sublabel="$latest['month'] ?? ''" />
        <x-stat-tile label="Revenue / Employee" :value="F::myr($revenuePerEmployee)" :sublabel="$latest['month'] ?? ''" />
        <x-stat-tile label="Labour Cost % of Revenue" :value="F::pct($labourCostPctRevenue)" :sublabel="$latest['month'] ?? ''" :deltaGood="$labourCostPctRevenue < 35" />
        <x-stat-tile label="Revenue / Visitor" :value="F::myr($revenuePerVisitor, false)" :sublabel="$latest['month'] ?? ''" />
    </x-kpi-row>

    <x-chart-grid :cols="1">
        <x-chart-card title="Revenue, Visitors, Headcount & Labour Cost — Indexed" :subtitle="'Indexed to 100 at '.($trend[0]['month'] ?? '').' so growth rates compare on one scale'"
            :legend="view('components.legend', ['items' => [['label' => 'Revenue', 'color' => 'var(--series-1)'], ['label' => 'Visitors', 'color' => 'var(--series-3)'], ['label' => 'Headcount', 'color' => 'var(--series-4)'], ['label' => 'Labour cost', 'color' => 'var(--series-8)']]])">
            <x-chart :config="$indexedChart" :height="300" />
        </x-chart-card>
    </x-chart-grid>

    <div class="mt-4">
        <x-chart-card title="Revenue per Employee Trend" subtitle="MYR, trailing 12 months">
            <x-chart :config="$revPerEmpChart" :height="220" />
        </x-chart-card>
    </div>
</div>
