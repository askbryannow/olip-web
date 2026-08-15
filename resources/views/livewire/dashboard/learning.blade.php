@php use App\Support\Formatters as F; @endphp
<div>
    <x-page-header title="Learning & Development" subtitle="Training investment, mandatory compliance, and HRD Corp claim utilization" />

    <x-kpi-row>
        <x-stat-tile label="Training Hours / Employee" :value="number_format($latestHours, 1)" sublabel="latest month" />
        <x-stat-tile label="Mandatory Compliance" :value="F::pct($latestCompliance)" sublabel="latest month" :deltaGood="$latestCompliance >= 90" />
        <x-stat-tile label="HRD Corp Claims" :value="F::myr($totalSubmitted)" :sublabel="round($claimRate).'% of eligible, trailing 12mo'" />
    </x-kpi-row>

    <x-chart-grid :cols="2">
        <x-chart-card title="Training Hours per Employee" subtitle="Trailing 12 months">
            <x-chart :config="$hoursChart" :height="260" />
        </x-chart-card>

        <x-chart-card title="Mandatory Training Compliance" subtitle="% completed on time, trailing 12 months">
            <x-chart :config="$complianceChart" :height="260" />
        </x-chart-card>
    </x-chart-grid>

    <div class="mt-4">
        <x-chart-card title="HRD Corp Claims: Eligible vs Submitted" subtitle="MYR, trailing 12 months"
            :legend="view('components.legend', ['items' => [['label' => 'Eligible', 'color' => 'var(--baseline)'], ['label' => 'Submitted', 'color' => 'var(--series-1)']]])">
            <x-chart :config="$hrdcChart" :height="240" />
        </x-chart-card>
    </div>
</div>
