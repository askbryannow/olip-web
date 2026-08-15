@php use App\Support\Formatters as F; @endphp
<div>
    <x-page-header title="Safety" subtitle="Lost-time injury frequency, incident hotspots, and audit compliance" />

    <x-kpi-row>
        <x-stat-tile label="LTIFR" :value="number_format($latestLtifr, 1)" sublabel="per million hours, trailing 12mo" :deltaGood="$latestLtifr < 3" />
        <x-stat-tile label="Total Incidents" :value="F::num($totalIncidents)" sublabel="trailing 6mo, all severities" />
        <x-stat-tile label="Lost Time Injuries" :value="F::num($totalLTI)" sublabel="trailing 6mo" :deltaGood="$totalLTI === 0" />
        <x-stat-tile label="Avg Compliance Score" :value="round($avgCompliance)" :sublabel="$cm->label" :deltaGood="$avgCompliance >= 90" />
    </x-kpi-row>

    <x-chart-grid :cols="2">
        <x-chart-card title="LTIFR Trend" subtitle="Lost-time injuries per million hours worked (cumulative), trailing 12 months">
            <x-chart :config="$ltifrChart" :height="260" />
        </x-chart-card>

        <x-chart-card title="Incidents by Location" subtitle="Trailing 6 months"
            :legend="view('components.legend', ['items' => [['label' => 'All incidents', 'color' => 'var(--series-1)'], ['label' => 'Lost time injuries', 'color' => 'var(--status-critical)']]])">
            <x-chart :config="$byOutletChart" :height="260" />
        </x-chart-card>
    </x-chart-grid>

    <div class="mt-4">
        <x-chart-card title="Safety Compliance Score by Outlet" :subtitle="$cm->label">
            <x-chart :config="$complianceChart" :height="260" />
        </x-chart-card>
    </div>
</div>
