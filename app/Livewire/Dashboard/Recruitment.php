<?php

namespace App\Livewire\Dashboard;

use App\Support\ChartConfig;
use App\Support\Filters;
use App\Support\Selectors;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Recruitment extends Component
{
    #[On('filters-changed')]
    public function refresh(): void {}

    public function render()
    {
        $filters = Filters::current();
        $cm = Selectors::endMonth($filters);

        $funnel = Selectors::recruitmentFunnel($filters, 3);
        $aging = Selectors::vacancyAging($filters, $cm);
        $ttHire = Selectors::timeToHireTrend($filters, 12);
        $cph = Selectors::costPerHireTrend($filters, 12);

        $totalOpen = array_sum(array_column($aging, 'openVacancies'));
        $avgAge = $totalOpen > 0 ? array_sum(array_map(fn ($a) => $a['avgAgeDays'] * $a['openVacancies'], $aging)) / $totalOpen : 0;
        $latestTTH = collect($ttHire)->filter(fn ($t) => $t['days'] > 0)->last()['days'] ?? 0;
        $latestCPH = collect($cph)->filter(fn ($t) => $t['cost'] > 0)->last()['cost'] ?? 0;
        $conversionRate = $funnel[0]['value'] > 0 ? ($funnel[4]['value'] / $funnel[0]['value']) * 100 : 0;

        $agingChart = ChartConfig::bar(array_column($aging, 'department'), [['label' => 'Open vacancies', 'data' => array_column($aging, 'openVacancies'), 'color' => 'var(--series-1)']], 'number', true);
        $tthChart = ChartConfig::line(array_column($ttHire, 'month'), [['label' => 'Time to hire', 'data' => array_column($ttHire, 'days'), 'color' => 'var(--series-1)']], 'days');
        $cphChart = ChartConfig::line(array_column($cph, 'month'), [['label' => 'Cost per hire', 'data' => array_column($cph, 'cost'), 'color' => 'var(--series-2)']], 'currency');

        return view('livewire.dashboard.recruitment', compact('cm', 'funnel', 'aging', 'totalOpen', 'avgAge', 'latestTTH', 'latestCPH', 'conversionRate', 'agingChart', 'tthChart', 'cphChart'));
    }
}
