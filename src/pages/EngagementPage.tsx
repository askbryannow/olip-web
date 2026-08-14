import { Bar, BarChart, CartesianGrid, Cell, Line, LineChart, ResponsiveContainer, Tooltip, XAxis, YAxis } from 'recharts';
import { useFilters } from '../lib/FiltersContext';
import { PageHeader, KpiRow, ChartGrid } from '../components/PageHeader';
import { StatTile } from '../components/StatTile';
import { ChartCard } from '../components/ChartCard';
import { ChartTooltip, axisTick, gridStroke } from '../components/ChartTheme';
import { CATEGORICAL } from '../lib/colors';
import { fmtNum } from '../lib/filters';
import { engagementTrend, grievanceBreakdown } from '../lib/selectors';

export function EngagementPage() {
  const { filters } = useFilters();
  const trend = engagementTrend(filters);
  const grievances = grievanceBreakdown(filters);

  const latest = trend.slice(-1)[0];
  const totalGrievances = grievances.reduce((s, g) => s + g.count, 0);
  const totalRecognitions = trend.slice(-4).reduce((s, t) => s + t.recognitions, 0);

  return (
    <div>
      <PageHeader title="Engagement" subtitle="Survey sentiment, eNPS, grievances, and recognition" />

      <KpiRow>
        <StatTile label="Engagement Score" value={latest ? latest.score.toFixed(0) : '–'} sublabel={`${latest?.quarter ?? ''}, /100`} />
        <StatTile label="eNPS" value={latest ? latest.eNPS.toFixed(0) : '–'} sublabel={latest?.quarter ?? ''} deltaGood={(latest?.eNPS ?? 0) >= 0} />
        <StatTile label="Grievances" value={fmtNum(totalGrievances)} sublabel="recent period" deltaGood={false} />
        <StatTile label="Recognitions Given" value={fmtNum(totalRecognitions)} sublabel="trailing 4 quarters" deltaGood={true} />
      </KpiRow>

      <ChartGrid cols={2}>
        <ChartCard title="Engagement Score & eNPS Over Time" subtitle="Quarterly survey results">
          <ResponsiveContainer width="100%" height={260}>
            <LineChart data={trend} margin={{ left: -12, right: 8 }}>
              <CartesianGrid stroke={gridStroke} vertical={false} />
              <XAxis dataKey="quarter" tick={axisTick} axisLine={{ stroke: 'var(--baseline)' }} tickLine={false} />
              <YAxis tick={axisTick} axisLine={false} tickLine={false} />
              <Tooltip content={<ChartTooltip formatter={(v) => v.toFixed(0)} />} />
              <Line type="monotone" dataKey="score" name="Engagement score" stroke="var(--series-1)" strokeWidth={2} dot={{ r: 3 }} />
              <Line type="monotone" dataKey="eNPS" name="eNPS" stroke="var(--series-7)" strokeWidth={2} dot={{ r: 3 }} />
            </LineChart>
          </ResponsiveContainer>
        </ChartCard>

        <ChartCard title="Grievances by Category" subtitle="Recent period">
          <ResponsiveContainer width="100%" height={260}>
            <BarChart data={grievances} layout="vertical" margin={{ left: 8, right: 20 }}>
              <CartesianGrid stroke={gridStroke} horizontal={false} />
              <XAxis type="number" tick={axisTick} axisLine={{ stroke: 'var(--baseline)' }} tickLine={false} allowDecimals={false} />
              <YAxis type="category" dataKey="category" tick={axisTick} width={100} axisLine={false} tickLine={false} />
              <Tooltip content={<ChartTooltip formatter={(v) => fmtNum(v)} />} cursor={{ fill: 'var(--surface-2)' }} />
              <Bar dataKey="count" name="Grievances" radius={[0, 4, 4, 0]} maxBarSize={20}>
                {grievances.map((_, i) => (
                  <Cell key={i} fill={CATEGORICAL[i % CATEGORICAL.length]} />
                ))}
              </Bar>
            </BarChart>
          </ResponsiveContainer>
        </ChartCard>
      </ChartGrid>

      <div className="mt-4">
        <ChartCard title="Recognition Volume" subtitle="Recognitions given per quarter">
          <ResponsiveContainer width="100%" height={200}>
            <BarChart data={trend} margin={{ left: -12, right: 8 }}>
              <CartesianGrid stroke={gridStroke} vertical={false} />
              <XAxis dataKey="quarter" tick={axisTick} axisLine={{ stroke: 'var(--baseline)' }} tickLine={false} />
              <YAxis tick={axisTick} axisLine={false} tickLine={false} />
              <Tooltip content={<ChartTooltip formatter={(v) => fmtNum(v)} />} cursor={{ fill: 'var(--surface-2)' }} />
              <Bar dataKey="recognitions" name="Recognitions" fill="var(--series-3)" radius={[4, 4, 0, 0]} maxBarSize={26} />
            </BarChart>
          </ResponsiveContainer>
        </ChartCard>
      </div>
    </div>
  );
}
