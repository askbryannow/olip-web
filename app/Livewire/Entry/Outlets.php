<?php

namespace App\Livewire\Entry;

use App\Models\Outlet;
use App\Support\Options;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Outlets extends Component
{
    public array $form = [];
    public ?string $editingId = null;
    public bool $showForm = false;
    public ?string $error = null;

    public array $businessUnits = Options::BUSINESS_UNITS;
    public array $states = Options::STATES;

    public function mount(): void
    {
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->form = ['id' => '', 'name' => '', 'bu' => '', 'state' => '', 'departments' => '', 'opened_year' => now()->year];
        $this->editingId = null;
        $this->error = null;
    }

    public function openAdd(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(string $id): void
    {
        $outlet = Outlet::findOrFail($id);
        $this->form = [
            'id' => $outlet->id, 'name' => $outlet->name, 'bu' => $outlet->bu, 'state' => $outlet->state,
            'departments' => implode(', ', $outlet->departments), 'opened_year' => $outlet->opened_year,
        ];
        $this->editingId = $id;
        $this->error = null;
        $this->showForm = true;
    }

    public function save(): void
    {
        if (! $this->form['name'] || ! $this->form['bu'] || ! $this->form['state'] || ! $this->form['departments']) {
            $this->error = 'Please fill in all required fields.';

            return;
        }

        $departments = array_values(array_filter(array_map('trim', explode(',', $this->form['departments']))));

        $payload = [
            'name' => $this->form['name'], 'bu' => $this->form['bu'], 'state' => $this->form['state'],
            'departments' => $departments, 'opened_year' => $this->form['opened_year'],
        ];

        if ($this->editingId) {
            Outlet::find($this->editingId)->update($payload);
        } else {
            $id = $this->form['id'] ?: \Illuminate\Support\Str::slug($this->form['name']);
            Outlet::create(['id' => $id] + $payload);
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function delete(string $id): void
    {
        Outlet::destroy($id);
    }

    public function cancel(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.entry.outlets', ['outlets' => Outlet::orderBy('name')->get()]);
    }
}
