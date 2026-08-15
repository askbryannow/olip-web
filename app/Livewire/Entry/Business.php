<?php

namespace App\Livewire\Entry;

use App\Livewire\Entry\Concerns\EntityCrudComponent;
use App\Models\BusinessPerformanceRecord;
use App\Support\Calendar;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Business extends EntityCrudComponent
{
    protected function config(): array
    {
        return [
            'model' => BusinessPerformanceRecord::class,
            'title' => 'Business Performance',
            'description' => 'Monthly revenue and visitor counts by outlet.',
            'fields' => [
                ['key' => 'month_key', 'label' => 'Month', 'kind' => 'month', 'required' => true],
                ['key' => 'outlet_id', 'label' => 'Outlet', 'kind' => 'outlet', 'required' => true],
                ['key' => 'bu', 'label' => 'Business unit', 'kind' => 'derived'],
                ['key' => 'state', 'label' => 'State', 'kind' => 'derived'],
                ['key' => 'revenue', 'label' => 'Revenue (MYR)', 'kind' => 'number', 'required' => true],
                ['key' => 'visitors', 'label' => 'Visitors', 'kind' => 'number', 'required' => true],
            ],
            'columns' => ['month_key', 'outlet_id', 'revenue', 'visitors'],
            'defaults' => ['month_key' => Calendar::currentMonthKey()],
        ];
    }
}
