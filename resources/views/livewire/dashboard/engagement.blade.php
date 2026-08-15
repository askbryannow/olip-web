@php use App\Support\Formatters as F; @endphp
<div>
    <x-page-header title="Engagement" subtitle="Survey sentiment, eNPS, grievances, and recognition" />

    <x-kpi-row>
        <x-stat-tile label="Engagement Score" :value="$latest ? round($latest['score']) : '–'" :sublabel="($latest['quarter'] ?? '').', /100'" />
        <x-stat-tile label="eNPS" :value="$latest ? round($latest['eNPS']) : '–'" :sublabel="$latest['quarter'] ?? ''" :deltaGood="($latest['eNPS'] ?? 0) >= 0" />
        <x-stat-tile label="Grievances" :value="F::num($totalGrievances)" sublabel="recent period" :deltaGood="false" />
        <x-stat-tile label="Recognitions Given" :value="F::num($totalRecognitions)" sublabel="trailing 4 quarters" :deltaGood="true" />
    </x-kpi-row>

    <x-chart-grid :cols="2">
        <x-chart-card title="Engagement Score & eNPS Over Time" subtitle="Quarterly survey results">
            <x-chart :config="$scoreChart" :height="260" />
        </x-chart-card>

        <x-chart-card title="Grievances by Category" subtitle="Recent period">
            <x-chart :config="$grievanceChart" :height="260" />
        </x-chart-card>
    </x-chart-grid>

    <div class="mt-4">
        <x-chart-card title="Recognition Volume" subtitle="Recognitions given per quarter">
            <x-chart :config="$recognitionChart" :height="200" />
        </x-chart-card>
    </div>
</div>
