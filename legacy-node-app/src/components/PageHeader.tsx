export function PageHeader({ title, subtitle }: { title: string; subtitle?: string }) {
  return (
    <div className="mb-4">
      <h1 className="text-[19px] font-semibold text-[var(--text-primary)]">{title}</h1>
      {subtitle && <p className="text-[13px] text-[var(--text-muted)] mt-0.5">{subtitle}</p>}
    </div>
  );
}

export function KpiRow({ children }: { children: React.ReactNode }) {
  return <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-3 mb-5">{children}</div>;
}

export function ChartGrid({ children, cols = 3 }: { children: React.ReactNode; cols?: 1 | 2 | 3 }) {
  const colClass = cols === 1 ? 'grid-cols-1' : cols === 2 ? 'grid-cols-1 lg:grid-cols-2' : 'grid-cols-1 lg:grid-cols-3';
  return <div className={`grid ${colClass} gap-4`}>{children}</div>;
}
