interface FunnelStage {
  label: string;
  value: number;
}

export function Funnel({ stages }: { stages: FunnelStage[] }) {
  const max = Math.max(...stages.map((s) => s.value), 1);
  return (
    <div className="flex flex-col gap-2">
      {stages.map((s, i) => {
        const pct = (s.value / max) * 100;
        const barPct = Math.max(pct, 4);
        const labelOutside = barPct < 12;
        const convFromPrev = i > 0 && stages[i - 1].value > 0 ? (s.value / stages[i - 1].value) * 100 : null;
        return (
          <div key={s.label} className="flex items-center gap-3">
            <div className="w-28 shrink-0 text-[12px] text-[var(--text-secondary)]">{s.label}</div>
            <div className="flex-1 h-7 rounded bg-[var(--surface-2)] relative">
              <div
                className={`h-full rounded flex items-center ${labelOutside ? '' : 'justify-end pr-2'}`}
                style={{ width: `${barPct}%`, background: 'var(--series-1)' }}
              >
                {!labelOutside && <span className="text-[11px] font-medium text-white tabular">{s.value.toLocaleString('en-MY')}</span>}
              </div>
              {labelOutside && (
                <span
                  className="absolute top-1/2 -translate-y-1/2 text-[11px] font-medium text-[var(--text-primary)] tabular"
                  style={{ left: `calc(${barPct}% + 6px)` }}
                >
                  {s.value.toLocaleString('en-MY')}
                </span>
              )}
            </div>
            <div className="w-14 shrink-0 text-[11px] text-[var(--text-muted)] tabular text-right">
              {convFromPrev !== null ? `${convFromPrev.toFixed(0)}%` : ''}
            </div>
          </div>
        );
      })}
    </div>
  );
}
