import { Bar, BarChart, CartesianGrid, Cell, Line, LineChart, ResponsiveContainer, Tooltip, XAxis, YAxis } from 'recharts';
import { useFilters } from '../lib/FiltersContext';
import { PageHeader, KpiRow, ChartGrid } from '../components/PageHeader';
import { StatTile } from '../components/StatTile';
import { ChartCard } from '../components/ChartCard';
import { ChartTooltip, axisTick, gridStroke } from '../components/ChartTheme';
import { CATEGORICAL } from '../lib/colors';
import { fmtNum, fmtPct } from '../lib/filters';
import { absenteeismTrend, endMonth, lateClockInsByDept, lateClockInsTrend, leaveByType } from '../lib/selectors';
import { getDataset } from '../data/generate';

export function AttendancePage() {
  const { filters } = useFilters();
  const ds = getDataset();
  const cm = endMonth(filters, ds.calendar);

  const absTrend = absenteeismTrend(filters, 12);
  const leave = leaveByType(filters, 12);
  const lateTrend = lateClockInsTrend(filters, 12);
  const lateByDept = lateClockInsByDept(filters, cm);

  const latestAbs = absTrend.slice(-1)[0]?.rate ?? 0;
  const totalLeaveDays = leave.reduce((s, l) => s + l.days, 0);
  const latestLate = lateTrend.slice(-1)[0]?.late ?? 0;

  return (
    <div>
      <PageHeader title="Attendance & Leave" subtitle="Absenteeism trend, leave utilization, and punctuality" />

      <KpiRow>
        <StatTile label="Absenteeism Rate" value={fmtPct(latestAbs)} sublabel="latest month" deltaGood={latestAbs < 3} />
        <StatTile label="Total Leave Days" value={fmtNum(totalLeaveDays)} sublabel="trailing 12mo" />
        <StatTile label="Late Clock-ins" value={fmtNum(latestLate)} sublabel="latest month" />
      </KpiRow>

      <ChartGrid cols={2}>
        <ChartCard title="Absenteeism Trend" subtitle="% of working days lost, trailing 12 months">
          <ResponsiveContainer width="100%" height={260}>
            <LineChart data={absTrend} margin={{ left: -12, right: 8 }}>
              <CartesianGrid stroke={gridStroke} vertical={false} />
              <XAxis dataKey="month" tick={axisTick} axisLine={{ stroke: 'var(--baseline)' }} tickLine={false} interval="preserveStartEnd" />
              <YAxis tick={axisTick} axisLine={false} tickLine={false} tickFormatter={(v) => `${v}%`} />
              <Tooltip content={<ChartTooltip formatter={(v) => fmtPct(v)} />} />
              <Line type="monotone" dataKey="rate" name="Absenteeism rate" stroke="var(--series-1)" strokeWidth={2} dot={false} />
            </LineChart>
          </ResponsiveContainer>
        </ChartCard>

        <ChartCard title="Leave by Type" subtitle="Days consumed, trailing 12 months">
          <ResponsiveContainer width="100%" height={260}>
            <BarChart data={leave} layout="vertical" margin={{ left: 8, right: 20 }}>
              <CartesianGrid stroke={gridStroke} horizontal={false} />
              <XAxis type="number" tick={axisTick} axisLine={{ stroke: 'var(--baseline)' }} tickLine={false} />
              <YAxis type="category" dataKey="type" tick={axisTick} width={110} axisLine={false} tickLine={false} />
              <Tooltip content={<ChartTooltip formatter={(v) => `${fmtNum(v)} days`} />} cursor={{ fill: 'var(--surface-2)' }} />
              <Bar dataKey="days" name="Leave days" radius={[0, 4, 4, 0]} maxBarSize={20}>
                {leave.map((_, i) => (
                  <Cell key={i} fill={CATEGORICAL[i % CATEGORICAL.length]} />
                ))}
              </Bar>
            </BarChart>
          </ResponsiveContainer>
        </ChartCard>

        <ChartCard title="Late Clock-ins Trend" subtitle="Trailing 12 months">
          <ResponsiveContainer width="100%" height={220}>
            <BarChart data={lateTrend} margin={{ left: -12, right: 8 }}>
              <CartesianGrid stroke={gridStroke} vertical={false} />
              <XAxis dataKey="month" tick={axisTick} axisLine={{ stroke: 'var(--baseline)' }} tickLine={false} interval="preserveStartEnd" />
              <YAxis tick={axisTick} axisLine={false} tickLine={false} />
              <Tooltip content={<ChartTooltip formatter={(v) => fmtNum(v)} />} cursor={{ fill: 'var(--surface-2)' }} />
              <Bar dataKey="late" name="Late clock-ins" fill="var(--series-8)" radius={[4, 4, 0, 0]} maxBarSize={20} />
            </BarChart>
          </ResponsiveContainer>
        </ChartCard>

        <ChartCard title="Late Clock-ins by Department" subtitle={cm.label}>
          <ResponsiveContainer width="100%" height={220}>
            <BarChart data={lateByDept} layout="vertical" margin={{ left: 8, right: 20 }}>
              <CartesianGrid stroke={gridStroke} horizontal={false} />
              <XAxis type="number" tick={axisTick} axisLine={{ stroke: 'var(--baseline)' }} tickLine={false} />
              <YAxis type="category" dataKey="department" tick={axisTick} width={110} axisLine={false} tickLine={false} />
              <Tooltip content={<ChartTooltip formatter={(v) => fmtNum(v)} />} cursor={{ fill: 'var(--surface-2)' }} />
              <Bar dataKey="late" name="Late clock-ins" fill="var(--series-2)" radius={[0, 4, 4, 0]} maxBarSize={18} />
            </BarChart>
          </ResponsiveContainer>
        </ChartCard>
      </ChartGrid>
    </div>
  );
}
