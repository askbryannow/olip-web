<?php

namespace App\Livewire\Portal;

use App\Models\BlipEnrollment;
use App\Support\Options;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

/** The Interest Form, as a short step-by-step "booking" flow rather than one long page. */
#[Layout('layouts.portal')]
class Enroll extends Component
{
    public int $step = 1;

    public bool $confirmConduct = false;

    public string $reason = '';

    public ?string $error = null;

    public function mount(): void
    {
        if (Auth::guard('employee')->user()->blipEnrollment()->exists()) {
            $this->redirectRoute('portal.passport');
        }
    }

    public function tenureMonths(): int
    {
        $employee = Auth::guard('employee')->user();

        return (int) $employee->join_date->diffInMonths(now());
    }

    public function isEligible(): bool
    {
        return $this->tenureMonths() >= Options::BLIP_MIN_TENURE_MONTHS;
    }

    public function next(): void
    {
        $this->error = null;

        if ($this->step === 1) {
            if (! $this->isEligible()) {
                $this->error = 'You need at least '.Options::BLIP_MIN_TENURE_MONTHS.' months in your current role to register.';

                return;
            }
            if (! $this->confirmConduct) {
                $this->error = 'Please confirm the statement above to continue.';

                return;
            }
        }

        if ($this->step === 2 && trim($this->reason) === '') {
            $this->error = 'Tell us in a sentence why you want to join.';

            return;
        }

        $this->step++;
    }

    public function back(): void
    {
        $this->error = null;
        $this->step = max(1, $this->step - 1);
    }

    public function submit(): void
    {
        $employee = Auth::guard('employee')->user();

        if ($employee->blipEnrollment()->exists()) {
            $this->redirectRoute('portal.passport');

            return;
        }

        BlipEnrollment::create([
            'employee_id' => $employee->id,
            'status' => 'interest_submitted',
            'reason' => $this->reason,
            'tenure_months_at_registration' => $this->tenureMonths(),
        ]);

        $this->redirectRoute('portal.passport');
    }

    public function render()
    {
        return view('livewire.portal.enroll', [
            'employee' => Auth::guard('employee')->user(),
        ]);
    }
}
