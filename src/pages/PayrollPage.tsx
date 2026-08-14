import { Area, AreaChart, Bar, BarChart, CartesianGrid, ResponsiveContainer, Tooltip, XAxis, YAxis } from 'recharts';
import { useFilters } from '../lib/FiltersContext';
import { PageHeader, KpiRow, ChartGrid } from '../components/PageHeader';
import { StatTile } from '../components/StatTile';
import { ChartCard, Legend } from '../components/ChartCard';
import { ChartTooltip, axisTick, gridStroke } from '../components/ChartTheme';
import { fmtMYR, fmtNum, fmtPct } from '../lib/filters';
import { endMonth, overtimeByDept, overtimeFor, payrollCostFor, payrollPctRevenueByOutlet, payrollTrend, revenueFor } from '../lib/selectors';
import { getDataset } from '../data/generate';

export function PayrollPage() {
  const { filters } = useFilters();
  const ds = getDataset();
  const cm = endMonth(filters, ds.calendar);

  const trend = payrollTrend(filters, 12);
  const otByDept = overtimeByDept(filters, cm);
  const pctByOutlet = payrollPctRevenueByOutlet(filters, cm);

  const payrollCost = payrollCostFor(filters, cm);
  const revenue = revenueFor(filters, cm);
  const pctRevenue = revenue > 0 ? (payrollCost / revenue) * 100 : 0;
  const ot = overtimeFor(filters, cm);
  const otVariancePct = ot.budgetHours > 0 ? ((ot.hours - ot.budgetHours) / ot.budgetHours) * 100 : 0;

  return (
    <div>
      <PageHeader title="Payroll & Overtime" subtitle="Payroll cost trend, overtime variance by department, and payroll efficiency by outlet" />

      <KpiRow>
        <StatTile label="Payroll Cost" value={fmtMYR(payrollCost)} sublabel={cm.label} />
        <StatTile label="Payroll % of Revenue" value={fmtPct(pctRevenue)} sublabel={cm.label} />
        <StatTile label="Overtime Cost" value={fmtMYR(ot.pay)} sublabel={`${fmtNum(ot.hours)} hrs`} />
        <StatTile
          label="OT vs Budget"
          value={`${otVariancePct >= 0 ? '+' : ''}${otVariancePct.toFixed(1)}%`}
          sublabel="actual vs budgeted hours"
          deltaGood={otVariancePct <= 0}
          delta={otVariancePct <= 0 ? 'Within budget' : 'Over budget'}
        />
      </KpiRow>

      <ChartGrid cols={2}>
        <ChartCard
          title="Payroll Cost Trend"
          subtitle="Base + overtime + statutory, trailing 12 months"
          legend={<Legend items={[{ label: 'Base pay', color: 'var(--series-1)' }, { label: 'Overtime', color: 'var(--series-2)' }, { label: 'Statutory', color: 'var(--series-4)' }]} />}
        >
          <ResponsiveContainer width="100%" height={280}>
            <AreaChart data={trend} margin={{ left: 4, right: 8 }}>
              <CartesianGrid stroke={gridStroke} vertical={false} />
              <XAxis dataKey="month" tick={axisTick} axisLine={{ stroke: 'var(--baseline)' }} tickLine={false} interval="preserveStartEnd" />
              <YAxis tick={axisTick} axisLine={false} tickLine={false} tickFormatter={(v) => fmtMYR(v)} />
              <Tooltip content={<ChartTooltip formatter={(v) => fmtMYR(v)} />} />
              <Area type="monotone" dataKey="base" name="Base pay" stackId="pay" stroke="var(--series-1)" fill="var(--series-1)" fillOpacity={0.75} />
              <Area type="monotone" dataKey="overtime" name="Overtime" stackId="pay" stroke="var(--series-2)" fill="var(--series-2)" fillOpacity={0.75} />
              <Area type="monotone" dataKey="statutory" name="Statutory" stackId="pay" stroke="var(--series-4)" fill="var(--series-4)" fillOpacity={0.75} />
            </AreaChart>
          </ResponsiveContainer>
        </ChartCard>

        <ChartCard
          title="Overtime by Department: Actual vs Budget"
          subtitle={cm.label}
          legend={<Legend items={[{ label: 'Actual hrs', color: 'var(--series-1)' }, { label: 'Budget hrs', color: 'var(--text-muted)' }]} />}
        >
          <ResponsiveContainer width="100%" height={280}>
            <BarChart data={otByDept} layout="vertical" margin={{ left: 8, right: 20 }}>
              <CartesianGrid stroke={gridStroke} horizontal={false} />
              <XAxis type="number" tick={axisTick} axisLine={{ stroke: 'var(--baseline)' }} tickLine={false} />
              <YAxis type="category" dataKey="department" tick={axisTick} width={100} axisLine={false} tickLine={false} />
              <Tooltip content={<ChartTooltip formatter={(v) => `${fmtNum(v)} hrs`} />} cursor={{ fill: 'var(--surface-2)' }} />
              <Bar dataKey="budgetHours" name="Budget hrs" fill="var(--baseline)" radius={[0, 4, 4, 0]} maxBarSize={12} />
              <Bar dataKey="actualHours" name="Actual hrs" fill="var(--series-1)" radius={[0, 4, 4, 0]} maxBarSize={12} />
            </BarChart>
          </ResponsiveContainer>
        </ChartCard>
      </ChartGrid>

      <div className="mt-4">
        <ChartCard title="Payroll % of Revenue by Outlet" subtitle={cm.label}>
          <ResponsiveContainer width="100%" height={Math.max(220, pctByOutlet.length * 28)}>
            <BarChart data={pctByOutlet} layout="vertical" margin={{ left: 8, right: 30 }}>
              <CartesianGrid stroke={gridStroke} horizontal={false} />
              <XAxis type="number" tick={axisTick} axisLine={{ stroke: 'var(--baseline)' }} tickLine={false} tickFormatter={(v) => `${v}%`} />
              <YAxis type="category" dataKey="outlet" tick={axisTick} width={170} axisLine={false} tickLine={false} />
              <Tooltip content={<ChartTooltip formatter={(v) => fmtPct(v)} />} cursor={{ fill: 'var(--surface-2)' }} />
              <Bar dataKey="pct" name="Payroll % of revenue" fill="var(--series-3)" radius={[0, 4, 4, 0]} maxBarSize={16} />
            </BarChart>
          </ResponsiveContainer>
        </ChartCard>
      </div>
    </div>
  );
}
