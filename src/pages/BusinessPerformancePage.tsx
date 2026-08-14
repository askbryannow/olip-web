import { CartesianGrid, Line, LineChart, ResponsiveContainer, Tooltip, XAxis, YAxis } from 'recharts';
import { useFilters } from '../lib/FiltersContext';
import { PageHeader, KpiRow, ChartGrid } from '../components/PageHeader';
import { StatTile } from '../components/StatTile';
import { ChartCard, Legend } from '../components/ChartCard';
import { ChartTooltip, axisTick, gridStroke } from '../components/ChartTheme';
import { fmtMYR, fmtNum, fmtPct } from '../lib/filters';
import { businessTrend, endMonth } from '../lib/selectors';
import { getDataset } from '../data/store';

export function BusinessPerformancePage() {
  const { filters } = useFilters();
  const ds = getDataset();
  const cm = endMonth(filters, ds.calendar);

  const trend = businessTrend(filters, 12);
  const base = trend[0] ?? { revenue: 1, visitors: 1, headcount: 1, labourCost: 1 };
  const indexed = trend.map((t) => ({
    month: t.month,
    Revenue: base.revenue > 0 ? (t.revenue / base.revenue) * 100 : 100,
    Visitors: base.visitors > 0 ? (t.visitors / base.visitors) * 100 : 100,
    Headcount: base.headcount > 0 ? (t.headcount / base.headcount) * 100 : 100,
    'Labour Cost': base.labourCost > 0 ? (t.labourCost / base.labourCost) * 100 : 100,
  }));

  const latest = trend.slice(-1)[0];
  const revenuePerEmployee = latest && latest.headcount > 0 ? latest.revenue / latest.headcount : 0;
  const labourCostPctRevenue = latest && latest.revenue > 0 ? (latest.labourCost / latest.revenue) * 100 : 0;
  const revenuePerVisitor = latest && latest.visitors > 0 ? latest.revenue / latest.visitors : 0;

  const revTrend = trend.map((t) => ({ month: t.month, revenuePerEmployee: t.headcount > 0 ? t.revenue / t.headcount : 0 }));

  return (
    <div>
      <PageHeader title="People & Business Performance" subtitle="Revenue and footfall against headcount and labour cost — the exec tie-out" />

      <KpiRow>
        <StatTile label="Revenue" value={fmtMYR(latest?.revenue ?? 0)} sublabel={cm.label} />
        <StatTile label="Visitors" value={fmtNum(latest?.visitors ?? 0)} sublabel={cm.label} />
        <StatTile label="Revenue / Employee" value={fmtMYR(revenuePerEmployee)} sublabel={cm.label} />
        <StatTile label="Labour Cost % of Revenue" value={fmtPct(labourCostPctRevenue)} sublabel={cm.label} deltaGood={labourCostPctRevenue < 35} />
        <StatTile label="Revenue / Visitor" value={fmtMYR(revenuePerVisitor, false)} sublabel={cm.label} />
      </KpiRow>

      <ChartGrid cols={1}>
        <ChartCard
          title="Revenue, Visitors, Headcount & Labour Cost — Indexed"
          subtitle={`Indexed to 100 at ${trend[0]?.month ?? ''} so growth rates compare on one scale`}
          legend={
            <Legend
              items={[
                { label: 'Revenue', color: 'var(--series-1)' },
                { label: 'Visitors', color: 'var(--series-3)' },
                { label: 'Headcount', color: 'var(--series-4)' },
                { label: 'Labour cost', color: 'var(--series-8)' },
              ]}
            />
          }
        >
          <ResponsiveContainer width="100%" height={300}>
            <LineChart data={indexed} margin={{ left: -12, right: 8 }}>
              <CartesianGrid stroke={gridStroke} vertical={false} />
              <XAxis dataKey="month" tick={axisTick} axisLine={{ stroke: 'var(--baseline)' }} tickLine={false} interval="preserveStartEnd" />
              <YAxis tick={axisTick} axisLine={false} tickLine={false} tickFormatter={(v) => `${v}`} />
              <Tooltip content={<ChartTooltip formatter={(v) => v.toFixed(0)} />} />
              <Line type="monotone" dataKey="Revenue" stroke="var(--series-1)" strokeWidth={2} dot={false} />
              <Line type="monotone" dataKey="Visitors" stroke="var(--series-3)" strokeWidth={2} dot={false} />
              <Line type="monotone" dataKey="Headcount" stroke="var(--series-4)" strokeWidth={2} dot={false} />
              <Line type="monotone" dataKey="Labour Cost" stroke="var(--series-8)" strokeWidth={2} dot={false} />
            </LineChart>
          </ResponsiveContainer>
        </ChartCard>
      </ChartGrid>

      <div className="mt-4">
        <ChartCard title="Revenue per Employee Trend" subtitle="MYR, trailing 12 months">
          <ResponsiveContainer width="100%" height={220}>
            <LineChart data={revTrend} margin={{ left: 4, right: 8 }}>
              <CartesianGrid stroke={gridStroke} vertical={false} />
              <XAxis dataKey="month" tick={axisTick} axisLine={{ stroke: 'var(--baseline)' }} tickLine={false} interval="preserveStartEnd" />
              <YAxis tick={axisTick} axisLine={false} tickLine={false} tickFormatter={(v) => fmtMYR(v)} />
              <Tooltip content={<ChartTooltip formatter={(v) => fmtMYR(v)} />} />
              <Line type="monotone" dataKey="revenuePerEmployee" name="Revenue / employee" stroke="var(--series-1)" strokeWidth={2} dot={false} />
            </LineChart>
          </ResponsiveContainer>
        </ChartCard>
      </div>
    </div>
  );
}
