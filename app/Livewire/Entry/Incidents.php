<?php

namespace App\Livewire\Entry;

use App\Livewire\Entry\Concerns\EntityCrudComponent;
use App\Models\IncidentRecord;
use App\Support\Calendar;
use App\Support\Options;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Incidents extends EntityCrudComponent
{
    protected function config(): array
    {
        return [
            'model' => IncidentRecord::class,
            'title' => 'Safety Incidents',
            'description' => 'Monthly incident counts by severity, hours worked, and compliance audit score, per outlet.',
            'fields' => [
                ['key' => 'month_key', 'label' => 'Month', 'kind' => 'month', 'required' => true],
                ['key' => 'outlet_id', 'label' => 'Outlet', 'kind' => 'outlet', 'required' => true],
                ['key' => 'bu', 'label' => 'Business unit', 'kind' => 'derived'],
                ['key' => 'state', 'label' => 'State', 'kind' => 'derived'],
                ['key' => 'severity', 'label' => 'Severity', 'kind' => 'select', 'options' => Options::SEVERITIES, 'required' => true],
                ['key' => 'count', 'label' => 'Count', 'kind' => 'number', 'required' => true],
                ['key' => 'hours_worked', 'label' => 'Hours worked (outlet, month)', 'kind' => 'number', 'required' => true],
                ['key' => 'days_lost', 'label' => 'Days lost', 'kind' => 'number'],
                ['key' => 'compliance_audit_score', 'label' => 'Compliance audit score (0-100)', 'kind' => 'number', 'required' => true],
            ],
            'columns' => ['month_key', 'outlet_id', 'severity', 'count', 'compliance_audit_score'],
            'defaults' => ['month_key' => Calendar::currentMonthKey()],
        ];
    }
}
