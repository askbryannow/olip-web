<?php

namespace App\Livewire\Entry;

use App\Livewire\Entry\Concerns\EntityCrudComponent;
use App\Models\RecruitmentRecord;
use App\Support\Calendar;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Recruitment extends EntityCrudComponent
{
    protected function config(): array
    {
        return [
            'model' => RecruitmentRecord::class,
            'title' => 'Recruitment',
            'description' => 'Monthly applicant funnel, vacancies, and hiring cost by outlet/department.',
            'fields' => [
                ['key' => 'month_key', 'label' => 'Month', 'kind' => 'month', 'required' => true],
                ['key' => 'outlet_id', 'label' => 'Outlet', 'kind' => 'outlet', 'required' => true],
                ['key' => 'department', 'label' => 'Department', 'kind' => 'department', 'required' => true],
                ['key' => 'bu', 'label' => 'Business unit', 'kind' => 'derived'],
                ['key' => 'state', 'label' => 'State', 'kind' => 'derived'],
                ['key' => 'applied', 'label' => 'Applied', 'kind' => 'number', 'required' => true],
                ['key' => 'screened', 'label' => 'Screened', 'kind' => 'number', 'required' => true],
                ['key' => 'interviewed', 'label' => 'Interviewed', 'kind' => 'number', 'required' => true],
                ['key' => 'offered', 'label' => 'Offered', 'kind' => 'number', 'required' => true],
                ['key' => 'joined', 'label' => 'Joined', 'kind' => 'number', 'required' => true],
                ['key' => 'open_vacancies', 'label' => 'Open vacancies', 'kind' => 'number', 'required' => true],
                ['key' => 'avg_vacancy_age_days', 'label' => 'Avg vacancy age (days)', 'kind' => 'number', 'required' => true],
                ['key' => 'avg_time_to_hire_days', 'label' => 'Avg time to hire (days)', 'kind' => 'number', 'required' => true],
                ['key' => 'cost_per_hire', 'label' => 'Cost per hire (MYR)', 'kind' => 'number', 'required' => true],
            ],
            'columns' => ['month_key', 'outlet_id', 'department', 'applied', 'joined', 'open_vacancies'],
            'defaults' => ['month_key' => Calendar::currentMonthKey()],
        ];
    }
}
