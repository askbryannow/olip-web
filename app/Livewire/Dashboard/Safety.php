<?php

namespace App\Livewire\Dashboard;

use App\Support\ChartConfig;
use App\Support\Filters;
use App\Support\Selectors;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Safety extends Component
{
    #[On('filters-changed')]
    public function refresh(): void {}

    public function render()
    {
        $filters = Filters::current();
        $cm = Selectors::endMonth($filters);

        $ltifr = Selectors::ltifrTrend($filters, 12);
        $byOutlet = Selectors::incidentsByOutlet($filters, 6);
        $compliance = Selectors::complianceByOutlet($filters, $cm);

        $latestLtifr = collect($ltifr)->last()['ltifr'] ?? 0;
        $totalIncidents = array_sum(array_column($byOutlet, 'count'));
        $totalLTI = array_sum(array_column($byOutlet, 'lti'));
        $avgCompliance = count($compliance) ? array_sum(array_column($compliance, 'score')) / count($compliance) : 0;

        $ltifrChart = ChartConfig::line(array_column($ltifr, 'month'), [['label' => 'LTIFR', 'data' => array_column($ltifr, 'ltifr'), 'color' => 'var(--status-critical)']], 'number');
        $byOutletChart = ChartConfig::bar(array_column($byOutlet, 'outlet'), [
            ['label' => 'All incidents', 'data' => array_column($byOutlet, 'count'), 'color' => 'var(--series-1)'],
            ['label' => 'Lost time injuries', 'data' => array_column($byOutlet, 'lti'), 'color' => 'var(--status-critical)'],
        ], 'number', true);

        $complianceColors = array_map(function ($c) {
            if ($c['score'] >= 90) return 'var(--status-good)';
            if ($c['score'] >= 80) return 'var(--status-warning)';
            if ($c['score'] >= 70) return 'var(--status-serious)';

            return 'var(--status-critical)';
        }, $compliance);
        $complianceChart = ChartConfig::bar(array_column($compliance, 'outlet'), [['label' => 'Compliance score', 'data' => array_column($compliance, 'score')]], 'number', true);
        $complianceChart['data']['datasets'][0]['backgroundColor'] = $complianceColors;

        return view('livewire.dashboard.safety', compact('cm', 'latestLtifr', 'totalIncidents', 'totalLTI', 'avgCompliance', 'ltifrChart', 'byOutletChart', 'complianceChart'));
    }
}
