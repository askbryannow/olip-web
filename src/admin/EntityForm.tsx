import { useState } from 'react';
import type { FieldConfig, OutletRow } from './types';
import { parseDepartments } from './types';

const EMPLOYMENT_TYPES = ['Permanent', 'Contract', 'Part-Time', 'Intern'];

interface EntityFormProps {
  title: string;
  fields: FieldConfig[];
  initialValues: Record<string, unknown>;
  outlets: OutletRow[] | null;
  onSubmit: (values: Record<string, unknown>) => Promise<void>;
  onCancel: () => void;
}

function toDateInputValue(v: unknown): string {
  if (typeof v !== 'string' || !v) return '';
  return v.slice(0, 10);
}

export function EntityForm({ title, fields, initialValues, outlets, onSubmit, onCancel }: EntityFormProps) {
  const [values, setValues] = useState<Record<string, unknown>>(initialValues);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const selectedOutlet = outlets?.find((o) => o.id === values.outletId);

  function setField(key: string, val: unknown) {
    setValues((prev) => {
      const next = { ...prev, [key]: val };
      if (key === 'outletId') {
        const outlet = outlets?.find((o) => o.id === val);
        if (outlet) {
          next.bu = outlet.bu;
          next.state = outlet.state;
          if (fields.some((f) => f.key === 'department')) next.department = '';
        }
      }
      return next;
    });
  }

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setSaving(true);
    setError(null);
    try {
      await onSubmit(values);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to save');
    } finally {
      setSaving(false);
    }
  }

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/30 px-4">
      <form onSubmit={handleSubmit} className="flex max-h-[85vh] w-full max-w-lg flex-col gap-4 overflow-y-auto rounded-lg border border-[var(--border-hairline)] bg-[var(--surface-card)] p-5 scrollbar-thin">
        <h2 className="text-[14px] font-semibold text-[var(--text-primary)]">{title}</h2>

        <div className="grid grid-cols-2 gap-3">
          {fields
            .filter((f) => f.kind !== 'derived')
            .map((f) => {
              const value = values[f.key];
              const full = f.kind === 'outlet' || f.kind === 'select';
              return (
                <label key={f.key} className={`flex flex-col gap-1 ${full ? 'col-span-2' : ''}`}>
                  <span className="text-[11px] font-medium text-[var(--text-secondary)]">
                    {f.label}
                    {f.required && ' *'}
                  </span>
                  {f.kind === 'outlet' ? (
                    <select
                      required={f.required}
                      value={(value as string) ?? ''}
                      onChange={(e) => setField(f.key, e.target.value)}
                      className="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px]"
                    >
                      <option value="">Select outlet…</option>
                      {outlets?.map((o) => (
                        <option key={o.id} value={o.id}>
                          {o.name}
                        </option>
                      ))}
                    </select>
                  ) : f.kind === 'department' ? (
                    <select
                      required={f.required}
                      value={(value as string) ?? ''}
                      onChange={(e) => setField(f.key, e.target.value)}
                      className="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px]"
                    >
                      <option value="">Select department…</option>
                      {(selectedOutlet ? parseDepartments(selectedOutlet) : []).map((d) => (
                        <option key={d} value={d}>
                          {d}
                        </option>
                      ))}
                    </select>
                  ) : f.kind === 'select' ? (
                    <select
                      required={f.required}
                      value={(value as string) ?? ''}
                      onChange={(e) => setField(f.key, e.target.value)}
                      className="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px]"
                    >
                      <option value="">Select…</option>
                      {(f.options ?? (f.key === 'employmentType' ? EMPLOYMENT_TYPES : [])).map((opt) => (
                        <option key={opt} value={opt}>
                          {opt}
                        </option>
                      ))}
                    </select>
                  ) : f.kind === 'month' ? (
                    <input
                      type="month"
                      required={f.required}
                      value={(value as string) ?? ''}
                      onChange={(e) => setField(f.key, e.target.value)}
                      className="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px]"
                    />
                  ) : f.kind === 'date' ? (
                    <input
                      type="date"
                      required={f.required}
                      value={toDateInputValue(value)}
                      onChange={(e) => setField(f.key, e.target.value)}
                      className="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px]"
                    />
                  ) : f.kind === 'number' ? (
                    <input
                      type="number"
                      step={f.step ?? 'any'}
                      required={f.required}
                      value={value === undefined || value === null ? '' : (value as number | string)}
                      onChange={(e) => setField(f.key, e.target.value === '' ? '' : Number(e.target.value))}
                      className="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px] tabular"
                    />
                  ) : (
                    <input
                      type="text"
                      required={f.required}
                      value={(value as string) ?? ''}
                      onChange={(e) => setField(f.key, e.target.value)}
                      className="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-2 py-1.5 text-[13px]"
                    />
                  )}
                </label>
              );
            })}
        </div>

        {error && <div className="text-[12px] text-[var(--status-critical)]">{error}</div>}

        <div className="flex justify-end gap-2 pt-1">
          <button type="button" onClick={onCancel} className="rounded-md px-3 py-1.5 text-[13px] text-[var(--text-secondary)] hover:bg-[var(--surface-2)]">
            Cancel
          </button>
          <button type="submit" disabled={saving} className="rounded-md bg-[var(--series-1)] px-3 py-1.5 text-[13px] font-medium text-white disabled:opacity-50">
            {saving ? 'Saving…' : 'Save'}
          </button>
        </div>
      </form>
    </div>
  );
}
