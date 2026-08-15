<?php

namespace App\Livewire\Entry;

use App\Livewire\Entry\Concerns\EntityCrudComponent;
use App\Models\AttendanceRecord;
use App\Support\Calendar;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Attendance extends EntityCrudComponent
{
    protected function config(): array
    {
        return [
            'model' => AttendanceRecord::class,
            'title' => 'Attendance & Leave',
            'description' => 'Monthly absenteeism, late clock-ins, and leave days consumed by type, per outlet/department.',
            'fields' => [
                ['key' => 'month_key', 'label' => 'Month', 'kind' => 'month', 'required' => true],
                ['key' => 'outlet_id', 'label' => 'Outlet', 'kind' => 'outlet', 'required' => true],
                ['key' => 'department', 'label' => 'Department', 'kind' => 'department', 'required' => true],
                ['key' => 'bu', 'label' => 'Business unit', 'kind' => 'derived'],
                ['key' => 'state', 'label' => 'State', 'kind' => 'derived'],
                ['key' => 'headcount', 'label' => 'Headcount', 'kind' => 'number', 'required' => true],
                ['key' => 'working_days', 'label' => 'Working days in month', 'kind' => 'number', 'required' => true],
                ['key' => 'absent_days', 'label' => 'Absent days (total)', 'kind' => 'number', 'required' => true],
                ['key' => 'late_clock_ins', 'label' => 'Late clock-ins (count)', 'kind' => 'number', 'required' => true],
                ['key' => 'leave_annual', 'label' => 'Annual leave (days)', 'kind' => 'number'],
                ['key' => 'leave_medical', 'label' => 'Medical leave (days)', 'kind' => 'number'],
                ['key' => 'leave_emergency', 'label' => 'Emergency leave (days)', 'kind' => 'number'],
                ['key' => 'leave_unpaid', 'label' => 'Unpaid leave (days)', 'kind' => 'number'],
                ['key' => 'leave_maternity_paternity', 'label' => 'Maternity/Paternity (days)', 'kind' => 'number'],
            ],
            'columns' => ['month_key', 'outlet_id', 'department', 'headcount', 'absent_days', 'late_clock_ins'],
            'defaults' => ['month_key' => Calendar::currentMonthKey()],
        ];
    }
}
