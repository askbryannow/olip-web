import { Bar, BarChart, CartesianGrid, Cell, Line, LineChart, ResponsiveContainer, Tooltip, XAxis, YAxis } from 'recharts';
import { useFilters } from '../lib/FiltersContext';
import { PageHeader, KpiRow, ChartGrid } from '../components/PageHeader';
import { StatTile } from '../components/StatTile';
import { ChartCard, Legend } from '../components/ChartCard';
import { ChartTooltip, axisTick, gridStroke } from '../components/ChartTheme';
import { fmtNum } from '../lib/filters';
import { STATUS } from '../lib/colors';
import { complianceByOutlet, endMonth, incidentsByOutlet, ltifrTrend } from '../lib/selectors';
import { getDataset } from '../data/generate';

export function SafetyPage() {
  const { filters } = useFilters();
  const ds = getDataset();
  const cm = endMonth(filters, ds.calendar);

  const ltifr = ltifrTrend(filters, 12);
  const byOutlet = incidentsByOutlet(filters, 6);
  const compliance = complianceByOutlet(filters, cm);

  const latestLtifr = ltifr.slice(-1)[0]?.ltifr ?? 0;
  const totalIncidents = byOutlet.reduce((s, o) => s + o.count, 0);
  const totalLTI = byOutlet.reduce((s, o) => s + o.lti, 0);
  const avgCompliance = compliance.length ? compliance.reduce((s, c) => s + c.score, 0) / compliance.length : 0;

  function complianceColor(score: number) {
    if (score >= 90) return STATUS.good;
    if (score >= 80) return STATUS.warning;
    if (score >= 70) return STATUS.serious;
    return STATUS.critical;
  }

  return (
    <div>
      <PageHeader title="Safety" subtitle="Lost-time injury frequency, incident hotspots, and audit compliance" />

      <KpiRow>
        <StatTile label="LTIFR" value={latestLtifr.toFixed(1)} sublabel="per million hours, trailing 12mo" deltaGood={latestLtifr < 3} />
        <StatTile label="Total Incidents" value={fmtNum(totalIncidents)} sublabel="trailing 6mo, all severities" />
        <StatTile label="Lost Time Injuries" value={fmtNum(totalLTI)} sublabel="trailing 6mo" deltaGood={totalLTI === 0} />
        <StatTile label="Avg Compliance Score" value={avgCompliance.toFixed(0)} sublabel={cm.label} deltaGood={avgCompliance >= 90} />
      </KpiRow>

      <ChartGrid cols={2}>
        <ChartCard title="LTIFR Trend" subtitle="Lost-time injuries per million hours worked (cumulative), trailing 12 months">
          <ResponsiveContainer width="100%" height={260}>
            <LineChart data={ltifr} margin={{ left: -12, right: 8 }}>
              <CartesianGrid stroke={gridStroke} vertical={false} />
              <XAxis dataKey="month" tick={axisTick} axisLine={{ stroke: 'var(--baseline)' }} tickLine={false} interval="preserveStartEnd" />
              <YAxis tick={axisTick} axisLine={false} tickLine={false} />
              <Tooltip content={<ChartTooltip formatter={(v) => v.toFixed(1)} />} />
              <Line type="monotone" dataKey="ltifr" name="LTIFR" stroke="var(--status-critical)" strokeWidth={2} dot={false} />
            </LineChart>
          </ResponsiveContainer>
        </ChartCard>

        <ChartCard
          title="Incidents by Location"
          subtitle="Trailing 6 months"
          legend={<Legend items={[{ label: 'All incidents', color: 'var(--series-1)' }, { label: 'Lost time injuries', color: 'var(--status-critical)' }]} />}
        >
          <ResponsiveContainer width="100%" height={Math.max(220, byOutlet.length * 26)}>
            <BarChart data={byOutlet} layout="vertical" margin={{ left: 8, right: 20 }}>
              <CartesianGrid stroke={gridStroke} horizontal={false} />
              <XAxis type="number" tick={axisTick} axisLine={{ stroke: 'var(--baseline)' }} tickLine={false} allowDecimals={false} />
              <YAxis type="category" dataKey="outlet" tick={axisTick} width={150} axisLine={false} tickLine={false} />
              <Tooltip content={<ChartTooltip formatter={(v) => fmtNum(v)} />} cursor={{ fill: 'var(--surface-2)' }} />
              <Bar dataKey="count" name="All incidents" fill="var(--series-1)" radius={[0, 4, 4, 0]} maxBarSize={14} />
              <Bar dataKey="lti" name="Lost time injuries" fill="var(--status-critical)" radius={[0, 4, 4, 0]} maxBarSize={14} />
            </BarChart>
          </ResponsiveContainer>
        </ChartCard>
      </ChartGrid>

      <div className="mt-4">
        <ChartCard title="Safety Compliance Score by Outlet" subtitle={cm.label}>
          <ResponsiveContainer width="100%" height={Math.max(220, compliance.length * 26)}>
            <BarChart data={compliance} layout="vertical" margin={{ left: 8, right: 24 }}>
              <CartesianGrid stroke={gridStroke} horizontal={false} />
              <XAxis type="number" domain={[0, 100]} tick={axisTick} axisLine={{ stroke: 'var(--baseline)' }} tickLine={false} />
              <YAxis type="category" dataKey="outlet" tick={axisTick} width={170} axisLine={false} tickLine={false} />
              <Tooltip content={<ChartTooltip formatter={(v) => v.toFixed(0)} />} cursor={{ fill: 'var(--surface-2)' }} />
              <Bar dataKey="score" name="Compliance score" radius={[0, 4, 4, 0]} maxBarSize={16}>
                {compliance.map((c, i) => (
                  <Cell key={i} fill={complianceColor(c.score)} />
                ))}
              </Bar>
            </BarChart>
          </ResponsiveContainer>
        </ChartCard>
      </div>
    </div>
  );
}
