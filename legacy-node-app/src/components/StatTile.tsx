interface StatTileProps {
  label: string;
  value: string;
  delta?: string;
  deltaGood?: boolean | null; // true = good (green), false = bad (red), null/undefined = neutral
  sublabel?: string;
}

export function StatTile({ label, value, delta, deltaGood, sublabel }: StatTileProps) {
  return (
    <div className="flex flex-col gap-1.5 rounded-lg border border-[var(--border-hairline)] bg-[var(--surface-card)] px-4 py-3.5 min-w-0">
      <div className="text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)] truncate">{label}</div>
      <div className="text-[26px] leading-none font-semibold text-[var(--text-primary)] tabular">{value}</div>
      <div className="flex items-center gap-1.5 min-h-[16px]">
        {delta && (
          <span
            className="text-[12px] font-medium tabular"
            style={{ color: deltaGood === null || deltaGood === undefined ? 'var(--text-secondary)' : deltaGood ? 'var(--success-text)' : 'var(--status-critical)' }}
          >
            {delta}
          </span>
        )}
        {sublabel && <span className="text-[12px] text-[var(--text-muted)] truncate">{sublabel}</span>}
      </div>
    </div>
  );
}
