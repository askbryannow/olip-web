import { Bar, BarChart, CartesianGrid, Line, LineChart, ResponsiveContainer, Tooltip, XAxis, YAxis } from 'recharts';
import { useFilters } from '../lib/FiltersContext';
import { PageHeader, KpiRow, ChartGrid } from '../components/PageHeader';
import { StatTile } from '../components/StatTile';
import { ChartCard } from '../components/ChartCard';
import { ChartTooltip, axisTick, gridStroke } from '../components/ChartTheme';
import { Funnel } from '../components/Funnel';
import { fmtMYR, fmtNum } from '../lib/filters';
import { costPerHireTrend, endMonth, recruitmentFunnel, timeToHireTrend, vacancyAging } from '../lib/selectors';
import { getDataset } from '../data/generate';

export function RecruitmentPage() {
  const { filters } = useFilters();
  const ds = getDataset();
  const cm = endMonth(filters, ds.calendar);

  const funnel = recruitmentFunnel(filters, 3);
  const aging = vacancyAging(filters, cm);
  const ttHire = timeToHireTrend(filters, 12);
  const cph = costPerHireTrend(filters, 12);

  const totalOpen = aging.reduce((s, a) => s + a.openVacancies, 0);
  const avgAge = aging.length ? aging.reduce((s, a) => s + a.avgAgeDays * a.openVacancies, 0) / Math.max(totalOpen, 1) : 0;
  const latestTTH = ttHire.filter((t) => t.days > 0).slice(-1)[0]?.days ?? 0;
  const latestCPH = cph.filter((t) => t.cost > 0).slice(-1)[0]?.cost ?? 0;
  const conversionRate = funnel[0].value > 0 ? (funnel[4].value / funnel[0].value) * 100 : 0;

  return (
    <div>
      <PageHeader title="Recruitment" subtitle="Applicant funnel, vacancy aging, and hiring efficiency" />

      <KpiRow>
        <StatTile label="Open Vacancies" value={fmtNum(totalOpen)} sublabel={cm.label} />
        <StatTile label="Avg Vacancy Age" value={`${avgAge.toFixed(0)} days`} sublabel="open positions" />
        <StatTile label="Time to Hire" value={`${latestTTH.toFixed(0)} days`} sublabel="latest month" />
        <StatTile label="Cost per Hire" value={fmtMYR(latestCPH)} sublabel="latest month" />
      </KpiRow>

      <ChartGrid cols={2}>
        <ChartCard title="Recruitment Funnel" subtitle="Applicants → Joins, trailing 3 months">
          <Funnel stages={funnel} />
          <div className="mt-2 text-[12px] text-[var(--text-secondary)]">Overall conversion: <span className="font-medium text-[var(--text-primary)]">{conversionRate.toFixed(1)}%</span> applied → joined</div>
        </ChartCard>

        <ChartCard title="Aging Vacancies by Department" subtitle={cm.label}>
          <ResponsiveContainer width="100%" height={260}>
            <BarChart data={aging} layout="vertical" margin={{ left: 8, right: 20 }}>
              <CartesianGrid stroke={gridStroke} horizontal={false} />
              <XAxis type="number" tick={axisTick} axisLine={{ stroke: 'var(--baseline)' }} tickLine={false} />
              <YAxis type="category" dataKey="department" tick={axisTick} width={110} axisLine={false} tickLine={false} />
              <Tooltip content={<ChartTooltip formatter={(v, n) => (n === 'Avg age (days)' ? `${v.toFixed(0)}d` : fmtNum(v))} />} cursor={{ fill: 'var(--surface-2)' }} />
              <Bar dataKey="openVacancies" name="Open vacancies" fill="var(--series-1)" radius={[0, 4, 4, 0]} maxBarSize={18} />
            </BarChart>
          </ResponsiveContainer>
        </ChartCard>

        <ChartCard title="Time to Hire Trend" subtitle="Avg days, trailing 12 months">
          <ResponsiveContainer width="100%" height={220}>
            <LineChart data={ttHire} margin={{ left: -12, right: 8 }}>
              <CartesianGrid stroke={gridStroke} vertical={false} />
              <XAxis dataKey="month" tick={axisTick} axisLine={{ stroke: 'var(--baseline)' }} tickLine={false} interval="preserveStartEnd" />
              <YAxis tick={axisTick} axisLine={false} tickLine={false} />
              <Tooltip content={<ChartTooltip formatter={(v) => `${v.toFixed(0)}d`} />} />
              <Line type="monotone" dataKey="days" name="Time to hire" stroke="var(--series-1)" strokeWidth={2} dot={false} connectNulls />
            </LineChart>
          </ResponsiveContainer>
        </ChartCard>

        <ChartCard title="Cost per Hire Trend" subtitle="MYR, trailing 12 months">
          <ResponsiveContainer width="100%" height={220}>
            <LineChart data={cph} margin={{ left: 4, right: 8 }}>
              <CartesianGrid stroke={gridStroke} vertical={false} />
              <XAxis dataKey="month" tick={axisTick} axisLine={{ stroke: 'var(--baseline)' }} tickLine={false} interval="preserveStartEnd" />
              <YAxis tick={axisTick} axisLine={false} tickLine={false} tickFormatter={(v) => fmtMYR(v)} />
              <Tooltip content={<ChartTooltip formatter={(v) => fmtMYR(v)} />} />
              <Line type="monotone" dataKey="cost" name="Cost per hire" stroke="var(--series-2)" strokeWidth={2} dot={false} connectNulls />
            </LineChart>
          </ResponsiveContainer>
        </ChartCard>
      </ChartGrid>
    </div>
  );
}
