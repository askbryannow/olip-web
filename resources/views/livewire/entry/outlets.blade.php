<div>
    <div class="flex items-start justify-between">
        <x-page-header title="Outlets" subtitle="The outlet/branch list used across every other table — usually set up once." />
        <button wire:click="openAdd" class="rounded-md bg-[var(--series-1)] px-3 py-1.5 text-[13px] font-medium text-white">+ Add</button>
    </div>

    <div class="overflow-x-auto rounded-lg border border-[var(--border-hairline)] bg-[var(--surface-card)] scrollbar-thin">
        <table class="w-full min-w-[700px] border-collapse">
            <thead>
                <tr class="border-b border-[var(--border-hairline)]">
                    <th class="px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Name</th>
                    <th class="px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Business Unit</th>
                    <th class="px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">State</th>
                    <th class="px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Departments</th>
                    <th class="px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Opened</th>
                    <th class="px-3 py-2"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($outlets as $o)
                    <tr class="border-b border-[var(--border-hairline)] last:border-0 hover:bg-[var(--surface-2)]">
                        <td class="px-3 py-2 text-[12px] text-[var(--text-secondary)] whitespace-nowrap">{{ $o->name }}</td>
                        <td class="px-3 py-2 text-[12px] text-[var(--text-secondary)] whitespace-nowrap">{{ $o->bu }}</td>
                        <td class="px-3 py-2 text-[12px] text-[var(--text-secondary)] whitespace-nowrap">{{ $o->state }}</td>
                        <td class="px-3 py-2 text-[12px] text-[var(--text-secondary)]">{{ implode(', ', $o->departments) }}</td>
                        <td class="px-3 py-2 text-[12px] text-[var(--text-secondary)] tabular">{{ $o->opened_year }}</td>
                        <td class="px-3 py-2 text-right whitespace-nowrap">
                            <button wire:click="openEdit('{{ $o->id }}')" class="text-[12px] font-medium text-[var(--series-1)] hover:underline mr-3">Edit</button>
                            <button wire:click="delete('{{ $o->id }}')" wire:confirm="Delete this outlet?" class="text-[12px] font-medium text-[var(--status-critical)] hover:underline">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if ($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 px-4">
            <form wire:submit="save" class="flex w-full max-w-lg flex-col gap-4 rounded-lg border border-[var(--border-hairline)] bg-[var(--surface-card)] p-5">
                <h2 class="text-[14px] font-semibold text-[var(--text-primary)]">{{ $editingId ? 'Edit' : 'Add' }} Outlet</h2>

                <div class="grid grid-cols-2 gap-3">
                    <label class="flex flex-col gap-1 col-span-2">
                        <span class="text-[11px] font-medium text-[var(--text-secondary)]">Name *</span>
                        <input type="text" wire:model="form.name" class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px]">
                    </label>
                    <label class="flex flex-col gap-1 col-span-2">
                        <span class="text-[11px] font-medium text-[var(--text-secondary)]">Business unit *</span>
                        <select wire:model="form.bu" class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px]">
                            <option value="">Select…</option>
                            @foreach ($businessUnits as $b)
                                <option value="{{ $b }}">{{ $b }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="flex flex-col gap-1 col-span-2">
                        <span class="text-[11px] font-medium text-[var(--text-secondary)]">State *</span>
                        <select wire:model="form.state" class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px]">
                            <option value="">Select…</option>
                            @foreach ($states as $s)
                                <option value="{{ $s }}">{{ $s }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="flex flex-col gap-1 col-span-2">
                        <span class="text-[11px] font-medium text-[var(--text-secondary)]">Departments (comma-separated) *</span>
                        <input type="text" wire:model="form.departments" placeholder="Sales, Store Operations, Customer Service"
                               class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px]">
                    </label>
                    <label class="flex flex-col gap-1">
                        <span class="text-[11px] font-medium text-[var(--text-secondary)]">Opened year</span>
                        <input type="number" wire:model="form.opened_year" class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px] tabular">
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
