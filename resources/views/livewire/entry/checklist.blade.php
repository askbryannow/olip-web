<div>
    <x-page-header title="Monthly Data Checklist" :subtitle="'What\'s still missing for '.$monthKey.' before you regenerate the dashboard'" />

    <div class="overflow-x-auto rounded-lg border border-[var(--border-hairline)] bg-[var(--surface-card)] scrollbar-thin">
        <table class="w-full min-w-[700px] border-collapse">
            <thead>
                <tr class="border-b border-[var(--border-hairline)]">
                    <th class="px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Outlet</th>
                    @foreach ($checks as $label => $ids)
                        <th class="px-3 py-2 text-center text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)] whitespace-nowrap">{{ $label }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($outlets as $o)
                    @php $isOperating = in_array($o->bu, $operatingBUs, true); @endphp
                    <tr class="border-b border-[var(--border-hairline)] last:border-0">
                        <td class="px-3 py-2 text-[12px] text-[var(--text-secondary)] whitespace-nowrap">{{ $o->name }}</td>
                        @foreach ($checks as $label => $ids)
                            <td class="px-3 py-2 text-center">
                                @if ($label === 'Business Performance' && ! $isOperating)
                                    <span class="text-[12px] text-[var(--text-muted)]">n/a</span>
                                @else
                                    @php $done = $ids->contains($o->id); @endphp
                                    <span class="inline-block rounded-full px-2 py-0.5 text-[11px] font-medium"
                                          style="background: {{ $done ? 'color-mix(in srgb, var(--status-good) 15%, transparent)' : 'color-mix(in srgb, var(--status-warning) 20%, transparent)' }}; color: {{ $done ? 'var(--status-good)' : 'var(--text-primary)' }}">
                                        {{ $done ? 'Done' : 'Missing' }}
                                    </span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <p class="mt-3 text-[12px] text-[var(--text-muted)]">
        Employee master updates (new hires, resignations) don't need a monthly entry here — they're captured whenever they happen on the Employees page.
    </p>
</div>
