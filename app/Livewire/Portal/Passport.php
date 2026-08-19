<?php

namespace App\Livewire\Portal;

use App\Models\BlipEmbassy;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.portal')]
class Passport extends Component
{
    public function render()
    {
        $employee = Auth::guard('employee')->user();
        $enrollment = $employee->blipEnrollment()->with('stamps')->first();
        $embassies = BlipEmbassy::orderBy('sequence')->get();

        $stampsByEmbassy = $enrollment
            ? $enrollment->stamps->keyBy('embassy_id')
            : collect();

        return view('livewire.portal.passport', [
            'employee' => $employee,
            'enrollment' => $enrollment,
            'embassies' => $embassies,
            'stampsByEmbassy' => $stampsByEmbassy,
            'earnedCount' => $enrollment ? $enrollment->stampsEarnedCount() : 0,
            'stage' => $enrollment ? $enrollment->passportStage() : null,
        ]);
    }
}
