<div>
    <div class="flex items-start justify-between">
        <x-page-header :title="$cfg['title']" :subtitle="$cfg['description']" />
        <button wire:click="openAdd" class="rounded-md bg-[var(--series-1)] px-3 py-1.5 text-[13px] font-medium text-white">+ Add</button>
    </div>

    <div class="overflow-x-auto rounded-lg border border-[var(--border-hairline)] bg-[var(--surface-card)] scrollbar-thin">
        <table class="w-full min-w-[600px] border-collapse">
            <thead>
                <tr class="border-b border-[var(--border-hairline)]">
                    @foreach ($cfg['columns'] as $col)
                        <th class="px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)] whitespace-nowrap">
                            {{ collect($cfg['fields'])->firstWhere('key', $col)['label'] ?? $col }}
                        </th>
                    @endforeach
                    <th class="px-3 py-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr class="border-b border-[var(--border-hairline)] last:border-0 hover:bg-[var(--surface-2)]">
                        @foreach ($cfg['columns'] as $col)
                            <td class="px-3 py-2 text-[12px] text-[var(--text-secondary)] whitespace-nowrap tabular">
                                @if ($col === 'outlet_id')
                                    {{ $outlets->firstWhere('id', $row->outlet_id)?->name ?? $row->outlet_id }}
                                @else
                                    {{ $row->{$col} }}
                                @endif
                            </td>
                        @endforeach
                        <td class="px-3 py-2 text-right whitespace-nowrap">
                            <button wire:click="openEdit({{ $row->id }})" class="text-[12px] font-medium text-[var(--series-1)] hover:underline mr-3">Edit</button>
                            <button wire:click="delete({{ $row->id }})" wire:confirm="Delete this record?" class="text-[12px] font-medium text-[var(--status-critical)] hover:underline">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($cfg['columns']) + 1 }}" class="px-3 py-6 text-center text-[12px] text-[var(--text-muted)]">No records yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 px-4">
            <form wire:submit="save" class="flex max-h-[85vh] w-full max-w-lg flex-col gap-4 overflow-y-auto rounded-lg border border-[var(--border-hairline)] bg-[var(--surface-card)] p-5 scrollbar-thin">
                <h2 class="text-[14px] font-semibold text-[var(--text-primary)]">{{ $editingId ? 'Edit' : 'Add' }} {{ $cfg['title'] }}</h2>

                <div class="grid grid-cols-2 gap-3">
                    @foreach ($cfg['fields'] as $f)
                        @continue($f['kind'] === 'derived')
                        <label class="flex flex-col gap-1 {{ in_array($f['kind'], ['outlet', 'select']) ? 'col-span-2' : '' }}">
                            <span class="text-[11px] font-medium text-[var(--text-secondary)]">{{ $f['label'] }}{{ $f['required'] ?? false ? ' *' : '' }}</span>

                            @if ($f['kind'] === 'outlet')
                                <select wire:model.live="form.{{ $f['key'] }}" class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px]">
                                    <option value="">Select outlet…</option>
                                    @foreach ($outlets as $o)
                                        <option value="{{ $o->id }}">{{ $o->name }}</option>
                                    @endforeach
                                </select>
                            @elseif ($f['kind'] === 'department')
                                <select wire:model="form.{{ $f['key'] }}" class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px]">
                                    <option value="">Select department…</option>
                                    @foreach ($this->departmentOptions() as $d)
                                        <option value="{{ $d }}">{{ $d }}</option>
                                    @endforeach
                                </select>
                            @elseif ($f['kind'] === 'select')
                                <select wire:model="form.{{ $f['key'] }}" class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px]">
                                    <option value="">Select…</option>
                                    @foreach ($f['options'] as $opt)
                                        <option value="{{ $opt }}">{{ $opt }}</option>
                                    @endforeach
                                </select>
                            @elseif ($f['kind'] === 'month')
                                <input type="month" wire:model="form.{{ $f['key'] }}" class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px]">
                            @elseif ($f['kind'] === 'date')
                                <input type="date" wire:model="form.{{ $f['key'] }}" class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px]">
                            @elseif ($f['kind'] === 'number')
                                <input type="number" step="any" wire:model="form.{{ $f['key'] }}" class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px] tabular">
                            @else
                                <input type="text" wire:model="form.{{ $f['key'] }}" class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px]">
                            @endif
                        </label>
                    @endforeach
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
