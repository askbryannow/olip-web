<?php

namespace App\Livewire\Dashboard;

use App\Support\ChartConfig;
use App\Support\Filters;
use App\Support\Selectors;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Learning extends Component
{
    #[On('filters-changed')]
    public function refresh(): void {}

    public function render()
    {
        $filters = Filters::current();
        $hours = Selectors::trainingHoursTrend($filters, 12);
        $compliance = Selectors::complianceTrend($filters, 12);
        $hrdc = Selectors::hrdcClaimsTrend($filters, 12);

        $latestHours = collect($hours)->last()['hoursPerEmployee'] ?? 0;
        $latestCompliance = collect($compliance)->last()['rate'] ?? 0;
        $totalEligible = array_sum(array_column($hrdc, 'eligible'));
        $totalSubmitted = array_sum(array_column($hrdc, 'submitted'));
        $claimRate = $totalEligible > 0 ? ($totalSubmitted / $totalEligible) * 100 : 0;

        $hoursChart = ChartConfig::line(array_column($hours, 'month'), [['label' => 'Hours / employee', 'data' => array_column($hours, 'hoursPerEmployee'), 'color' => 'var(--series-1)']]);
        $complianceChart = ChartConfig::line(array_column($compliance, 'month'), [['label' => 'Compliance rate', 'data' => array_column($compliance, 'rate'), 'color' => 'var(--series-3)']], 'percent1');
        $hrdcChart = ChartConfig::bar(array_column($hrdc, 'month'), [
            ['label' => 'Eligible', 'data' => array_column($hrdc, 'eligible'), 'color' => 'var(--baseline)'],
            ['label' => 'Submitted', 'data' => array_column($hrdc, 'submitted'), 'color' => 'var(--series-1)'],
        ], 'currency');

        return view('livewire.dashboard.learning', compact('latestHours', 'latestCompliance', 'totalSubmitted', 'claimRate', 'hoursChart', 'complianceChart', 'hrdcChart'));
    }
}
