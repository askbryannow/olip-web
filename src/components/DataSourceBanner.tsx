import { useAppData } from '../lib/AppDataContext';

export function DataSourceBanner() {
  const { source } = useAppData();
  if (source === 'live') return null;

  const message =
    source === 'demo'
      ? 'No records in the database yet — showing demo data. Add real data under Data Entry, then refresh.'
      : "Backend not reachable — showing demo data. Run the API server to enter and load your own data.";

  return (
    <div className="border-b border-[var(--border-hairline)] bg-[color-mix(in_srgb,var(--status-warning)_14%,var(--surface-1))] px-5 py-2 text-[12px] text-[var(--text-primary)]">
      {message}
    </div>
  );
}
