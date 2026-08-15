<?php

namespace App\Livewire\Entry;

use App\Livewire\Entry\Concerns\EntityCrudComponent;
use App\Models\TrainingRecord;
use App\Support\Calendar;
use App\Support\Options;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Training extends EntityCrudComponent
{
    protected function config(): array
    {
        return [
            'model' => TrainingRecord::class,
            'title' => 'Training & HRDC',
            'description' => 'Monthly training hours, mandatory compliance, and HRD Corp claims by outlet/department/employment type.',
            'fields' => [
                ['key' => 'month_key', 'label' => 'Month', 'kind' => 'month', 'required' => true],
                ['key' => 'outlet_id', 'label' => 'Outlet', 'kind' => 'outlet', 'required' => true],
                ['key' => 'department', 'label' => 'Department', 'kind' => 'department', 'required' => true],
                ['key' => 'employment_type', 'label' => 'Employment type', 'kind' => 'select', 'options' => Options::EMPLOYMENT_TYPES, 'required' => true],
                ['key' => 'bu', 'label' => 'Business unit', 'kind' => 'derived'],
                ['key' => 'state', 'label' => 'State', 'kind' => 'derived'],
                ['key' => 'headcount', 'label' => 'Headcount', 'kind' => 'number', 'required' => true],
                ['key' => 'training_hours', 'label' => 'Training hours (total)', 'kind' => 'number', 'required' => true],
                ['key' => 'mandatory_due', 'label' => 'Mandatory training due', 'kind' => 'number', 'required' => true],
                ['key' => 'mandatory_completed', 'label' => 'Mandatory training completed', 'kind' => 'number', 'required' => true],
                ['key' => 'hrdc_claim_eligible', 'label' => 'HRDC eligible (MYR)', 'kind' => 'number', 'required' => true],
                ['key' => 'hrdc_claim_submitted', 'label' => 'HRDC submitted (MYR)', 'kind' => 'number', 'required' => true],
            ],
            'columns' => ['month_key', 'outlet_id', 'department', 'employment_type', 'training_hours', 'mandatory_completed'],
            'defaults' => ['month_key' => Calendar::currentMonthKey()],
        ];
    }
}
