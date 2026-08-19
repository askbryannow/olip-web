<?php

namespace App\Livewire\Portal;

use App\Models\Employee;
use App\Models\Outlet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Portal account creation, in two steps: find your existing Employee record
 * (HR already holds your headcount data), then set a login for it. This
 * claims the record — it does not create a new one.
 */
#[Layout('layouts.portal')]
class Register extends Component
{
    public int $step = 1;

    public ?string $outletId = null;

    /** String, not int: bound directly to a <select>, whose empty placeholder option is "". */
    public ?string $employeeId = null;

    public string $email = '';

    public string $password = '';

    public string $passwordConfirmation = '';

    public ?string $error = null;

    public function updatedOutletId(): void
    {
        $this->employeeId = null;
    }

    public function chooseOutlet(): void
    {
        $this->error = null;

        if (! $this->outletId) {
            $this->error = 'Choose your outlet to continue.';

            return;
        }

        $this->step = 2;
    }

    public function chooseIdentity(): void
    {
        $this->error = null;

        if (! $this->employeeId) {
            $this->error = 'Select your name to continue.';

            return;
        }

        $this->step = 3;
    }

    public function back(): void
    {
        $this->error = null;
        $this->step = max(1, $this->step - 1);
    }

    public function unclaimedEmployees()
    {
        if (! $this->outletId) {
            return collect();
        }

        return Employee::query()
            ->where('outlet_id', $this->outletId)
            ->whereNull('exit_date')
            ->whereNull('email')
            ->orderBy('name')
            ->get();
    }

    public function register(): void
    {
        $this->error = null;

        $employee = Employee::query()->whereNull('email')->find($this->employeeId);

        if (! $employee) {
            $this->error = 'That record is no longer available — please pick your name again.';
            $this->step = 2;

            return;
        }

        if (! $this->email || ! str_contains($this->email, '@')) {
            $this->error = 'Enter a valid email address.';

            return;
        }

        if (Employee::query()->where('email', $this->email)->exists()) {
            $this->error = 'That email is already registered — try signing in instead.';

            return;
        }

        if (strlen($this->password) < 8) {
            $this->error = 'Password must be at least 8 characters.';

            return;
        }

        if ($this->password !== $this->passwordConfirmation) {
            $this->error = 'Passwords do not match.';

            return;
        }

        $employee->update([
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        Auth::guard('employee')->login($employee);
        session()->regenerate();

        $this->redirectRoute('portal.passport');
    }

    public function render()
    {
        return view('livewire.portal.register', [
            'outlets' => Outlet::orderBy('name')->get(),
            'employees' => $this->unclaimedEmployees(),
        ]);
    }
}
