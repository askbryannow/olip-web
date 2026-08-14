import { seqStep } from '../lib/colors';

interface HeatmapProps {
  rows: string[];
  cols: string[];
  value: (row: string, col: string) => number;
  formatter?: (v: number) => string;
}

export function Heatmap({ rows, cols, value, formatter }: HeatmapProps) {
  const values = rows.flatMap((r) => cols.map((c) => value(r, c)));
  const min = Math.min(...values);
  const max = Math.max(...values);

  return (
    <div className="overflow-x-auto scrollbar-thin">
      <table className="border-collapse w-full min-w-[520px]">
        <thead>
          <tr>
            <th className="text-left text-[11px] font-medium text-[var(--text-muted)] pb-1 pr-2 sticky left-0 bg-[var(--surface-card)]"></th>
            {cols.map((c) => (
              <th key={c} className="text-[11px] font-medium text-[var(--text-muted)] pb-1 px-1 text-center whitespace-nowrap">
                {c}
              </th>
            ))}
          </tr>
        </thead>
        <tbody>
          {rows.map((r) => (
            <tr key={r}>
              <td className="text-[11px] text-[var(--text-secondary)] pr-2 py-0.5 whitespace-nowrap sticky left-0 bg-[var(--surface-card)]">{r}</td>
              {cols.map((c) => {
                const v = value(r, c);
                const bg = seqStep(v, min, max);
                const light = max === min ? true : (v - min) / (max - min) < 0.6;
                return (
                  <td key={c} className="p-0.5">
                    <div
                      className="h-7 min-w-[34px] rounded-sm flex items-center justify-center text-[10px] font-medium tabular"
                      style={{ background: bg, color: light ? '#0b0b0b' : '#ffffff' }}
                      title={`${r} · ${c}: ${formatter ? formatter(v) : v}`}
                    >
                      {formatter ? formatter(v) : v}
                    </div>
                  </td>
                );
              })}
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
