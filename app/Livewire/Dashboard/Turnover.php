<?php

namespace App\Livewire\Dashboard;

use App\Support\ChartConfig;
use App\Support\Filters;
use App\Support\Selectors;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Turnover extends Component
{
    #[On('filters-changed')]
    public function refresh(): void {}

    public function render()
    {
        $filters = Filters::current();
        $trend = Selectors::turnoverTrend($filters, 12);
        $rate = Selectors::turnoverRateTrailing($filters, 12);
        $early = Selectors::earlyAttritionRate($filters, 12);
        $heat = Selectors::turnoverHeatmapByDept($filters, 6);

        $totalVoluntary = array_sum(array_column($trend, 'voluntary'));
        $totalInvoluntary = array_sum(array_column($trend, 'involuntary'));
        $totalResigns = $totalVoluntary + $totalInvoluntary;
        $voluntaryPct = $totalResigns > 0 ? ($totalVoluntary / $totalResigns) * 100 : 0;

        $trendChart = ChartConfig::bar(array_column($trend, 'month'), [
            ['label' => 'Voluntary', 'data' => array_column($trend, 'voluntary'), 'color' => 'var(--series-1)', 'stack' => 'exits'],
            ['label' => 'Involuntary', 'data' => array_column($trend, 'involuntary'), 'color' => 'var(--series-8)', 'stack' => 'exits'],
        ], 'number', false, true);
        $exitVolumeChart = ChartConfig::bar(array_column($trend, 'month'), [['label' => 'Total exits', 'data' => array_column($trend, 'total'), 'color' => 'var(--series-7)']]);

        return view('livewire.dashboard.turnover', compact('rate', 'early', 'heat', 'totalVoluntary', 'totalResigns', 'voluntaryPct', 'trendChart', 'exitVolumeChart'));
    }
}
