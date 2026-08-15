@php use App\Support\Formatters as F; @endphp
<div>
    <x-page-header title="Recruitment" subtitle="Applicant funnel, vacancy aging, and hiring efficiency" />

    <x-kpi-row>
        <x-stat-tile label="Open Vacancies" :value="F::num($totalOpen)" :sublabel="$cm->label" />
        <x-stat-tile label="Avg Vacancy Age" :value="round($avgAge).' days'" sublabel="open positions" />
        <x-stat-tile label="Time to Hire" :value="round($latestTTH).' days'" sublabel="latest month" />
        <x-stat-tile label="Cost per Hire" :value="F::myr($latestCPH)" sublabel="latest month" />
    </x-kpi-row>

    <x-chart-grid :cols="2">
        <x-chart-card title="Recruitment Funnel" subtitle="Applicants → Joins, trailing 3 months">
            <x-funnel :stages="$funnel" />
            <div class="mt-2 text-[12px] text-[var(--text-secondary)]">Overall conversion: <span class="font-medium text-[var(--text-primary)]">{{ number_format($conversionRate, 1) }}%</span> applied → joined</div>
        </x-chart-card>

        <x-chart-card title="Aging Vacancies by Department" :subtitle="$cm->label">
            <x-chart :config="$agingChart" :height="260" />
        </x-chart-card>

        <x-chart-card title="Time to Hire Trend" subtitle="Avg days, trailing 12 months">
            <x-chart :config="$tthChart" :height="220" />
        </x-chart-card>

        <x-chart-card title="Cost per Hire Trend" subtitle="MYR, trailing 12 months">
            <x-chart :config="$cphChart" :height="220" />
        </x-chart-card>
    </x-chart-grid>
</div>
