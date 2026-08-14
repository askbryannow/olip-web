import { useMemo } from 'react';
import { useFilters } from '../lib/FiltersContext';
import { getDataset } from '../data/generate';
import { BUSINESS_UNITS, EMPLOYMENT_TYPES, STATES } from '../data/outlets';

function Select({
  label,
  value,
  onChange,
  options,
}: {
  label: string;
  value: string;
  onChange: (v: string) => void;
  options: { value: string; label: string }[];
}) {
  return (
    <label className="flex flex-col gap-1 min-w-0">
      <span className="text-[10px] font-medium uppercase tracking-wide text-[var(--text-muted)]">{label}</span>
      <select
        value={value}
        onChange={(e) => onChange(e.target.value)}
        className="text-[12px] rounded-md border border-[var(--border-hairline)] bg-[var(--surface-card)] px-2 py-1.5 text-[var(--text-primary)] min-w-0 max-w-[180px] truncate focus:outline-none focus:ring-2 focus:ring-[var(--series-1)]"
      >
        {options.map((o) => (
          <option key={o.value} value={o.value}>
            {o.label}
          </option>
        ))}
      </select>
    </label>
  );
}

export function FilterBar() {
  const { filters, patchFilters, resetFilters } = useFilters();
  const dataset = getDataset();

  const outletOptions = useMemo(() => {
    return dataset.outlets
      .filter((o) => (filters.bu === 'ALL' || o.bu === filters.bu) && (filters.state === 'ALL' || o.state === filters.state))
      .map((o) => ({ value: o.id, label: o.name }));
  }, [dataset.outlets, filters.bu, filters.state]);

  const isFiltered =
    filters.monthKey !== 'ALL' || filters.bu !== 'ALL' || filters.state !== 'ALL' || filters.outletId !== 'ALL' || filters.employmentType !== 'ALL';

  return (
    <div className="flex flex-wrap items-end gap-3 border-b border-[var(--border-hairline)] bg-[var(--surface-1)] px-5 py-3 sticky top-0 z-20">
      <Select
        label="Month (as of)"
        value={filters.monthKey}
        onChange={(v) => patchFilters({ monthKey: v })}
        options={[{ value: 'ALL', label: 'Latest' }, ...dataset.calendar.map((c) => ({ value: c.key, label: c.label })).reverse()]}
      />
      <Select
        label="Business Unit"
        value={filters.bu}
        onChange={(v) => patchFilters({ bu: v as typeof filters.bu, outletId: 'ALL' })}
        options={[{ value: 'ALL', label: 'All BUs' }, ...BUSINESS_UNITS.map((b) => ({ value: b, label: b }))]}
      />
      <Select
        label="State"
        value={filters.state}
        onChange={(v) => patchFilters({ state: v as typeof filters.state, outletId: 'ALL' })}
        options={[{ value: 'ALL', label: 'All States' }, ...STATES.map((s) => ({ value: s, label: s }))]}
      />
      <Select
        label="Outlet"
        value={filters.outletId}
        onChange={(v) => patchFilters({ outletId: v })}
        options={[{ value: 'ALL', label: 'All Outlets' }, ...outletOptions]}
      />
      <Select
        label="Employment Type"
        value={filters.employmentType}
        onChange={(v) => patchFilters({ employmentType: v as typeof filters.employmentType })}
        options={[{ value: 'ALL', label: 'All Types' }, ...EMPLOYMENT_TYPES.map((e) => ({ value: e, label: e }))]}
      />
      {isFiltered && (
        <button
          onClick={resetFilters}
          className="text-[12px] font-medium text-[var(--series-1)] hover:underline ml-1 mb-1.5"
        >
          Clear filters
        </button>
      )}
    </div>
  );
}
