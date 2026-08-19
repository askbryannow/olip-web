<?php

namespace App\Livewire\Blip;

use App\Livewire\Entry\Concerns\EntityCrudComponent;
use App\Models\BlipCohort;
use App\Support\Options;

class Cohorts extends EntityCrudComponent
{
    protected function config(): array
    {
        return [
            'model' => BlipCohort::class,
            'title' => 'BLIP Cohorts',
            'description' => 'Cohort dates and capacity for the Leadership Passport visits.',
            'fields' => [
                ['key' => 'name', 'label' => 'Name', 'kind' => 'text', 'required' => true],
                ['key' => 'code', 'label' => 'Code', 'kind' => 'text', 'required' => true],
                ['key' => 'start_date', 'label' => 'Start date', 'kind' => 'date', 'required' => true],
                ['key' => 'capacity', 'label' => 'Capacity', 'kind' => 'number', 'required' => true],
                ['key' => 'status', 'label' => 'Status', 'kind' => 'select', 'options' => Options::BLIP_COHORT_STATUSES, 'required' => true],
            ],
            'columns' => ['name', 'code', 'start_date', 'capacity', 'status'],
            'defaults' => ['capacity' => 12, 'status' => 'planned'],
        ];
    }
}
