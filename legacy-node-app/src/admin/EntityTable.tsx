import { useEffect, useState } from 'react';
import { api } from '../lib/api';
import { EntityForm } from './EntityForm';
import { useOutlets } from './useOutlets';
import type { EntityConfig } from './types';
import { PageHeader } from '../components/PageHeader';

export function EntityTable({ config }: { config: EntityConfig }) {
  const outlets = useOutlets();
  const [rows, setRows] = useState<Record<string, unknown>[] | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [editing, setEditing] = useState<Record<string, unknown> | null>(null);
  const [showForm, setShowForm] = useState(false);

  async function load() {
    try {
      const data = await api.get<Record<string, unknown>[]>(config.endpoint);
      setRows(data);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to load');
    }
  }

  useEffect(() => {
    setRows(null);
    void load();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [config.endpoint]);

  async function handleSave(values: Record<string, unknown>) {
    const payload = config.transformBeforeSave ? config.transformBeforeSave(values) : values;
    if (editing?.id) {
      await api.put(`${config.endpoint}/${editing.id}`, payload);
    } else {
      await api.post(config.endpoint, payload);
    }
    setShowForm(false);
    setEditing(null);
    await load();
  }

  async function handleDelete(id: string) {
    if (!confirm('Delete this record?')) return;
    await api.del(`${config.endpoint}/${id}`);
    await load();
  }

  const outletName = (id: unknown) => outlets?.find((o) => o.id === id)?.name ?? id;
  const formatDepartments = (v: unknown) => {
    try {
      return (JSON.parse(v as string) as string[]).join(', ');
    } catch {
      return String(v ?? '');
    }
  };

  return (
    <div>
      <div className="flex items-start justify-between">
        <PageHeader title={config.title} subtitle={config.description} />
        <button
          onClick={() => {
            setEditing(null);
            setShowForm(true);
          }}
          className="rounded-md bg-[var(--series-1)] px-3 py-1.5 text-[13px] font-medium text-white"
        >
          + Add
        </button>
      </div>

      {error && <div className="mb-3 text-[12px] text-[var(--status-critical)]">{error}</div>}

      <div className="overflow-x-auto rounded-lg border border-[var(--border-hairline)] bg-[var(--surface-card)] scrollbar-thin">
        <table className="w-full min-w-[600px] border-collapse">
          <thead>
            <tr className="border-b border-[var(--border-hairline)]">
              {config.columns.map((c) => (
                <th key={c} className="px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)] whitespace-nowrap">
                  {config.fields.find((f) => f.key === c)?.label ?? c}
                </th>
              ))}
              <th className="px-3 py-2" />
            </tr>
          </thead>
          <tbody>
            {rows === null && (
              <tr>
                <td colSpan={config.columns.length + 1} className="px-3 py-6 text-center text-[12px] text-[var(--text-muted)]">
                  Loading…
                </td>
              </tr>
            )}
            {rows?.length === 0 && (
              <tr>
                <td colSpan={config.columns.length + 1} className="px-3 py-6 text-center text-[12px] text-[var(--text-muted)]">
                  No records yet.
                </td>
              </tr>
            )}
            {rows?.map((row) => (
              <tr key={row.id as string} className="border-b border-[var(--border-hairline)] last:border-0 hover:bg-[var(--surface-2)]">
                {config.columns.map((c) => (
                  <td key={c} className="px-3 py-2 text-[12px] text-[var(--text-secondary)] whitespace-nowrap tabular">
                    {c === 'outletId' ? String(outletName(row[c])) : c === 'departments' ? formatDepartments(row[c]) : String(row[c] ?? '')}
                  </td>
                ))}
                <td className="px-3 py-2 text-right whitespace-nowrap">
                  <button
                    onClick={() => {
                      setEditing(config.transformForEdit ? config.transformForEdit(row) : row);
                      setShowForm(true);
                    }}
                    className="text-[12px] font-medium text-[var(--series-1)] hover:underline mr-3"
                  >
                    Edit
                  </button>
                  <button
                    onClick={() => handleDelete(row.id as string)}
                    className="text-[12px] font-medium text-[var(--status-critical)] hover:underline"
                  >
                    Delete
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {showForm && (
        <EntityForm
          title={editing ? `Edit ${config.title}` : `Add ${config.title}`}
          fields={config.fields}
          initialValues={editing ?? config.defaultValues?.() ?? {}}
          outlets={outlets}
          onSubmit={handleSave}
          onCancel={() => {
            setShowForm(false);
            setEditing(null);
          }}
        />
      )}
    </div>
  );
}
