@php use App\Support\Formatters as F; @endphp
<div>
    <x-page-header title="Attendance & Leave" subtitle="Absenteeism trend, leave utilization, and punctuality" />

    <x-kpi-row>
        <x-stat-tile label="Absenteeism Rate" :value="F::pct($latestAbs)" sublabel="latest month" :deltaGood="$latestAbs < 3" />
        <x-stat-tile label="Total Leave Days" :value="F::num($totalLeaveDays)" sublabel="trailing 12mo" />
        <x-stat-tile label="Late Clock-ins" :value="F::num($latestLate)" sublabel="latest month" />
    </x-kpi-row>

    <x-chart-grid :cols="2">
        <x-chart-card title="Absenteeism Trend" subtitle="% of working days lost, trailing 12 months">
            <x-chart :config="$absChart" :height="260" />
        </x-chart-card>

        <x-chart-card title="Leave by Type" subtitle="Days consumed, trailing 12 months">
            <x-chart :config="$leaveChart" :height="260" />
        </x-chart-card>

        <x-chart-card title="Late Clock-ins Trend" subtitle="Trailing 12 months">
            <x-chart :config="$lateTrendChart" :height="220" />
        </x-chart-card>

        <x-chart-card title="Late Clock-ins by Department" :subtitle="$cm->label">
            <x-chart :config="$lateByDeptChart" :height="220" />
        </x-chart-card>
    </x-chart-grid>
</div>
