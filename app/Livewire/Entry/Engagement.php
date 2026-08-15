<?php

namespace App\Livewire\Entry;

use App\Livewire\Entry\Concerns\EntityCrudComponent;
use App\Models\EngagementRecord;
use App\Support\Calendar;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Engagement extends EntityCrudComponent
{
    protected function config(): array
    {
        return [
            'model' => EngagementRecord::class,
            'title' => 'Engagement Surveys',
            'description' => 'Quarterly engagement score, eNPS, grievances, and recognition by outlet.',
            'fields' => [
                ['key' => 'quarter_key', 'label' => 'Quarter (e.g. 2026-Q3)', 'kind' => 'text', 'required' => true],
                ['key' => 'month_key', 'label' => 'Representative month', 'kind' => 'month', 'required' => true],
                ['key' => 'outlet_id', 'label' => 'Outlet', 'kind' => 'outlet', 'required' => true],
                ['key' => 'bu', 'label' => 'Business unit', 'kind' => 'derived'],
                ['key' => 'state', 'label' => 'State', 'kind' => 'derived'],
                ['key' => 'headcount', 'label' => 'Headcount', 'kind' => 'number', 'required' => true],
                ['key' => 'respondents', 'label' => 'Survey respondents', 'kind' => 'number', 'required' => true],
                ['key' => 'engagement_score', 'label' => 'Engagement score (0-100)', 'kind' => 'number', 'required' => true],
                ['key' => 'e_nps', 'label' => 'eNPS (-100 to 100)', 'kind' => 'number', 'required' => true],
                ['key' => 'grievance_compensation', 'label' => 'Grievances: Compensation', 'kind' => 'number'],
                ['key' => 'grievance_conduct', 'label' => 'Grievances: Conduct', 'kind' => 'number'],
                ['key' => 'grievance_workload', 'label' => 'Grievances: Workload', 'kind' => 'number'],
                ['key' => 'grievance_facilities', 'label' => 'Grievances: Facilities', 'kind' => 'number'],
                ['key' => 'grievance_other', 'label' => 'Grievances: Other', 'kind' => 'number'],
                ['key' => 'recognitions_given', 'label' => 'Recognitions given', 'kind' => 'number', 'required' => true],
            ],
            'columns' => ['quarter_key', 'outlet_id', 'engagement_score', 'e_nps', 'recognitions_given'],
            'defaults' => ['quarter_key' => Calendar::currentQuarterKey(), 'month_key' => Calendar::currentMonthKey()],
        ];
    }
}
