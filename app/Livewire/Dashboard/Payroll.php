<?php

namespace App\Livewire\Dashboard;

use App\Support\ChartConfig;
use App\Support\Filters;
use App\Support\Selectors;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Payroll extends Component
{
    #[On('filters-changed')]
    public function refresh(): void {}

    public function render()
    {
        $filters = Filters::current();
        $cm = Selectors::endMonth($filters);

        $trend = Selectors::payrollTrend($filters, 12);
        $otByDept = Selectors::overtimeByDept($filters, $cm);
        $pctByOutlet = Selectors::payrollPctRevenueByOutlet($filters, $cm);

        $payrollCost = Selectors::payrollCostFor($filters, $cm);
        $revenue = Selectors::revenueFor($filters, $cm);
        $pctRevenue = $revenue > 0 ? ($payrollCost / $revenue) * 100 : 0;
        $ot = Selectors::overtimeFor($filters, $cm);
        $otVariancePct = $ot['budgetHours'] > 0 ? (($ot['hours'] - $ot['budgetHours']) / $ot['budgetHours']) * 100 : 0;

        $trendChart = ChartConfig::area(array_column($trend, 'month'), [
            ['label' => 'Base pay', 'data' => array_column($trend, 'base'), 'color' => 'var(--series-1)'],
            ['label' => 'Overtime', 'data' => array_column($trend, 'overtime'), 'color' => 'var(--series-2)'],
            ['label' => 'Statutory', 'data' => array_column($trend, 'statutory'), 'color' => 'var(--series-4)'],
        ]);
        $otChart = ChartConfig::bar(array_column($otByDept, 'department'), [
            ['label' => 'Budget hrs', 'data' => array_column($otByDept, 'budgetHours'), 'color' => 'var(--baseline)'],
            ['label' => 'Actual hrs', 'data' => array_column($otByDept, 'actualHours'), 'color' => 'var(--series-1)'],
        ], 'number', true);
        $pctChart = ChartConfig::bar(array_column($pctByOutlet, 'outlet'), [['label' => 'Payroll % of revenue', 'data' => array_column($pctByOutlet, 'pct'), 'color' => 'var(--series-3)']], 'percent1', true);

        return view('livewire.dashboard.payroll', compact('cm', 'payrollCost', 'pctRevenue', 'ot', 'otVariancePct', 'trendChart', 'otChart', 'pctChart', 'pctByOutlet'));
    }
}
