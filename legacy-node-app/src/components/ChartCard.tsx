import type { ReactNode } from 'react';

interface ChartCardProps {
  title: string;
  subtitle?: string;
  children: ReactNode;
  className?: string;
  legend?: ReactNode;
  actions?: ReactNode;
}

export function ChartCard({ title, subtitle, children, className, legend, actions }: ChartCardProps) {
  return (
    <div className={`flex flex-col gap-3 rounded-lg border border-[var(--border-hairline)] bg-[var(--surface-card)] p-4 min-w-0 ${className ?? ''}`}>
      <div className="flex items-start justify-between gap-3">
        <div className="min-w-0">
          <h3 className="text-[13px] font-semibold text-[var(--text-primary)] truncate">{title}</h3>
          {subtitle && <p className="text-[12px] text-[var(--text-muted)] mt-0.5">{subtitle}</p>}
        </div>
        <div className="flex items-center gap-3 shrink-0">
          {legend}
          {actions}
        </div>
      </div>
      <div className="min-w-0">{children}</div>
    </div>
  );
}

export function Legend({ items }: { items: { label: string; color: string }[] }) {
  return (
    <div className="flex flex-wrap items-center gap-x-3 gap-y-1">
      {items.map((it) => (
        <div key={it.label} className="flex items-center gap-1.5">
          <span className="inline-block h-2 w-2 rounded-full" style={{ background: it.color }} />
          <span className="text-[11px] text-[var(--text-secondary)]">{it.label}</span>
        </div>
      ))}
    </div>
  );
}
