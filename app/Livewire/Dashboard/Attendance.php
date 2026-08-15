<?php

namespace App\Livewire\Dashboard;

use App\Support\ChartConfig;
use App\Support\Filters;
use App\Support\Selectors;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Attendance extends Component
{
    #[On('filters-changed')]
    public function refresh(): void {}

    public function render()
    {
        $filters = Filters::current();
        $cm = Selectors::endMonth($filters);

        $absTrend = Selectors::absenteeismTrend($filters, 12);
        $leave = Selectors::leaveByType($filters, 12);
        $lateTrend = Selectors::lateClockInsTrend($filters, 12);
        $lateByDept = Selectors::lateClockInsByDept($filters, $cm);

        $latestAbs = collect($absTrend)->last()['rate'] ?? 0;
        $totalLeaveDays = array_sum(array_column($leave, 'days'));
        $latestLate = collect($lateTrend)->last()['late'] ?? 0;

        $absChart = ChartConfig::line(array_column($absTrend, 'month'), [['label' => 'Absenteeism rate', 'data' => array_column($absTrend, 'rate'), 'color' => 'var(--series-1)']], 'percent1');
        $leaveChart = ChartConfig::categoricalBar(array_column($leave, 'type'), array_column($leave, 'days'), 'number', true);
        $lateTrendChart = ChartConfig::bar(array_column($lateTrend, 'month'), [['label' => 'Late clock-ins', 'data' => array_column($lateTrend, 'late'), 'color' => 'var(--series-8)']]);
        $lateByDeptChart = ChartConfig::bar(array_column($lateByDept, 'department'), [['label' => 'Late clock-ins', 'data' => array_column($lateByDept, 'late'), 'color' => 'var(--series-2)']], 'number', true);

        return view('livewire.dashboard.attendance', compact('cm', 'latestAbs', 'totalLeaveDays', 'latestLate', 'absChart', 'leaveChart', 'lateTrendChart', 'lateByDeptChart'));
    }
}
