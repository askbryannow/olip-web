<?php

namespace App\Livewire\Entry\Concerns;

use App\Models\Outlet;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Shared list + add/edit/delete behaviour for the monthly-record tables (Overtime,
 * Attendance, Recruitment, Training, Engagement, Incidents, Business Performance).
 * Subclasses just describe themselves via config(): model, title, and fields.
 *
 * Field shape: ['key' => 'overtime_hours', 'label' => 'Overtime hours', 'kind' => 'number'|'text'|'month'|'date'|'select'|'outlet'|'department'|'derived', 'options' => [...], 'required' => bool]
 */
#[Layout('layouts.app')]
abstract class EntityCrudComponent extends Component
{
    public array $form = [];
    public ?int $editingId = null;
    public bool $showForm = false;
    public ?string $error = null;

    /** @return array{model: class-string, title: string, description: string, fields: array, columns: array, defaults?: array} */
    abstract protected function config(): array;

    public function mount(): void
    {
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $cfg = $this->config();
        $this->form = collect($cfg['fields'])->mapWithKeys(fn ($f) => [$f['key'] => $cfg['defaults'][$f['key']] ?? null])->all();
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
        $cfg = $this->config();
        $model = $cfg['model']::findOrFail($id);
        $this->form = collect($cfg['fields'])->mapWithKeys(fn ($f) => [$f['key'] => $model->{$f['key']}])->all();
        $this->editingId = $id;
        $this->error = null;
        $this->showForm = true;
    }

    public function updatedFormOutletId(): void
    {
        // Auto-fill business unit & state from the chosen outlet, and clear a stale department.
        $outlet = Outlet::find($this->form['outlet_id'] ?? null);
        if ($outlet) {
            $this->form['bu'] = $outlet->bu;
            $this->form['state'] = $outlet->state;
            if (array_key_exists('department', $this->form)) {
                $this->form['department'] = null;
            }
        }
    }

    public function departmentOptions(): array
    {
        $outlet = Outlet::find($this->form['outlet_id'] ?? null);

        return $outlet?->departments ?? [];
    }

    public function save(): void
    {
        $cfg = $this->config();
        $required = collect($cfg['fields'])->where('required', true)->pluck('key');
        foreach ($required as $key) {
            if ($this->form[$key] === null || $this->form[$key] === '') {
                $this->error = 'Please fill in all required fields.';

                return;
            }
        }

        $payload = $this->form;

        if ($this->editingId) {
            $cfg['model']::find($this->editingId)->update($payload);
        } else {
            $cfg['model']::create($payload);
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $this->config()['model']::destroy($id);
    }

    public function cancel(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function render()
    {
        $cfg = $this->config();
        $rows = $cfg['model']::orderByDesc('id')->limit(200)->get();
        $outlets = Outlet::orderBy('name')->get();

        return view('livewire.entry.entity-crud', [
            'cfg' => $cfg,
            'rows' => $rows,
            'outlets' => $outlets,
        ]);
    }
}
