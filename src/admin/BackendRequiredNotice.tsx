export function BackendRequiredNotice({ title }: { title: string }) {
  return (
    <div>
      <h1 className="text-[19px] font-semibold text-[var(--text-primary)] mb-1">{title}</h1>
      <div className="mt-4 rounded-lg border border-[var(--border-hairline)] bg-[var(--surface-card)] p-6 max-w-lg">
        <p className="text-[13px] text-[var(--text-secondary)]">
          This preview is running without the API server, so there's no database to read or write. Data entry, employee records, and the monthly
          checklist all need the live backend.
        </p>
        <p className="text-[13px] text-[var(--text-secondary)] mt-3">
          Run <code className="rounded bg-[var(--surface-2)] px-1.5 py-0.5 text-[12px]">npm run dev:all</code> from the project locally to try the
          full flow: sign in, add real employees and monthly figures, and watch the dashboard pages update from them.
        </p>
      </div>
    </div>
  );
}
