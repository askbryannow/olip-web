<?php

namespace App\Livewire\Entry;

use App\Livewire\Entry\Concerns\EntityCrudComponent;
use App\Models\OvertimeRecord;
use App\Support\Calendar;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Overtime extends EntityCrudComponent
{
    protected function config(): array
    {
        return [
            'model' => OvertimeRecord::class,
            'title' => 'Overtime',
            'description' => 'Monthly overtime hours, pay, and budget by outlet/department.',
            'fields' => [
                ['key' => 'month_key', 'label' => 'Month', 'kind' => 'month', 'required' => true],
                ['key' => 'outlet_id', 'label' => 'Outlet', 'kind' => 'outlet', 'required' => true],
                ['key' => 'department', 'label' => 'Department', 'kind' => 'department', 'required' => true],
                ['key' => 'bu', 'label' => 'Business unit', 'kind' => 'derived'],
                ['key' => 'state', 'label' => 'State', 'kind' => 'derived'],
                ['key' => 'overtime_hours', 'label' => 'Overtime hours (actual)', 'kind' => 'number', 'required' => true],
                ['key' => 'overtime_pay', 'label' => 'Overtime pay (MYR)', 'kind' => 'number', 'required' => true],
                ['key' => 'budget_hours', 'label' => 'Budgeted hours', 'kind' => 'number', 'required' => true],
            ],
            'columns' => ['month_key', 'outlet_id', 'department', 'overtime_hours', 'overtime_pay', 'budget_hours'],
            'defaults' => ['month_key' => Calendar::currentMonthKey()],
        ];
    }
}
