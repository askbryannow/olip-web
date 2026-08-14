export type PageKey =
  | 'exec'
  | 'recruitment'
  | 'turnover'
  | 'payroll'
  | 'attendance'
  | 'learning'
  | 'engagement'
  | 'safety'
  | 'business';

const NAV: { key: PageKey; label: string; group: string }[] = [
  { key: 'exec', label: 'Executive Overview', group: 'Overview' },
  { key: 'business', label: 'People & Business Performance', group: 'Overview' },
  { key: 'recruitment', label: 'Recruitment', group: 'Drill-through' },
  { key: 'turnover', label: 'Turnover', group: 'Drill-through' },
  { key: 'payroll', label: 'Payroll & Overtime', group: 'Drill-through' },
  { key: 'attendance', label: 'Attendance & Leave', group: 'Drill-through' },
  { key: 'learning', label: 'Learning & Development', group: 'Drill-through' },
  { key: 'engagement', label: 'Engagement', group: 'Drill-through' },
  { key: 'safety', label: 'Safety', group: 'Drill-through' },
];

export function Sidebar({ page, onNavigate }: { page: PageKey; onNavigate: (p: PageKey) => void }) {
  const groups = ['Overview', 'Drill-through'];
  return (
    <aside className="w-56 shrink-0 border-r border-[var(--border-hairline)] bg-[var(--surface-1)] flex flex-col h-full">
      <div className="px-4 py-4 border-b border-[var(--border-hairline)]">
        <div className="text-[14px] font-semibold text-[var(--text-primary)]">People Analytics</div>
        <div className="text-[11px] text-[var(--text-muted)] mt-0.5">HR &amp; Workforce Dashboard</div>
      </div>
      <nav className="flex-1 overflow-y-auto py-3 scrollbar-thin">
        {groups.map((g) => (
          <div key={g} className="mb-3">
            <div className="px-4 pb-1 text-[10px] font-semibold uppercase tracking-wide text-[var(--text-muted)]">{g}</div>
            {NAV.filter((n) => n.group === g).map((n) => (
              <button
                key={n.key}
                onClick={() => onNavigate(n.key)}
                className={`w-full text-left px-4 py-2 text-[13px] transition-colors border-l-2 ${
                  page === n.key
                    ? 'border-[var(--series-1)] bg-[color-mix(in_srgb,var(--series-1)_10%,transparent)] text-[var(--text-primary)] font-medium'
                    : 'border-transparent text-[var(--text-secondary)] hover:bg-[var(--surface-2)]'
                }`}
              >
                {n.label}
              </button>
            ))}
          </div>
        ))}
      </nav>
    </aside>
  );
}

export { NAV };
