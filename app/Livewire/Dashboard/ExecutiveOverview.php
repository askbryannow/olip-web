<?php

namespace App\Livewire\Dashboard;

use App\Support\Calendar;
use App\Support\ChartConfig;
use App\Support\Filters;
use App\Support\Selectors;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class ExecutiveOverview extends Component
{
    #[On('filters-changed')]
    public function refresh(): void {}

    public function render()
    {
        $filters = Filters::current();
        $calendar = Calendar::trailing();
        $cm = Selectors::endMonth($filters);
        $date = $cm->endOfMonth();

        $idx = collect($calendar)->search(fn ($c) => $c->key === $cm->key);
        $prevCm = $idx > 0 ? $calendar[$idx - 1] : null;

        $headcount = Selectors::headcountAt($filters, $date);
        $prevHeadcount = $prevCm ? Selectors::headcountAt($filters, $prevCm->endOfMonth()) : $headcount;
        $headcountDelta = $headcount - $prevHeadcount;

        $turnoverRate = Selectors::turnoverRateTrailing($filters, 12);
        $payrollCost = Selectors::payrollCostFor($filters, $cm);
        $revenue = Selectors::revenueFor($filters, $cm);
        $payrollPctRevenue = $revenue > 0 ? ($payrollCost / $revenue) * 100 : 0;
        $overtime = Selectors::overtimeFor($filters, $cm);

        $ttHire = Selectors::timeToHireTrend($filters, 3);
        $avgTimeToHire = collect($ttHire)->filter(fn ($t) => $t['days'] > 0)->last()['days'] ?? 0;
        $revenuePerEmployee = $headcount > 0 ? $revenue / $headcount : 0;
        $ltifr = collect(Selectors::ltifrTrend($filters, 12))->last()['ltifr'] ?? 0;

        $hcTrend = Selectors::headcountTrend($filters, 12);
        $hrTrend = Selectors::hireResignTrend($filters, 12);
        $buData = Selectors::headcountByBU($filters, $date);
        $genderData = Selectors::genderMix($filters, $date);
        $etData = Selectors::employmentTypeMix($filters, $date);

        $buChart = ChartConfig::categoricalBar(array_column($buData, 'bu'), array_column($buData, 'headcount'));
        $hrChart = ChartConfig::line(array_column($hrTrend, 'month'), [
            ['label' => 'Hires', 'data' => array_column($hrTrend, 'hires'), 'color' => 'var(--series-3)'],
            ['label' => 'Resignations', 'data' => array_column($hrTrend, 'resignations'), 'color' => 'var(--series-8)'],
            ['label' => 'Net growth', 'data' => array_column($hrTrend, 'netGrowth'), 'color' => 'var(--series-1)', 'dashed' => true],
        ]);
        $etChart = ChartConfig::bar(array_column($etData, 'type'), [['label' => 'Employees', 'data' => array_column($etData, 'count'), 'color' => 'var(--series-1)']], 'number', true);
        $hcChart = ChartConfig::line(array_column($hcTrend, 'month'), [
            ['label' => 'Headcount', 'data' => array_column($hcTrend, 'headcount'), 'color' => 'var(--series-1)'],
        ]);

        return view('livewire.dashboard.executive-overview', [
            'cm' => $cm, 'headcount' => $headcount, 'headcountDelta' => $headcountDelta,
            'turnoverRate' => $turnoverRate, 'payrollCost' => $payrollCost, 'payrollPctRevenue' => $payrollPctRevenue,
            'overtime' => $overtime, 'avgTimeToHire' => $avgTimeToHire, 'revenuePerEmployee' => $revenuePerEmployee, 'ltifr' => $ltifr,
            'genderData' => $genderData,
            'buChart' => $buChart, 'hrChart' => $hrChart, 'etChart' => $etChart, 'hcChart' => $hcChart,
        ]);
    }
}
