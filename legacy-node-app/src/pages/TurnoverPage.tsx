import { Bar, BarChart, CartesianGrid, ResponsiveContainer, Tooltip, XAxis, YAxis } from 'recharts';
import { useFilters } from '../lib/FiltersContext';
import { PageHeader, KpiRow, ChartGrid } from '../components/PageHeader';
import { StatTile } from '../components/StatTile';
import { ChartCard, Legend } from '../components/ChartCard';
import { ChartTooltip, axisTick, gridStroke } from '../components/ChartTheme';
import { Heatmap } from '../components/Heatmap';
import { fmtNum, fmtPct } from '../lib/filters';
import { earlyAttritionRate, turnoverHeatmapByDept, turnoverRateTrailing, turnoverTrend } from '../lib/selectors';

export function TurnoverPage() {
  const { filters } = useFilters();
  const trend = turnoverTrend(filters, 12);
  const rate = turnoverRateTrailing(filters, 12);
  const early = earlyAttritionRate(filters, 12);
  const heat = turnoverHeatmapByDept(filters, 6);

  const totalVoluntary = trend.reduce((s, t) => s + t.voluntary, 0);
  const totalInvoluntary = trend.reduce((s, t) => s + t.involuntary, 0);
  const totalResigns = totalVoluntary + totalInvoluntary;
  const voluntaryPct = totalResigns > 0 ? (totalVoluntary / totalResigns) * 100 : 0;

  return (
    <div>
      <PageHeader title="Turnover" subtitle="Attrition trend, voluntary vs involuntary split, early attrition, and department hotspots" />

      <KpiRow>
        <StatTile label="Turnover Rate" value={fmtPct(rate)} sublabel="trailing 12mo" />
        <StatTile label="Voluntary Share" value={fmtPct(voluntaryPct, 0)} sublabel={`${fmtNum(totalVoluntary)} of ${fmtNum(totalResigns)} exits`} />
        <StatTile label="Early Attrition" value={fmtPct(early, 0)} sublabel="exits within 1yr tenure" deltaGood={early < 25} delta={early < 25 ? 'Healthy' : 'Elevated'} />
        <StatTile label="Total Exits" value={fmtNum(totalResigns)} sublabel="trailing 12mo" />
      </KpiRow>

      <ChartGrid cols={2}>
        <ChartCard
          title="Monthly Turnover Trend"
          subtitle="Voluntary vs involuntary exits, trailing 12 months"
          legend={<Legend items={[{ label: 'Voluntary', color: 'var(--series-1)' }, { label: 'Involuntary', color: 'var(--series-8)' }]} />}
        >
          <ResponsiveContainer width="100%" height={280}>
            <BarChart data={trend} margin={{ left: -12, right: 8 }}>
              <CartesianGrid stroke={gridStroke} vertical={false} />
              <XAxis dataKey="month" tick={axisTick} axisLine={{ stroke: 'var(--baseline)' }} tickLine={false} interval="preserveStartEnd" />
              <YAxis tick={axisTick} axisLine={false} tickLine={false} />
              <Tooltip content={<ChartTooltip formatter={(v) => fmtNum(v)} />} cursor={{ fill: 'var(--surface-2)' }} />
              <Bar dataKey="voluntary" name="Voluntary" stackId="exits" fill="var(--series-1)" radius={[0, 0, 0, 0]} maxBarSize={22} />
              <Bar dataKey="involuntary" name="Involuntary" stackId="exits" fill="var(--series-8)" radius={[3, 3, 0, 0]} maxBarSize={22} />
            </BarChart>
          </ResponsiveContainer>
        </ChartCard>

        <ChartCard title="Turnover Rate by Department" subtitle="Monthly rate (%), trailing 6 months">
          <Heatmap
            rows={heat.depts}
            cols={heat.months}
            value={(dept, month) => heat.cell(dept, month)}
            formatter={(v) => `${v}%`}
          />
        </ChartCard>
      </ChartGrid>

      <div className="mt-4">
        <ChartCard title="Exit Volume by Month" subtitle="Total exits, trailing 12 months">
          <ResponsiveContainer width="100%" height={200}>
            <BarChart data={trend} margin={{ left: -12, right: 8 }}>
              <CartesianGrid stroke={gridStroke} vertical={false} />
              <XAxis dataKey="month" tick={axisTick} axisLine={{ stroke: 'var(--baseline)' }} tickLine={false} interval="preserveStartEnd" />
              <YAxis tick={axisTick} axisLine={false} tickLine={false} />
              <Tooltip content={<ChartTooltip formatter={(v) => fmtNum(v)} />} cursor={{ fill: 'var(--surface-2)' }} />
              <Bar dataKey="total" name="Total exits" fill="var(--series-7)" radius={[4, 4, 0, 0]} maxBarSize={22} />
            </BarChart>
          </ResponsiveContainer>
        </ChartCard>
      </div>
    </div>
  );
}
