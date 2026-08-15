<div>
    <div class="flex items-start justify-between">
        <x-page-header title="Employees" subtitle="The employee master. To record a resignation or termination, edit the employee and set an exit date & type." />
        <button wire:click="openAdd" class="rounded-md bg-[var(--series-1)] px-3 py-1.5 text-[13px] font-medium text-white">+ Add</button>
    </div>

    <div class="overflow-x-auto rounded-lg border border-[var(--border-hairline)] bg-[var(--surface-card)] scrollbar-thin">
        <table class="w-full min-w-[800px] border-collapse">
            <thead>
                <tr class="border-b border-[var(--border-hairline)]">
                    <th class="px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Name</th>
                    <th class="px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Outlet</th>
                    <th class="px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Department</th>
                    <th class="px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Type</th>
                    <th class="px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Level</th>
                    <th class="px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Join Date</th>
                    <th class="px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Exit Date</th>
                    <th class="px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Base Salary</th>
                    <th class="px-3 py-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($employees as $e)
                    <tr class="border-b border-[var(--border-hairline)] last:border-0 hover:bg-[var(--surface-2)]">
                        <td class="px-3 py-2 text-[12px] text-[var(--text-secondary)] whitespace-nowrap">{{ $e->name }}</td>
                        <td class="px-3 py-2 text-[12px] text-[var(--text-secondary)] whitespace-nowrap">{{ $outlets->firstWhere('id', $e->outlet_id)?->name }}</td>
                        <td class="px-3 py-2 text-[12px] text-[var(--text-secondary)] whitespace-nowrap">{{ $e->department }}</td>
                        <td class="px-3 py-2 text-[12px] text-[var(--text-secondary)] whitespace-nowrap">{{ $e->employment_type }}</td>
                        <td class="px-3 py-2 text-[12px] text-[var(--text-secondary)] whitespace-nowrap">{{ $e->level }}</td>
                        <td class="px-3 py-2 text-[12px] text-[var(--text-secondary)] tabular whitespace-nowrap">{{ $e->join_date?->format('Y-m-d') }}</td>
                        <td class="px-3 py-2 text-[12px] text-[var(--text-secondary)] tabular whitespace-nowrap">{{ $e->exit_date?->format('Y-m-d') }}</td>
                        <td class="px-3 py-2 text-[12px] text-[var(--text-secondary)] tabular whitespace-nowrap">{{ number_format($e->base_salary, 2) }}</td>
                        <td class="px-3 py-2 text-right whitespace-nowrap">
                            <button wire:click="openEdit({{ $e->id }})" class="text-[12px] font-medium text-[var(--series-1)] hover:underline mr-3">Edit</button>
                            <button wire:click="delete({{ $e->id }})" wire:confirm="Delete this employee? This removes their entire history from the dashboard." class="text-[12px] font-medium text-[var(--status-critical)] hover:underline">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="px-3 py-6 text-center text-[12px] text-[var(--text-muted)]">No employees yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 px-4">
            <form wire:submit="save" class="flex max-h-[85vh] w-full max-w-lg flex-col gap-4 overflow-y-auto rounded-lg border border-[var(--border-hairline)] bg-[var(--surface-card)] p-5 scrollbar-thin">
                <h2 class="text-[14px] font-semibold text-[var(--text-primary)]">{{ $editingId ? 'Edit' : 'Add' }} Employee</h2>

                <div class="grid grid-cols-2 gap-3">
                    <label class="flex flex-col gap-1 col-span-2">
                        <span class="text-[11px] font-medium text-[var(--text-secondary)]">Name *</span>
                        <input type="text" wire:model="form.name" class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px]">
                    </label>
                    <label class="flex flex-col gap-1">
                        <span class="text-[11px] font-medium text-[var(--text-secondary)]">Gender *</span>
                        <select wire:model="form.gender" class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px]">
                            <option value="">Select…</option>
                            @foreach ($genders as $g)<option value="{{ $g }}">{{ $g }}</option>@endforeach
                        </select>
                    </label>
                    <label class="flex flex-col gap-1">
                        <span class="text-[11px] font-medium text-[var(--text-secondary)]">Employment type *</span>
                        <select wire:model="form.employment_type" class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px]">
                            <option value="">Select…</option>
                            @foreach ($employmentTypes as $t)<option value="{{ $t }}">{{ $t }}</option>@endforeach
                        </select>
                    </label>
                    <label class="flex flex-col gap-1 col-span-2">
                        <span class="text-[11px] font-medium text-[var(--text-secondary)]">Outlet *</span>
                        <select wire:model.live="form.outlet_id" class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px]">
                            <option value="">Select outlet…</option>
                            @foreach ($outlets as $o)<option value="{{ $o->id }}">{{ $o->name }}</option>@endforeach
                        </select>
                    </label>
                    <label class="flex flex-col gap-1">
                        <span class="text-[11px] font-medium text-[var(--text-secondary)]">Department *</span>
                        <select wire:model="form.department" class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px]">
                            <option value="">Select department…</option>
                            @foreach ($this->departmentOptions() as $d)<option value="{{ $d }}">{{ $d }}</option>@endforeach
                        </select>
                    </label>
                    <label class="flex flex-col gap-1">
                        <span class="text-[11px] font-medium text-[var(--text-secondary)]">Level *</span>
                        <select wire:model="form.level" class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px]">
                            <option value="">Select…</option>
                            @foreach ($levels as $l)<option value="{{ $l }}">{{ $l }}</option>@endforeach
                        </select>
                    </label>
                    <label class="flex flex-col gap-1">
                        <span class="text-[11px] font-medium text-[var(--text-secondary)]">Join date *</span>
                        <input type="date" wire:model="form.join_date" class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px]">
                    </label>
                    <label class="flex flex-col gap-1">
                        <span class="text-[11px] font-medium text-[var(--text-secondary)]">Base salary (MYR/mo) *</span>
                        <input type="number" step="any" wire:model="form.base_salary" class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px] tabular">
                    </label>
                    <label class="flex flex-col gap-1">
                        <span class="text-[11px] font-medium text-[var(--text-secondary)]">Exit date</span>
                        <input type="date" wire:model="form.exit_date" class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px]">
                    </label>
                    <label class="flex flex-col gap-1">
                        <span class="text-[11px] font-medium text-[var(--text-secondary)]">Exit type</span>
                        <select wire:model="form.exit_type" class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px]">
                            <option value="">Select…</option>
                            @foreach ($exitTypes as $t)<option value="{{ $t }}">{{ $t }}</option>@endforeach
                        </select>
                    </label>
                </div>

                @if ($error)
                    <div class="text-[12px] text-[var(--status-critical)]">{{ $error }}</div>
                @endif

                <div class="flex justify-end gap-2 pt-1">
                    <button type="button" wire:click="cancel" class="rounded-md px-3 py-1.5 text-[13px] text-[var(--text-secondary)] hover:bg-[var(--surface-2)]">Cancel</button>
                    <button type="submit" class="rounded-md bg-[var(--series-1)] px-3 py-1.5 text-[13px] font-medium text-white">Save</button>
                </div>
            </form>
        </div>
    @endif
</div>
