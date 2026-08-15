<?php

namespace App\Livewire\Entry;

use App\Models\Employee;
use App\Models\Outlet;
use App\Support\Options;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Employees extends Component
{
    public array $form = [];
    public ?int $editingId = null;
    public bool $showForm = false;
    public ?string $error = null;

    public array $genders = ['Male', 'Female'];
    public array $employmentTypes = Options::EMPLOYMENT_TYPES;
    public array $levels = Options::LEVELS;
    public array $exitTypes = Options::EXIT_TYPES;

    public function mount(): void
    {
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->form = [
            'name' => '', 'gender' => '', 'outlet_id' => '', 'department' => '', 'bu' => '', 'state' => '',
            'employment_type' => '', 'level' => '', 'join_date' => '', 'base_salary' => '',
            'exit_date' => '', 'exit_type' => '',
        ];
        $this->editingId = null;
        $this->error = null;
    }

    public function openAdd(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $e = Employee::findOrFail($id);
        $this->form = [
            'name' => $e->name, 'gender' => $e->gender, 'outlet_id' => $e->outlet_id, 'department' => $e->department,
            'bu' => $e->bu, 'state' => $e->state, 'employment_type' => $e->employment_type, 'level' => $e->level,
            'join_date' => $e->join_date?->format('Y-m-d'), 'base_salary' => $e->base_salary,
            'exit_date' => $e->exit_date?->format('Y-m-d'), 'exit_type' => $e->exit_type,
        ];
        $this->editingId = $id;
        $this->error = null;
        $this->showForm = true;
    }

    public function updatedFormOutletId(): void
    {
        $outlet = Outlet::find($this->form['outlet_id'] ?? null);
        if ($outlet) {
            $this->form['bu'] = $outlet->bu;
            $this->form['state'] = $outlet->state;
            $this->form['department'] = null;
        }
    }

    public function departmentOptions(): array
    {
        return Outlet::find($this->form['outlet_id'] ?? null)?->departments ?? [];
    }

    public function save(): void
    {
        foreach (['name', 'gender', 'outlet_id', 'department', 'employment_type', 'level', 'join_date', 'base_salary'] as $required) {
            if (empty($this->form[$required])) {
                $this->error = 'Please fill in all required fields.';

                return;
            }
        }

        $payload = $this->form;
        $payload['exit_date'] = $payload['exit_date'] ?: null;
        $payload['exit_type'] = $payload['exit_type'] ?: null;

        if ($this->editingId) {
            Employee::find($this->editingId)->update($payload);
        } else {
            Employee::create($payload);
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        Employee::destroy($id);
    }

    public function cancel(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.entry.employees', [
            'employees' => Employee::orderByDesc('id')->limit(300)->get(),
            'outlets' => Outlet::orderBy('name')->get(),
        ]);
    }
}
