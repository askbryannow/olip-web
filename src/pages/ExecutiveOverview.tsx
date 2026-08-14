import { Bar, BarChart, CartesianGrid, Cell, Line, LineChart, ResponsiveContainer, Tooltip, XAxis, YAxis } from 'recharts';
import { useFilters } from '../lib/FiltersContext';
import { PageHeader, KpiRow, ChartGrid } from '../components/PageHeader';
import { StatTile } from '../components/StatTile';
import { ChartCard, Legend } from '../components/ChartCard';
import { ChartTooltip, axisTick, gridStroke } from '../components/ChartTheme';
import { CATEGORICAL } from '../lib/colors';
import { fmtMYR, fmtNum, fmtPct, monthEndDate } from '../lib/filters';
import {
  employmentTypeMix,
  endMonth,
  genderMix,
  headcountAt,
  headcountByBU,
  headcountTrend,
  hireResignTrend,
  ltifrTrend,
  payrollCostFor,
  revenueFor,
  timeToHireTrend,
  turnoverRateTrailing,
  overtimeFor,
} from '../lib/selectors';
import { getDataset } from '../data/generate';

export function ExecutiveOverview() {
  const { filters } = useFilters();
  const ds = getDataset();
  const cm = endMonth(filters, ds.calendar);
  const date = monthEndDate(cm);
  const prevIdx = ds.calendar.findIndex((c) => c.key === cm.key) - 1;
  const prevCm = prevIdx >= 0 ? ds.calendar[prevIdx] : null;

  const headcount = headcountAt(filters, date);
  const prevHeadcount = prevCm ? headcountAt(filters, monthEndDate(prevCm)) : headcount;
  const headcountDelta = headcount - prevHeadcount;

  const turnoverRate = turnoverRateTrailing(filters, 12);
  const payrollCost = payrollCostFor(filters, cm);
  const revenue = revenueFor(filters, cm);
  const payrollPctRevenue = revenue > 0 ? (payrollCost / revenue) * 100 : 0;
  const overtime = overtimeFor(filters, cm);
  const ttHire = timeToHireTrend(filters, 3);
  const avgTimeToHire = ttHire.filter((t) => t.days > 0).slice(-1)[0]?.days ?? 0;
  const revenuePerEmployee = headcount > 0 ? revenue / headcount : 0;
  const ltifr = ltifrTrend(filters, 12).slice(-1)[0]?.ltifr ?? 0;

  const hcTrend = headcountTrend(filters, 12);
  const hrTrend = hireResignTrend(filters, 12);
  const buData = headcountByBU(filters, date);
  const genderData = genderMix(filters, date);
  const etData = employmentTypeMix(filters, date);

  return (
    <div>
      <PageHeader title="Executive Overview" subtitle={`As of ${cm.label} · workforce, cost, and safety at a glance`} />

      <KpiRow>
        <StatTile label="Headcount" value={fmtNum(headcount)} delta={`${headcountDelta >= 0 ? '+' : ''}${headcountDelta} MoM`} deltaGood={headcountDelta >= 0} />
        <StatTile label="Turnover Rate" value={fmtPct(turnoverRate)} sublabel="trailing 12mo" deltaGood={null} />
        <StatTile label="Payroll Cost" value={fmtMYR(payrollCost)} sublabel={`${fmtPct(payrollPctRevenue)} of revenue`} deltaGood={null} />
        <StatTile label="Overtime Cost" value={fmtMYR(overtime.pay)} sublabel={`${fmtNum(overtime.hours)} hrs`} deltaGood={null} />
        <StatTile label="Time to Hire" value={`${avgTimeToHire.toFixed(0)} days`} sublabel="avg, trailing 3mo" deltaGood={null} />
        <StatTile label="Revenue / Employee" value={fmtMYR(revenuePerEmployee)} sublabel="this month" deltaGood={null} />
        <StatTile label="Safety Incident Rate" value={ltifr.toFixed(1)} sublabel="LTIFR, trailing 12mo" deltaGood={null} />
      </KpiRow>

      <ChartGrid cols={3}>
        <ChartCard title="Headcount by Business Unit" subtitle={cm.label}>
          <ResponsiveContainer width="100%" height={260}>
            <BarChart data={buData} layout="vertical" margin={{ left: 8, right: 16 }}>
              <CartesianGrid stroke={gridStroke} horizontal={false} />
              <XAxis type="number" tick={axisTick} axisLine={{ stroke: 'var(--baseline)' }} tickLine={false} />
              <YAxis type="category" dataKey="bu" tick={axisTick} width={110} axisLine={false} tickLine={false} />
              <Tooltip content={<ChartTooltip formatter={(v) => fmtNum(v)} />} cursor={{ fill: 'var(--surface-2)' }} />
              <Bar dataKey="headcount" name="Headcount" radius={[0, 4, 4, 0]} maxBarSize={22}>
                {buData.map((_, i) => (
                  <Cell key={i} fill={CATEGORICAL[i % CATEGORICAL.length]} />
                ))}
              </Bar>
            </BarChart>
          </ResponsiveContainer>
        </ChartCard>

        <ChartCard
          title="Hires, Resignations & Net Growth"
          subtitle="Trailing 12 months"
          legend={<Legend items={[{ label: 'Hires', color: 'var(--series-3)' }, { label: 'Resignations', color: 'var(--series-8)' }, { label: 'Net growth', color: 'var(--series-1)' }]} />}
        >
          <ResponsiveContainer width="100%" height={260}>
            <LineChart data={hrTrend} margin={{ left: -12, right: 8 }}>
              <CartesianGrid stroke={gridStroke} vertical={false} />
              <XAxis dataKey="month" tick={axisTick} axisLine={{ stroke: 'var(--baseline)' }} tickLine={false} interval="preserveStartEnd" />
              <YAxis tick={axisTick} axisLine={false} tickLine={false} />
              <Tooltip content={<ChartTooltip formatter={(v) => fmtNum(v)} />} />
              <Line type="monotone" dataKey="hires" name="Hires" stroke="var(--series-3)" strokeWidth={2} dot={false} />
              <Line type="monotone" dataKey="resignations" name="Resignations" stroke="var(--series-8)" strokeWidth={2} dot={false} />
              <Line type="monotone" dataKey="netGrowth" name="Net growth" stroke="var(--series-1)" strokeWidth={2} dot={false} strokeDasharray="4 3" />
            </LineChart>
          </ResponsiveContainer>
        </ChartCard>

        <div className="flex flex-col gap-4">
          <ChartCard title="Employment Type" subtitle={cm.label}>
            <ResponsiveContainer width="100%" height={110}>
              <BarChart data={etData} layout="vertical" margin={{ left: 8, right: 24 }}>
                <XAxis type="number" hide />
                <YAxis type="category" dataKey="type" tick={axisTick} width={80} axisLine={false} tickLine={false} />
                <Tooltip content={<ChartTooltip formatter={(v) => fmtNum(v)} />} cursor={{ fill: 'var(--surface-2)' }} />
                <Bar dataKey="count" name="Employees" radius={[0, 4, 4, 0]} maxBarSize={16} fill="var(--series-1)" />
              </BarChart>
            </ResponsiveContainer>
          </ChartCard>
          <ChartCard title="Gender Mix" subtitle={cm.label}>
            <div className="flex items-center gap-4">
              <div className="flex-1 h-6 rounded-full overflow-hidden flex bg-[var(--surface-2)]">
                <div style={{ width: `${(genderData.male / Math.max(genderData.total, 1)) * 100}%`, background: 'var(--series-1)' }} />
                <div style={{ width: `${(genderData.female / Math.max(genderData.total, 1)) * 100}%`, background: 'var(--series-5)' }} />
              </div>
            </div>
            <div className="flex justify-between mt-2 text-[12px]">
              <span className="text-[var(--text-secondary)]">● Male {fmtNum(genderData.male)} ({fmtPct((genderData.male / Math.max(genderData.total, 1)) * 100, 0)})</span>
              <span className="text-[var(--text-secondary)]">● Female {fmtNum(genderData.female)} ({fmtPct((genderData.female / Math.max(genderData.total, 1)) * 100, 0)})</span>
            </div>
          </ChartCard>
        </div>
      </ChartGrid>

      <div className="mt-4">
        <ChartCard title="Headcount Trend" subtitle="Trailing 12 months">
          <ResponsiveContainer width="100%" height={200}>
            <LineChart data={hcTrend} margin={{ left: -12, right: 8 }}>
              <CartesianGrid stroke={gridStroke} vertical={false} />
              <XAxis dataKey="month" tick={axisTick} axisLine={{ stroke: 'var(--baseline)' }} tickLine={false} interval="preserveStartEnd" />
              <YAxis tick={axisTick} axisLine={false} tickLine={false} domain={['dataMin - 20', 'dataMax + 20']} />
              <Tooltip content={<ChartTooltip formatter={(v) => fmtNum(v)} />} />
              <Line type="monotone" dataKey="headcount" name="Headcount" stroke="var(--series-1)" strokeWidth={2} dot={false} />
            </LineChart>
          </ResponsiveContainer>
        </ChartCard>
      </div>
    </div>
  );
}
