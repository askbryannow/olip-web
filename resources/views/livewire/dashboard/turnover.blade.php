@php use App\Support\Formatters as F; @endphp
<div>
    <x-page-header title="Turnover" subtitle="Attrition trend, voluntary vs involuntary split, early attrition, and department hotspots" />

    <x-kpi-row>
        <x-stat-tile label="Turnover Rate" :value="F::pct($rate)" sublabel="trailing 12mo" />
        <x-stat-tile label="Voluntary Share" :value="round($voluntaryPct).'%'" :sublabel="F::num($totalVoluntary).' of '.F::num($totalResigns).' exits'" />
        <x-stat-tile label="Early Attrition" :value="round($early).'%'" sublabel="exits within 1yr tenure" :deltaGood="$early < 25" :delta="$early < 25 ? 'Healthy' : 'Elevated'" />
        <x-stat-tile label="Total Exits" :value="F::num($totalResigns)" sublabel="trailing 12mo" />
    </x-kpi-row>

    <x-chart-grid :cols="2">
        <x-chart-card title="Monthly Turnover Trend" subtitle="Voluntary vs involuntary exits, trailing 12 months"
            :legend="view('components.legend', ['items' => [['label' => 'Voluntary', 'color' => 'var(--series-1)'], ['label' => 'Involuntary', 'color' => 'var(--series-8)']]])">
            <x-chart :config="$trendChart" :height="280" />
        </x-chart-card>

        <x-chart-card title="Turnover Rate by Department" subtitle="Monthly rate (%), trailing 6 months">
            <x-heatmap :rows="$heat['depts']" :cols="$heat['months']" :cells="$heat['cells']" suffix="%" />
        </x-chart-card>
    </x-chart-grid>

    <div class="mt-4">
        <x-chart-card title="Exit Volume by Month" subtitle="Total exits, trailing 12 months">
            <x-chart :config="$exitVolumeChart" :height="200" />
        </x-chart-card>
    </div>
</div>
