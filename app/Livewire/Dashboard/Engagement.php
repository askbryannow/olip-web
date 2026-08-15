<?php

namespace App\Livewire\Dashboard;

use App\Support\ChartConfig;
use App\Support\Filters;
use App\Support\Selectors;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Engagement extends Component
{
    #[On('filters-changed')]
    public function refresh(): void {}

    public function render()
    {
        $filters = Filters::current();
        $trend = Selectors::engagementTrend($filters);
        $grievances = Selectors::grievanceBreakdown($filters);

        $latest = collect($trend)->last();
        $totalGrievances = array_sum(array_column($grievances, 'count'));
        $totalRecognitions = array_sum(array_column(array_slice($trend, -4), 'recognitions'));

        $scoreChart = ChartConfig::line(array_column($trend, 'quarter'), [
            ['label' => 'Engagement score', 'data' => array_column($trend, 'score'), 'color' => 'var(--series-1)'],
            ['label' => 'eNPS', 'data' => array_column($trend, 'eNPS'), 'color' => 'var(--series-7)'],
        ]);
        $grievanceChart = ChartConfig::categoricalBar(array_column($grievances, 'category'), array_column($grievances, 'count'), 'number', true);
        $recognitionChart = ChartConfig::bar(array_column($trend, 'quarter'), [['label' => 'Recognitions', 'data' => array_column($trend, 'recognitions'), 'color' => 'var(--series-3)']]);

        return view('livewire.dashboard.engagement', compact('latest', 'totalGrievances', 'totalRecognitions', 'scoreChart', 'grievanceChart', 'recognitionChart'));
    }
}
