<?php

namespace App\Livewire\Entry;

use App\Models\AttendanceRecord;
use App\Models\BusinessPerformanceRecord;
use App\Models\EngagementRecord;
use App\Models\IncidentRecord;
use App\Models\Outlet;
use App\Models\OvertimeRecord;
use App\Models\RecruitmentRecord;
use App\Models\TrainingRecord;
use App\Support\Calendar;
use App\Support\Options;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Checklist extends Component
{
    public function render()
    {
        $monthKey = Calendar::currentMonthKey();
        $quarterKey = Calendar::currentQuarterKey();
        $outlets = Outlet::orderBy('name')->get();

        $checks = [
            'Overtime' => OvertimeRecord::where('month_key', $monthKey)->pluck('outlet_id')->unique(),
            'Attendance & Leave' => AttendanceRecord::where('month_key', $monthKey)->pluck('outlet_id')->unique(),
            'Recruitment' => RecruitmentRecord::where('month_key', $monthKey)->pluck('outlet_id')->unique(),
            'Training & HRDC' => TrainingRecord::where('month_key', $monthKey)->pluck('outlet_id')->unique(),
            'Safety Incidents' => IncidentRecord::where('month_key', $monthKey)->pluck('outlet_id')->unique(),
            'Business Performance' => BusinessPerformanceRecord::where('month_key', $monthKey)->pluck('outlet_id')->unique(),
            'Engagement Survey' => EngagementRecord::where('quarter_key', $quarterKey)->pluck('outlet_id')->unique(),
        ];

        return view('livewire.entry.checklist', [
            'monthKey' => $monthKey,
            'outlets' => $outlets,
            'checks' => $checks,
            'operatingBUs' => Options::OPERATING_BUS,
        ]);
    }
}
