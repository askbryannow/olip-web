import { getDataset } from '../data/store';
import type { CalendarMonth, Department, Filters } from '../data/types';
import { activeEmployeesAt, endMonth, matchesDims, monthEndDate, monthStartDate, trailingMonths } from './filters';

const ds = () => getDataset();

// ---------- generic helpers ----------
export function sum(arr: number[]): number {
  return arr.reduce((a, b) => a + b, 0);
}
export function avg(arr: number[]): number {
  return arr.length ? sum(arr) / arr.length : 0;
}
export function groupBy<T, K extends string>(arr: T[], keyFn: (t: T) => K): Record<K, T[]> {
  const out = {} as Record<K, T[]>;
  for (const item of arr) {
    const k = keyFn(item);
    (out[k] ??= []).push(item);
  }
  return out;
}
export function deptScope(filters: Filters): Department[] {
  const outlets = ds().outlets.filter((o) => matchesDims(o, filters));
  return Array.from(new Set(outlets.flatMap((o) => o.departments)));
}

// ---------- headcount / employees ----------
export function headcountAt(filters: Filters, date: Date): number {
  return activeEmployeesAt(ds(), filters, date).length;
}

export function hiresInMonth(filters: Filters, cm: CalendarMonth) {
  return ds().employees.filter(
    (e) => matchesDims(e, filters) && (filters.employmentType === 'ALL' || e.employmentType === filters.employmentType) &&
      e.joinDate.getFullYear() === cm.year && e.joinDate.getMonth() + 1 === cm.month,
  );
}
export function resignationsInMonth(filters: Filters, cm: CalendarMonth) {
  return ds().employees.filter(
    (e) =>
      matchesDims(e, filters) &&
      (filters.employmentType === 'ALL' || e.employmentType === filters.employmentType) &&
      e.exitDate &&
      e.exitDate.getFullYear() === cm.year &&
      e.exitDate.getMonth() + 1 === cm.month,
  );
}

export function headcountTrend(filters: Filters, n = 12) {
  const cal = trailingMonths(filters, ds().calendar, n);
  return cal.map((cm) => ({ month: cm.label, key: cm.key, headcount: headcountAt(filters, monthEndDate(cm)) }));
}

export function hireResignTrend(filters: Filters, n = 12) {
  const cal = trailingMonths(filters, ds().calendar, n);
  let net = 0;
  return cal.map((cm) => {
    const hires = hiresInMonth(filters, cm).length;
    const resigns = resignationsInMonth(filters, cm).length;
    net += hires - resigns;
    return { month: cm.label, key: cm.key, hires, resignations: resigns, netGrowth: hires - resigns };
  });
}

export function turnoverRateTrailing(filters: Filters, months = 12): number {
  const cal = trailingMonths(filters, ds().calendar, months);
  const resignCount = sum(cal.map((cm) => resignationsInMonth(filters, cm).length));
  const avgHc = avg(cal.map((cm) => headcountAt(filters, monthEndDate(cm))));
  return avgHc > 0 ? (resignCount / avgHc) * 100 : 0;
}

export function genderMix(filters: Filters, date: Date) {
  const emps = activeEmployeesAt(ds(), filters, date);
  const male = emps.filter((e) => e.gender === 'Male').length;
  const female = emps.length - male;
  return { male, female, total: emps.length };
}

export function employmentTypeMix(filters: Filters, date: Date) {
  const emps = activeEmployeesAt(ds(), filters, date);
  const byType = groupBy(emps, (e) => e.employmentType);
  return (['Permanent', 'Contract', 'Part-Time', 'Intern'] as const).map((t) => ({ type: t, count: byType[t]?.length ?? 0 }));
}

export function headcountByBU(filters: Filters, date: Date) {
  const emps = activeEmployeesAt(ds(), filters, date);
  const byBU = groupBy(emps, (e) => e.bu);
  return Object.entries(byBU)
    .map(([bu, list]) => ({ bu, headcount: list.length }))
    .sort((a, b) => b.headcount - a.headcount);
}

// ---------- payroll ----------
export function basePayFor(filters: Filters, cm: CalendarMonth): number {
  return sum(activeEmployeesAt(ds(), filters, monthEndDate(cm)).map((e) => e.baseSalary));
}
export function overtimeFor(filters: Filters, cm: CalendarMonth) {
  const recs = ds().overtime.filter((o) => o.monthKey === cm.key && matchesDims(o, filters));
  return { hours: sum(recs.map((r) => r.overtimeHours)), pay: sum(recs.map((r) => r.overtimePay)), budgetHours: sum(recs.map((r) => r.budgetHours)) };
}
export function statutoryFor(basePay: number): number {
  return basePay * 0.175; // EPF + SOCSO + EIS + HRDF approx employer contribution
}
export function payrollCostFor(filters: Filters, cm: CalendarMonth): number {
  const base = basePayFor(filters, cm);
  const ot = overtimeFor(filters, cm).pay;
  return base + ot + statutoryFor(base);
}
export function revenueFor(filters: Filters, cm: CalendarMonth): number {
  return sum(ds().business.filter((b) => b.monthKey === cm.key && matchesDims(b, filters)).map((b) => b.revenue));
}
export function payrollTrend(filters: Filters, n = 12) {
  const cal = trailingMonths(filters, ds().calendar, n);
  return cal.map((cm) => {
    const base = basePayFor(filters, cm);
    const ot = overtimeFor(filters, cm).pay;
    const statutory = statutoryFor(base);
    return { month: cm.label, key: cm.key, base, overtime: ot, statutory, total: base + ot + statutory };
  });
}
export function overtimeByDept(filters: Filters, cm: CalendarMonth) {
  const recs = ds().overtime.filter((o) => o.monthKey === cm.key && matchesDims(o, filters));
  const byDept = groupBy(recs, (r) => r.department);
  return Object.entries(byDept)
    .map(([department, list]) => ({
      department,
      actualHours: sum(list.map((r) => r.overtimeHours)),
      budgetHours: sum(list.map((r) => r.budgetHours)),
      pay: sum(list.map((r) => r.overtimePay)),
    }))
    .sort((a, b) => b.actualHours - a.actualHours);
}
export function payrollPctRevenueByOutlet(filters: Filters, cm: CalendarMonth) {
  return ds().outlets
    .filter((o) => matchesDims(o, filters) && (o.bu === 'Retail Stores' || o.bu === 'F&B Outlets' || o.bu === 'Warehouse & Logistics'))
    .map((o) => {
      const outletFilter = { ...filters, outletId: o.id };
      const payroll = payrollCostFor(outletFilter, cm);
      const revenue = revenueFor(outletFilter, cm);
      return { outlet: o.name, payroll, revenue, pct: revenue > 0 ? (payroll / revenue) * 100 : 0 };
    })
    .sort((a, b) => b.pct - a.pct);
}

// ---------- attendance ----------
export function attendanceRecords(filters: Filters, cm: CalendarMonth) {
  return ds().attendance.filter((a) => a.monthKey === cm.key && matchesDims(a, filters));
}
export function absenteeismTrend(filters: Filters, n = 12) {
  const cal = trailingMonths(filters, ds().calendar, n);
  return cal.map((cm) => {
    const recs = attendanceRecords(filters, cm);
    const totalAvailable = sum(recs.map((r) => r.headcount * r.workingDays));
    const totalAbsent = sum(recs.map((r) => r.absentDays));
    return { month: cm.label, key: cm.key, rate: totalAvailable > 0 ? (totalAbsent / totalAvailable) * 100 : 0 };
  });
}
export function leaveByType(filters: Filters, n = 12) {
  const cal = trailingMonths(filters, ds().calendar, n);
  const recs = cal.flatMap((cm) => attendanceRecords(filters, cm));
  const types = ['Annual', 'Medical', 'Emergency', 'Unpaid', 'Maternity/Paternity'] as const;
  return types.map((t) => ({ type: t, days: sum(recs.map((r) => r.leaveDays[t])) }));
}
export function lateClockInsTrend(filters: Filters, n = 12) {
  const cal = trailingMonths(filters, ds().calendar, n);
  return cal.map((cm) => ({ month: cm.label, key: cm.key, late: sum(attendanceRecords(filters, cm).map((r) => r.lateClockIns)) }));
}
export function lateClockInsByDept(filters: Filters, cm: CalendarMonth) {
  const recs = attendanceRecords(filters, cm);
  const byDept = groupBy(recs, (r) => r.department);
  return Object.entries(byDept)
    .map(([department, list]) => ({ department, late: sum(list.map((r) => r.lateClockIns)) }))
    .sort((a, b) => b.late - a.late);
}

// ---------- recruitment ----------
export function recruitmentRecords(filters: Filters, months: CalendarMonth[]) {
  const keys = new Set(months.map((m) => m.key));
  return ds().recruitment.filter((r) => keys.has(r.monthKey) && matchesDims(r, filters));
}
export function recruitmentFunnel(filters: Filters, n = 3) {
  const cal = trailingMonths(filters, ds().calendar, n);
  const recs = recruitmentRecords(filters, cal);
  return [
    { label: 'Applied', value: sum(recs.map((r) => r.applied)) },
    { label: 'Screened', value: sum(recs.map((r) => r.screened)) },
    { label: 'Interviewed', value: sum(recs.map((r) => r.interviewed)) },
    { label: 'Offered', value: sum(recs.map((r) => r.offered)) },
    { label: 'Joined', value: sum(recs.map((r) => r.joined)) },
  ];
}
export function timeToHireTrend(filters: Filters, n = 12) {
  const cal = trailingMonths(filters, ds().calendar, n);
  return cal.map((cm) => {
    const recs = recruitmentRecords(filters, [cm]).filter((r) => r.joined > 0);
    const weighted = sum(recs.map((r) => r.avgTimeToHireDays * r.joined));
    const totalJoined = sum(recs.map((r) => r.joined));
    return { month: cm.label, key: cm.key, days: totalJoined > 0 ? weighted / totalJoined : 0 };
  });
}
export function costPerHireTrend(filters: Filters, n = 12) {
  const cal = trailingMonths(filters, ds().calendar, n);
  return cal.map((cm) => {
    const recs = recruitmentRecords(filters, [cm]).filter((r) => r.joined > 0);
    const weighted = sum(recs.map((r) => r.costPerHire * r.joined));
    const totalJoined = sum(recs.map((r) => r.joined));
    return { month: cm.label, key: cm.key, cost: totalJoined > 0 ? weighted / totalJoined : 0 };
  });
}
export function vacancyAging(filters: Filters, cm: CalendarMonth) {
  const recs = recruitmentRecords(filters, [cm]).filter((r) => r.openVacancies > 0);
  const byDept = groupBy(recs, (r) => r.department);
  return Object.entries(byDept)
    .map(([department, list]) => ({
      department,
      openVacancies: sum(list.map((r) => r.openVacancies)),
      avgAgeDays: avg(list.map((r) => r.avgVacancyAgeDays)),
    }))
    .sort((a, b) => b.openVacancies - a.openVacancies);
}

// ---------- turnover ----------
export function turnoverTrend(filters: Filters, n = 12) {
  const cal = trailingMonths(filters, ds().calendar, n);
  return cal.map((cm) => {
    const resigns = resignationsInMonth(filters, cm);
    const voluntary = resigns.filter((e) => e.exitType === 'Voluntary').length;
    const involuntary = resigns.length - voluntary;
    const hc = headcountAt(filters, monthEndDate(cm));
    return { month: cm.label, key: cm.key, voluntary, involuntary, total: resigns.length, rate: hc > 0 ? (resigns.length / hc) * 100 : 0 };
  });
}
export function earlyAttritionRate(filters: Filters, n = 12): number {
  const cal = trailingMonths(filters, ds().calendar, n);
  const resigns = cal.flatMap((cm) => resignationsInMonth(filters, cm));
  if (resigns.length === 0) return 0;
  const early = resigns.filter((e) => e.exitDate && e.exitDate.getTime() - e.joinDate.getTime() < 365 * 86400000).length;
  return (early / resigns.length) * 100;
}
export function turnoverHeatmapByDept(filters: Filters, n = 6) {
  const cal = trailingMonths(filters, ds().calendar, n);
  const depts = deptScope(filters);
  return { months: cal.map((c) => c.label), depts, cell: (dept: string, monthLabel: string) => {
    const cm = cal.find((c) => c.label === monthLabel)!;
    const deptFilters = filters;
    const resigns = resignationsInMonth(deptFilters, cm).filter((e) => e.department === dept);
    const hc = ds().employees.filter((e) => matchesDims(e, deptFilters) && e.department === dept && e.joinDate <= monthEndDate(cm) && (e.exitDate === null || e.exitDate > monthEndDate(cm))).length;
    return hc > 0 ? Math.round((resigns.length / hc) * 1000) / 10 : 0;
  } };
}

// ---------- training ----------
export function trainingRecords(filters: Filters, months: CalendarMonth[]) {
  const keys = new Set(months.map((m) => m.key));
  return ds().training.filter((t) => keys.has(t.monthKey) && matchesDims(t, filters) && (filters.employmentType === 'ALL' || t.employmentType === filters.employmentType));
}
export function trainingHoursTrend(filters: Filters, n = 12) {
  const cal = trailingMonths(filters, ds().calendar, n);
  return cal.map((cm) => {
    const recs = trainingRecords(filters, [cm]);
    const hours = sum(recs.map((r) => r.trainingHours));
    const hc = sum(recs.map((r) => r.headcount));
    return { month: cm.label, key: cm.key, hoursPerEmployee: hc > 0 ? hours / hc : 0 };
  });
}
export function complianceTrend(filters: Filters, n = 12) {
  const cal = trailingMonths(filters, ds().calendar, n);
  return cal.map((cm) => {
    const recs = trainingRecords(filters, [cm]);
    const due = sum(recs.map((r) => r.mandatoryDue));
    const done = sum(recs.map((r) => r.mandatoryCompleted));
    return { month: cm.label, key: cm.key, rate: due > 0 ? (done / due) * 100 : 0 };
  });
}
export function hrdcClaimsTrend(filters: Filters, n = 12) {
  const cal = trailingMonths(filters, ds().calendar, n);
  return cal.map((cm) => {
    const recs = trainingRecords(filters, [cm]);
    return { month: cm.label, key: cm.key, eligible: sum(recs.map((r) => r.hrdcClaimEligible)), submitted: sum(recs.map((r) => r.hrdcClaimSubmitted)) };
  });
}

// ---------- engagement ----------
export function engagementRecords(filters: Filters) {
  return ds().engagement.filter((e) => matchesDims(e, filters));
}
export function engagementTrend(filters: Filters) {
  const recs = engagementRecords(filters);
  const byQ = groupBy(recs, (r) => r.quarterKey);
  return Object.entries(byQ)
    .map(([q, list]) => ({
      quarter: q,
      score: avg(list.map((r) => r.engagementScore)),
      eNPS: avg(list.map((r) => r.eNPS)),
      respondents: sum(list.map((r) => r.respondents)),
      recognitions: sum(list.map((r) => r.recognitionsGiven)),
    }))
    .sort((a, b) => (a.quarter > b.quarter ? 1 : -1))
    .slice(-8);
}
export function grievanceBreakdown(filters: Filters) {
  const recs = engagementRecords(filters).slice(-Math.max(1, ds().outlets.length * 2));
  const cats = ['Compensation', 'Conduct', 'Workload', 'Facilities', 'Other'] as const;
  return cats.map((c) => ({ category: c, count: sum(recs.map((r) => r.grievances[c])) }));
}

// ---------- safety ----------
export function incidentRecords(filters: Filters, months: CalendarMonth[]) {
  const keys = new Set(months.map((m) => m.key));
  return ds().incidents.filter((i) => keys.has(i.monthKey) && matchesDims(i, filters));
}
export function ltifrTrend(filters: Filters, n = 12) {
  const cal = trailingMonths(filters, ds().calendar, n);
  let cumIncidents = 0;
  let cumHours = 0;
  return cal.map((cm) => {
    const recs = incidentRecords(filters, [cm]);
    const lti = sum(recs.filter((r) => r.severity === 'Lost Time Injury').map((r) => r.count));
    const hours = sum(Array.from(new Set(recs.map((r) => r.outletId))).map((oid) => recs.find((r) => r.outletId === oid)!.hoursWorked));
    cumIncidents += lti;
    cumHours += hours;
    return { month: cm.label, key: cm.key, ltifr: cumHours > 0 ? (cumIncidents * 1_000_000) / cumHours : 0 };
  });
}
export function incidentsByOutlet(filters: Filters, n = 6) {
  const cal = trailingMonths(filters, ds().calendar, n);
  const recs = incidentRecords(filters, cal);
  const byOutlet = groupBy(recs, (r) => r.outletId);
  return Object.entries(byOutlet)
    .map(([outletId, list]) => ({
      outlet: ds().outlets.find((o) => o.id === outletId)?.name ?? outletId,
      count: sum(list.map((r) => r.count)),
      lti: sum(list.filter((r) => r.severity === 'Lost Time Injury').map((r) => r.count)),
    }))
    .filter((r) => r.count > 0)
    .sort((a, b) => b.count - a.count);
}
export function complianceByOutlet(filters: Filters, cm: CalendarMonth) {
  const recs = incidentRecords(filters, [cm]);
  const byOutlet = groupBy(recs, (r) => r.outletId);
  return Object.entries(byOutlet)
    .map(([outletId, list]) => ({ outlet: ds().outlets.find((o) => o.id === outletId)?.name ?? outletId, score: avg(list.map((r) => r.complianceAuditScore)) }))
    .sort((a, b) => a.score - b.score);
}

// ---------- business performance ----------
export function businessTrend(filters: Filters, n = 12) {
  const cal = trailingMonths(filters, ds().calendar, n);
  return cal.map((cm) => ({
    month: cm.label,
    key: cm.key,
    revenue: revenueFor(filters, cm),
    visitors: sum(ds().business.filter((b) => b.monthKey === cm.key && matchesDims(b, filters)).map((b) => b.visitors)),
    headcount: headcountAt(filters, monthEndDate(cm)),
    labourCost: payrollCostFor(filters, cm),
  }));
}

export { endMonth, monthEndDate, monthStartDate, trailingMonths };
