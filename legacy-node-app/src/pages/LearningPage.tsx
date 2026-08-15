import { Bar, CartesianGrid, ComposedChart, Line, LineChart, ResponsiveContainer, Tooltip, XAxis, YAxis } from 'recharts';
import { useFilters } from '../lib/FiltersContext';
import { PageHeader, KpiRow, ChartGrid } from '../components/PageHeader';
import { StatTile } from '../components/StatTile';
import { ChartCard, Legend } from '../components/ChartCard';
import { ChartTooltip, axisTick, gridStroke } from '../components/ChartTheme';
import { fmtMYR, fmtPct } from '../lib/filters';
import { complianceTrend, hrdcClaimsTrend, trainingHoursTrend } from '../lib/selectors';

export function LearningPage() {
  const { filters } = useFilters();
  const hours = trainingHoursTrend(filters, 12);
  const compliance = complianceTrend(filters, 12);
  const hrdc = hrdcClaimsTrend(filters, 12);

  const latestHours = hours.slice(-1)[0]?.hoursPerEmployee ?? 0;
  const latestCompliance = compliance.slice(-1)[0]?.rate ?? 0;
  const totalEligible = hrdc.reduce((s, h) => s + h.eligible, 0);
  const totalSubmitted = hrdc.reduce((s, h) => s + h.submitted, 0);
  const claimRate = totalEligible > 0 ? (totalSubmitted / totalEligible) * 100 : 0;

  return (
    <div>
      <PageHeader title="Learning & Development" subtitle="Training investment, mandatory compliance, and HRD Corp claim utilization" />

      <KpiRow>
        <StatTile label="Training Hours / Employee" value={latestHours.toFixed(1)} sublabel="latest month" />
        <StatTile label="Mandatory Compliance" value={fmtPct(latestCompliance)} sublabel="latest month" deltaGood={latestCompliance >= 90} />
        <StatTile label="HRD Corp Claims" value={fmtMYR(totalSubmitted)} sublabel={`${fmtPct(claimRate, 0)} of eligible, trailing 12mo`} />
      </KpiRow>

      <ChartGrid cols={2}>
        <ChartCard title="Training Hours per Employee" subtitle="Trailing 12 months">
          <ResponsiveContainer width="100%" height={260}>
            <LineChart data={hours} margin={{ left: -12, right: 8 }}>
              <CartesianGrid stroke={gridStroke} vertical={false} />
              <XAxis dataKey="month" tick={axisTick} axisLine={{ stroke: 'var(--baseline)' }} tickLine={false} interval="preserveStartEnd" />
              <YAxis tick={axisTick} axisLine={false} tickLine={false} />
              <Tooltip content={<ChartTooltip formatter={(v) => `${v.toFixed(1)} hrs`} />} />
              <Line type="monotone" dataKey="hoursPerEmployee" name="Hours / employee" stroke="var(--series-1)" strokeWidth={2} dot={false} />
            </LineChart>
          </ResponsiveContainer>
        </ChartCard>

        <ChartCard title="Mandatory Training Compliance" subtitle="% completed on time, trailing 12 months">
          <ResponsiveContainer width="100%" height={260}>
            <LineChart data={compliance} margin={{ left: -12, right: 8 }}>
              <CartesianGrid stroke={gridStroke} vertical={false} />
              <XAxis dataKey="month" tick={axisTick} axisLine={{ stroke: 'var(--baseline)' }} tickLine={false} interval="preserveStartEnd" />
              <YAxis tick={axisTick} axisLine={false} tickLine={false} domain={[0, 100]} tickFormatter={(v) => `${v}%`} />
              <Tooltip content={<ChartTooltip formatter={(v) => fmtPct(v)} />} />
              <Line type="monotone" dataKey="rate" name="Compliance rate" stroke="var(--series-3)" strokeWidth={2} dot={false} />
            </LineChart>
          </ResponsiveContainer>
        </ChartCard>
      </ChartGrid>

      <div className="mt-4">
        <ChartCard
          title="HRD Corp Claims: Eligible vs Submitted"
          subtitle="MYR, trailing 12 months"
          legend={<Legend items={[{ label: 'Eligible', color: 'var(--baseline)' }, { label: 'Submitted', color: 'var(--series-1)' }]} />}
        >
          <ResponsiveContainer width="100%" height={240}>
            <ComposedChart data={hrdc} margin={{ left: 4, right: 8 }}>
              <CartesianGrid stroke={gridStroke} vertical={false} />
              <XAxis dataKey="month" tick={axisTick} axisLine={{ stroke: 'var(--baseline)' }} tickLine={false} interval="preserveStartEnd" />
              <YAxis tick={axisTick} axisLine={false} tickLine={false} tickFormatter={(v) => fmtMYR(v)} />
              <Tooltip content={<ChartTooltip formatter={(v) => fmtMYR(v)} />} />
              <Bar dataKey="eligible" name="Eligible" fill="var(--baseline)" radius={[4, 4, 0, 0]} maxBarSize={22} />
              <Bar dataKey="submitted" name="Submitted" fill="var(--series-1)" radius={[4, 4, 0, 0]} maxBarSize={22} />
            </ComposedChart>
          </ResponsiveContainer>
        </ChartCard>
      </div>
    </div>
  );
}
