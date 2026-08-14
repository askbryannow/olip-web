import { useEffect, useState } from 'react';
import { api } from '../lib/api';
import { useOutlets } from './useOutlets';
import { currentMonthKey } from './types';
import { PageHeader } from '../components/PageHeader';
import { useAppData } from '../lib/AppDataContext';

interface Check {
  label: string;
  endpoint: string;
  key: string; // monthKey or quarterKey
  scopedOutlets?: string[]; // if set, only these outlets are expected (e.g. business excludes HQ)
}

function currentQuarterKey(): string {
  const d = new Date();
  return `${d.getFullYear()}-Q${Math.floor(d.getMonth() / 3) + 1}`;
}

export function ChecklistPage() {
  const outlets = useOutlets();
  const { refresh } = useAppData();
  const monthKey = currentMonthKey();
  const [rows, setRows] = useState<Record<string, Set<string>> | null>(null);

  const checks: Check[] = [
    { label: 'Overtime', endpoint: '/overtime', key: monthKey },
    { label: 'Attendance & Leave', endpoint: '/attendance', key: monthKey },
    { label: 'Recruitment', endpoint: '/recruitment', key: monthKey },
    { label: 'Training & HRDC', endpoint: '/training', key: monthKey },
    { label: 'Safety Incidents', endpoint: '/incidents', key: monthKey },
    { label: 'Business Performance', endpoint: '/business', key: monthKey },
    { label: 'Engagement Survey', endpoint: '/engagement', key: currentQuarterKey() },
  ];

  useEffect(() => {
    void (async () => {
      const entries = await Promise.all(
        checks.map(async (c) => {
          const data = await api.get<Record<string, unknown>[]>(`${c.endpoint}`);
          const monthField = c.endpoint === '/engagement' ? 'quarterKey' : 'monthKey';
          const covered = new Set(data.filter((r) => r[monthField] === c.key).map((r) => r.outletId as string));
          return [c.label, covered] as const;
        }),
      );
      setRows(Object.fromEntries(entries));
    })();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [monthKey]);

  const operatingOutlets = outlets?.filter((o) => o.bu === 'Retail Stores' || o.bu === 'F&B Outlets' || o.bu === 'Warehouse & Logistics') ?? [];
  const allOutlets = outlets ?? [];

  return (
    <div>
      <div className="flex items-start justify-between">
        <PageHeader title="Monthly Data Checklist" subtitle={`What's still missing for ${monthKey} before you regenerate the dashboard`} />
        <button onClick={() => void refresh()} className="rounded-md border border-[var(--border-hairline)] px-3 py-1.5 text-[13px] font-medium text-[var(--text-secondary)] hover:bg-[var(--surface-2)]">
          Refresh dashboard data
        </button>
      </div>

      {(!outlets || !rows) && <div className="text-[13px] text-[var(--text-muted)]">Loading…</div>}

      {outlets && rows && (
        <div className="overflow-x-auto rounded-lg border border-[var(--border-hairline)] bg-[var(--surface-card)] scrollbar-thin">
          <table className="w-full min-w-[700px] border-collapse">
            <thead>
              <tr className="border-b border-[var(--border-hairline)]">
                <th className="px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Outlet</th>
                {checks.map((c) => (
                  <th key={c.label} className="px-3 py-2 text-center text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)] whitespace-nowrap">
                    {c.label}
                  </th>
                ))}
              </tr>
            </thead>
            <tbody>
              {(checks[5] ? allOutlets : operatingOutlets).map((o) => {
                const isOperating = o.bu === 'Retail Stores' || o.bu === 'F&B Outlets' || o.bu === 'Warehouse & Logistics';
                return (
                  <tr key={o.id} className="border-b border-[var(--border-hairline)] last:border-0">
                    <td className="px-3 py-2 text-[12px] text-[var(--text-secondary)] whitespace-nowrap">{o.name}</td>
                    {checks.map((c) => {
                      if (c.label === 'Business Performance' && !isOperating) {
                        return (
                          <td key={c.label} className="px-3 py-2 text-center text-[12px] text-[var(--text-muted)]">
                            n/a
                          </td>
                        );
                      }
                      const done = rows[c.label]?.has(o.id);
                      return (
                        <td key={c.label} className="px-3 py-2 text-center">
                          <span
                            className="inline-block rounded-full px-2 py-0.5 text-[11px] font-medium"
                            style={{
                              background: done ? 'color-mix(in srgb, var(--status-good) 15%, transparent)' : 'color-mix(in srgb, var(--status-warning) 20%, transparent)',
                              color: done ? 'var(--status-good)' : 'var(--text-primary)',
                            }}
                          >
                            {done ? 'Done' : 'Missing'}
                          </span>
                        </td>
                      );
                    })}
                  </tr>
                );
              })}
            </tbody>
          </table>
        </div>
      )}

      <p className="mt-3 text-[12px] text-[var(--text-muted)]">
        Employee master updates (new hires, resignations) don't need a monthly entry here — they're captured whenever they happen on the Employees page.
        Once everything is "Done", click "Refresh dashboard data" or reload the app to see the new month reflected.
      </p>
    </div>
  );
}
