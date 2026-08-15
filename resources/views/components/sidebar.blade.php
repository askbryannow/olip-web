@php
    $overviewNav = [
        ['route' => 'dashboard.exec', 'label' => 'Executive Overview'],
        ['route' => 'dashboard.business', 'label' => 'People & Business Performance'],
    ];
    $drillNav = [
        ['route' => 'dashboard.recruitment', 'label' => 'Recruitment'],
        ['route' => 'dashboard.turnover', 'label' => 'Turnover'],
        ['route' => 'dashboard.payroll', 'label' => 'Payroll & Overtime'],
        ['route' => 'dashboard.attendance', 'label' => 'Attendance & Leave'],
        ['route' => 'dashboard.learning', 'label' => 'Learning & Development'],
        ['route' => 'dashboard.engagement', 'label' => 'Engagement'],
        ['route' => 'dashboard.safety', 'label' => 'Safety'],
    ];
    $entryNav = [
        ['route' => 'entry.checklist', 'label' => 'Monthly Checklist'],
        ['route' => 'entry.employees', 'label' => 'Employees'],
        ['route' => 'entry.outlets', 'label' => 'Outlets'],
        ['route' => 'entry.overtime', 'label' => 'Overtime'],
        ['route' => 'entry.attendance', 'label' => 'Attendance & Leave'],
        ['route' => 'entry.recruitment', 'label' => 'Recruitment'],
        ['route' => 'entry.training', 'label' => 'Training & HRDC'],
        ['route' => 'entry.engagement', 'label' => 'Engagement Surveys'],
        ['route' => 'entry.incidents', 'label' => 'Safety Incidents'],
        ['route' => 'entry.business', 'label' => 'Business Performance'],
    ];

    $navLink = function (array $item) {
        $active = request()->routeIs($item['route']);
        $classes = $active
            ? 'border-[var(--series-1)] bg-[color-mix(in_srgb,var(--series-1)_10%,transparent)] text-[var(--text-primary)] font-medium'
            : 'border-transparent text-[var(--text-secondary)] hover:bg-[var(--surface-2)]';

        return '<a href="'.route($item['route']).'" class="w-full block text-left px-4 py-2 text-[13px] transition-colors border-l-2 '.$classes.'">'.e($item['label']).'</a>';
    };
@endphp

<aside class="w-56 shrink-0 border-r border-[var(--border-hairline)] bg-[var(--surface-1)] flex flex-col h-full">
    <div class="px-4 py-4 border-b border-[var(--border-hairline)]">
        <div class="text-[14px] font-semibold text-[var(--text-primary)]">People Analytics</div>
        <div class="text-[11px] text-[var(--text-muted)] mt-0.5">HR &amp; Workforce Dashboard</div>
    </div>
    <nav class="flex-1 overflow-y-auto py-3 scrollbar-thin">
        <div class="mb-3">
            <div class="px-4 pb-1 text-[10px] font-semibold uppercase tracking-wide text-[var(--text-muted)]">Overview</div>
            @foreach ($overviewNav as $item)
                {!! $navLink($item) !!}
            @endforeach
        </div>
        <div class="mb-3">
            <div class="px-4 pb-1 text-[10px] font-semibold uppercase tracking-wide text-[var(--text-muted)]">Drill-through</div>
            @foreach ($drillNav as $item)
                {!! $navLink($item) !!}
            @endforeach
        </div>
        <div class="mb-3">
            <div class="px-4 pb-1 text-[10px] font-semibold uppercase tracking-wide text-[var(--text-muted)]">Data Entry</div>
            @foreach ($entryNav as $item)
                {!! $navLink($item) !!}
            @endforeach
        </div>
    </nav>
    <div class="border-t border-[var(--border-hairline)] p-3">
        <form method="POST" action="{{ route('auth.logout') }}">
            @csrf
            <button type="submit" class="w-full rounded-md px-3 py-1.5 text-[12px] font-medium text-[var(--text-secondary)] hover:bg-[var(--surface-2)] text-left">
                Sign out
            </button>
        </form>
    </div>
</aside>
