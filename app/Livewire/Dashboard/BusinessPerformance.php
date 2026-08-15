<?php

namespace App\Livewire\Dashboard;

use App\Support\ChartConfig;
use App\Support\Filters;
use App\Support\Selectors;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class BusinessPerformance extends Component
{
    #[On('filters-changed')]
    public function refresh(): void {}

    public function render()
    {
        $filters = Filters::current();
        $trend = Selectors::businessTrend($filters, 12);
        $base = $trend[0] ?? ['revenue' => 1, 'visitors' => 1, 'headcount' => 1, 'labourCost' => 1];

        $indexed = array_map(fn ($t) => [
            'month' => $t['month'],
            'Revenue' => $base['revenue'] > 0 ? ($t['revenue'] / $base['revenue']) * 100 : 100,
            'Visitors' => $base['visitors'] > 0 ? ($t['visitors'] / $base['visitors']) * 100 : 100,
            'Headcount' => $base['headcount'] > 0 ? ($t['headcount'] / $base['headcount']) * 100 : 100,
            'Labour Cost' => $base['labourCost'] > 0 ? ($t['labourCost'] / $base['labourCost']) * 100 : 100,
        ], $trend);

        $latest = end($trend) ?: null;
        $revenuePerEmployee = $latest && $latest['headcount'] > 0 ? $latest['revenue'] / $latest['headcount'] : 0;
        $labourCostPctRevenue = $latest && $latest['revenue'] > 0 ? ($latest['labourCost'] / $latest['revenue']) * 100 : 0;
        $revenuePerVisitor = $latest && $latest['visitors'] > 0 ? $latest['revenue'] / $latest['visitors'] : 0;

        $indexedChart = ChartConfig::line(array_column($indexed, 'month'), [
            ['label' => 'Revenue', 'data' => array_column($indexed, 'Revenue'), 'color' => 'var(--series-1)'],
            ['label' => 'Visitors', 'data' => array_column($indexed, 'Visitors'), 'color' => 'var(--series-3)'],
            ['label' => 'Headcount', 'data' => array_column($indexed, 'Headcount'), 'color' => 'var(--series-4)'],
            ['label' => 'Labour Cost', 'data' => array_column($indexed, 'Labour Cost'), 'color' => 'var(--series-8)'],
        ]);
        $revPerEmpChart = ChartConfig::line(array_column($trend, 'month'), [
            ['label' => 'Revenue / employee', 'data' => array_map(fn ($t) => $t['headcount'] > 0 ? $t['revenue'] / $t['headcount'] : 0, $trend), 'color' => 'var(--series-1)'],
        ], 'currency');

        return view('livewire.dashboard.business-performance', compact('trend', 'latest', 'revenuePerEmployee', 'labourCostPctRevenue', 'revenuePerVisitor', 'indexedChart', 'revPerEmpChart'));
    }
}
