export const axisTick = { fontSize: 11, fill: 'var(--text-muted)' };
export const gridStroke = 'var(--gridline)';
export const baselineStroke = 'var(--baseline)';

interface TooltipPayloadItem {
  dataKey?: string | number;
  name?: string | number;
  value?: number;
  color?: string;
}

interface ChartTooltipProps {
  active?: boolean;
  payload?: TooltipPayloadItem[];
  label?: string | number;
  formatter?: (v: number, name: string) => string;
}

export function ChartTooltip({ active, payload, label, formatter }: ChartTooltipProps) {
  if (!active || !payload || payload.length === 0) return null;
  return (
    <div className="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-card)] px-3 py-2 shadow-lg text-[12px]">
      {label !== undefined && <div className="mb-1 font-medium text-[var(--text-primary)]">{label}</div>}
      <div className="flex flex-col gap-0.5">
        {payload.map((p) => {
          const name = String(p.name ?? p.dataKey ?? '');
          const value = p.value ?? 0;
          return (
            <div key={String(p.dataKey)} className="flex items-center gap-2">
              <span className="inline-block h-2 w-2 rounded-full shrink-0" style={{ background: p.color }} />
              <span className="text-[var(--text-secondary)]">{name}:</span>
              <span className="font-medium tabular text-[var(--text-primary)]">{formatter ? formatter(value, name) : value}</span>
            </div>
          );
        })}
      </div>
    </div>
  );
}
