import { Rand } from './rng';
import { OUTLETS } from './outlets';
import { FIRST_NAMES_M, FIRST_NAMES_F, LAST_NAMES } from './names';
import type {
  CalendarMonth,
  Dataset,
  Department,
  Employee,
  EmploymentType,
  Gender,
  OvertimeRecord,
  AttendanceRecord,
  RecruitmentRecord,
  TrainingRecord,
  EngagementRecord,
  IncidentRecord,
  BusinessPerformanceRecord,
} from './types';

const TODAY = new Date('2026-08-14T00:00:00');
const WINDOW_MONTHS = 24;

const LEVELS = ['Executive', 'Senior Executive', 'Manager', 'Senior Manager', 'Head'] as const;
const SALARY_RANGE: Record<(typeof LEVELS)[number], [number, number]> = {
  Executive: [2600, 4000],
  'Senior Executive': [4000, 5800],
  Manager: [6200, 9200],
  'Senior Manager': [9800, 14000],
  Head: [15000, 21000],
};
const BU_SALARY_MULT: Record<string, number> = {
  'Retail Stores': 0.95,
  'F&B Outlets': 0.92,
  'Warehouse & Logistics': 1.0,
  'Corporate Support': 1.15,
  'Marketing & Digital': 1.1,
};
const FRONTLINE_DEPTS = new Set<Department>([
  'Sales', 'Store Operations', 'F&B Service', 'Kitchen', 'Warehouse', 'Logistics', 'Customer Service', 'Maintenance',
]);

function buildCalendar(): CalendarMonth[] {
  const months: CalendarMonth[] = [];
  for (let i = WINDOW_MONTHS - 1; i >= 0; i--) {
    const d = new Date(TODAY.getFullYear(), TODAY.getMonth() - i, 1);
    const key = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
    const label = d.toLocaleDateString('en-GB', { month: 'short', year: 'numeric' });
    months.push({
      key,
      label,
      year: d.getFullYear(),
      month: d.getMonth() + 1,
      date: d,
      isCurrent: i === 0,
    });
  }
  return months;
}

function monthEnd(d: Date): Date {
  return new Date(d.getFullYear(), d.getMonth() + 1, 0, 23, 59, 59);
}
function monthStart(d: Date): Date {
  return new Date(d.getFullYear(), d.getMonth(), 1);
}

function deptWeights(outletDepts: Department[]): [Department, number][] {
  return outletDepts.map((d) => [d, FRONTLINE_DEPTS.has(d) && d !== 'Maintenance' ? 3 : 1]);
}

function levelWeights(forHire: boolean): [(typeof LEVELS)[number], number][] {
  return forHire
    ? [['Executive', 50], ['Senior Executive', 28], ['Manager', 15], ['Senior Manager', 5], ['Head', 2]]
    : [['Executive', 42], ['Senior Executive', 27], ['Manager', 19], ['Senior Manager', 8], ['Head', 4]];
}

function genderWeights(dept: Department): [Gender, number][] {
  if (['Kitchen', 'Warehouse', 'Logistics', 'Maintenance'].includes(dept)) return [['Male', 74], ['Female', 26]];
  if (['F&B Service', 'Customer Service', 'Human Resources'].includes(dept)) return [['Male', 34], ['Female', 66]];
  return [['Male', 50], ['Female', 50]];
}

function employmentTypeWeights(dept: Department): [EmploymentType, number][] {
  if (dept === 'Warehouse' || dept === 'Logistics') return [['Permanent', 70], ['Contract', 20], ['Part-Time', 5], ['Intern', 5]];
  if (FRONTLINE_DEPTS.has(dept)) return [['Permanent', 55], ['Contract', 15], ['Part-Time', 25], ['Intern', 5]];
  return [['Permanent', 85], ['Contract', 10], ['Part-Time', 0], ['Intern', 5]];
}

function makeName(gender: Gender, r: Rand): string {
  const first = gender === 'Male' ? r.pick(FIRST_NAMES_M) : r.pick(FIRST_NAMES_F);
  const last = r.pick(LAST_NAMES);
  return `${first} ${last}`;
}

function randomDayInMonth(d: Date, r: Rand): Date {
  const days = new Date(d.getFullYear(), d.getMonth() + 1, 0).getDate();
  return new Date(d.getFullYear(), d.getMonth(), r.int(1, days));
}

let empSeq = 0;
function makeEmployee(
  r: Rand,
  outletId: string,
  bu: Employee['bu'],
  state: Employee['state'],
  depts: Department[],
  joinDate: Date,
  forHire: boolean,
): Employee {
  const department = r.weighted(deptWeights(depts));
  const level = r.weighted(levelWeights(forHire));
  const gender = r.weighted(genderWeights(department));
  const employmentType = r.weighted(employmentTypeWeights(department));
  const [lo, hi] = SALARY_RANGE[level];
  const mult = BU_SALARY_MULT[bu] ?? 1;
  const baseSalary = Math.round((r.float(lo, hi) * mult) / 10) * 10;
  empSeq += 1;
  return {
    id: `E${String(empSeq).padStart(5, '0')}`,
    name: makeName(gender, r),
    gender,
    bu,
    department,
    outletId,
    state,
    employmentType,
    level,
    joinDate,
    exitDate: null,
    exitType: null,
    baseSalary,
  };
}

function turnoverRateFor(bu: string, monthIdx: number): number {
  const base: Record<string, number> = {
    'Retail Stores': 0.026,
    'F&B Outlets': 0.032,
    'Warehouse & Logistics': 0.018,
    'Corporate Support': 0.01,
    'Marketing & Digital': 0.014,
  };
  // seasonal bump after year-end bonuses (Jan/Feb) and mid-year (Jun)
  const seasonal = [0.012, 0.01, 0, 0, 0, 0.006, 0, 0, 0, 0, 0, 0.004][monthIdx % 12];
  return (base[bu] ?? 0.02) + seasonal;
}

function outletTarget(r: Rand, bu: string): number {
  const ranges: Record<string, [number, number]> = {
    'Retail Stores': [26, 38],
    'F&B Outlets': [20, 32],
    'Warehouse & Logistics': [18, 26],
    'Corporate Support': [22, 26],
    'Marketing & Digital': [9, 12],
  };
  const [lo, hi] = ranges[bu] ?? [15, 25];
  return r.int(lo, hi);
}

function simulateEmployees(calendar: CalendarMonth[]): Employee[] {
  const r = new Rand(20260814);
  const employees: Employee[] = [];
  const windowStart = monthStart(calendar[0].date);

  for (const outlet of OUTLETS) {
    const target = outletTarget(r, outlet.bu);
    // seed initial workforce with historical join dates spread 3mo-8yr before window start
    for (let i = 0; i < target; i++) {
      const tenureDays = r.int(90, 8 * 365);
      const joinDate = new Date(windowStart.getTime() - tenureDays * 86400000);
      employees.push(makeEmployee(r, outlet.id, outlet.bu, outlet.state, outlet.departments, joinDate, false));
    }

    let growthDrift = r.float(0.1, 0.6); // slow headcount growth per month on average

    calendar.forEach((cm, idx) => {
      const mStart = monthStart(cm.date);
      const mEndDate = monthEnd(cm.date);
      const active = employees.filter(
        (e) => e.outletId === outlet.id && e.joinDate < mStart && (e.exitDate === null || e.exitDate > mEndDate),
      );
      const rate = turnoverRateFor(outlet.bu, idx);
      let resignCount = Math.round(active.length * rate + r.gaussLike(0, 0.6));
      resignCount = Math.max(0, Math.min(resignCount, Math.max(0, active.length - 4)));

      // pick resignations from active pool with tenure > 0
      const pool = [...active];
      for (let i = 0; i < resignCount; i++) {
        if (pool.length === 0) break;
        const idxPick = r.int(0, pool.length - 1);
        const emp = pool.splice(idxPick, 1)[0];
        emp.exitDate = randomDayInMonth(cm.date, r);
        emp.exitType = r.bool(0.7) ? 'Voluntary' : 'Involuntary';
      }

      const netGrowth = Math.round(r.gaussLike(growthDrift, 1.4));
      const currentAfterExits = active.length - resignCount;
      let hireCount = resignCount + netGrowth;
      // gently pull headcount back toward target
      if (currentAfterExits + hireCount > target * 1.25) hireCount = Math.max(resignCount, Math.round(hireCount * 0.4));
      if (currentAfterExits + hireCount < target * 0.7) hireCount += r.int(1, 3);
      hireCount = Math.max(0, hireCount);

      for (let i = 0; i < hireCount; i++) {
        const joinDate = randomDayInMonth(cm.date, r);
        employees.push(makeEmployee(r, outlet.id, outlet.bu, outlet.state, outlet.departments, joinDate, true));
      }
    });
  }

  return employees;
}

function activeAt(employees: Employee[], date: Date): Employee[] {
  return employees.filter((e) => e.joinDate <= date && (e.exitDate === null || e.exitDate > date));
}

function generateOvertime(calendar: CalendarMonth[], employees: Employee[], r: Rand): OvertimeRecord[] {
  const out: OvertimeRecord[] = [];
  for (const outlet of OUTLETS) {
    for (const dept of outlet.departments) {
      if (!FRONTLINE_DEPTS.has(dept)) continue;
      calendar.forEach((cm) => {
        const active = activeAt(employees, monthEnd(cm.date)).filter(
          (e) => e.outletId === outlet.id && e.department === dept,
        );
        if (active.length === 0) return;
        const avgOtHoursPerHead = r.float(4, 14);
        const overtimeHours = Math.round(active.length * avgOtHoursPerHead);
        const avgHourlyRate = active.reduce((s, e) => s + e.baseSalary, 0) / active.length / (26 * 8);
        const overtimePay = Math.round(overtimeHours * avgHourlyRate * 1.5);
        const budgetHours = Math.round(overtimeHours * r.float(0.75, 1.25));
        out.push({
          monthKey: cm.key,
          outletId: outlet.id,
          bu: outlet.bu,
          department: dept,
          state: outlet.state,
          overtimeHours,
          overtimePay,
          budgetHours,
        });
      });
    }
  }
  return out;
}

function generateAttendance(calendar: CalendarMonth[], employees: Employee[], r: Rand): AttendanceRecord[] {
  const out: AttendanceRecord[] = [];
  for (const outlet of OUTLETS) {
    for (const dept of outlet.departments) {
      calendar.forEach((cm) => {
        const active = activeAt(employees, monthEnd(cm.date)).filter(
          (e) => e.outletId === outlet.id && e.department === dept,
        );
        if (active.length === 0) return;
        const workingDays = 22;
        const absenteeismRate = r.float(0.015, 0.05);
        const absentDays = Math.round(active.length * workingDays * absenteeismRate);
        const lateClockIns = Math.round(active.length * r.float(0.3, 1.8));
        out.push({
          monthKey: cm.key,
          outletId: outlet.id,
          bu: outlet.bu,
          department: dept,
          state: outlet.state,
          workingDays,
          absentDays,
          lateClockIns,
          headcount: active.length,
          leaveDays: {
            Annual: Math.round(active.length * r.float(0.6, 1.1)),
            Medical: Math.round(active.length * r.float(0.3, 0.7)),
            Emergency: Math.round(active.length * r.float(0.05, 0.2)),
            Unpaid: Math.round(active.length * r.float(0, 0.15)),
            'Maternity/Paternity': r.bool(0.15) ? r.int(1, 3) : 0,
          },
        });
      });
    }
  }
  return out;
}

function generateRecruitment(calendar: CalendarMonth[], employees: Employee[], r: Rand): RecruitmentRecord[] {
  const out: RecruitmentRecord[] = [];
  for (const outlet of OUTLETS) {
    for (const dept of outlet.departments) {
      calendar.forEach((cm) => {
        const joined = employees.filter(
          (e) =>
            e.outletId === outlet.id &&
            e.department === dept &&
            e.joinDate.getFullYear() === cm.year &&
            e.joinDate.getMonth() + 1 === cm.month,
        ).length;
        if (joined === 0 && r.next() > 0.15) return; // most dept/months with no hire skip lightly (some show open vacancy only)
        const offered = joined + r.int(0, 2);
        const interviewed = offered + r.int(2, 6);
        const screened = interviewed + r.int(3, 10);
        const applied = screened + r.int(5, 25);
        out.push({
          monthKey: cm.key,
          outletId: outlet.id,
          bu: outlet.bu,
          department: dept,
          state: outlet.state,
          applied,
          screened,
          interviewed,
          offered,
          joined,
          openVacancies: r.int(0, 4),
          avgVacancyAgeDays: r.int(8, 55),
          avgTimeToHireDays: r.int(14, 45),
          costPerHire: Math.round(r.float(600, 2400)),
        });
      });
    }
  }
  return out;
}

function generateTraining(calendar: CalendarMonth[], employees: Employee[], r: Rand): TrainingRecord[] {
  const out: TrainingRecord[] = [];
  for (const outlet of OUTLETS) {
    for (const dept of outlet.departments) {
      for (const et of ['Permanent', 'Contract', 'Part-Time', 'Intern'] as EmploymentType[]) {
        calendar.forEach((cm) => {
          const active = activeAt(employees, monthEnd(cm.date)).filter(
            (e) => e.outletId === outlet.id && e.department === dept && e.employmentType === et,
          );
          if (active.length === 0) return;
          const trainingHours = Math.round(active.length * r.float(0.8, 4.5));
          const mandatoryDue = active.length;
          const mandatoryCompleted = Math.round(mandatoryDue * r.float(0.7, 1));
          const hrdcClaimEligible = Math.round(trainingHours * r.float(35, 55));
          const hrdcClaimSubmitted = Math.round(hrdcClaimEligible * r.float(0.55, 0.95));
          out.push({
            monthKey: cm.key,
            outletId: outlet.id,
            bu: outlet.bu,
            department: dept,
            state: outlet.state,
            employmentType: et,
            headcount: active.length,
            trainingHours,
            mandatoryDue,
            mandatoryCompleted,
            hrdcClaimEligible,
            hrdcClaimSubmitted,
          });
        });
      }
    }
  }
  return out;
}

function quarterOf(cm: CalendarMonth): string {
  const q = Math.floor((cm.month - 1) / 3) + 1;
  return `${cm.year}-Q${q}`;
}

function generateEngagement(calendar: CalendarMonth[], employees: Employee[], r: Rand): EngagementRecord[] {
  const out: EngagementRecord[] = [];
  const quarterMonths = new Map<string, CalendarMonth>();
  calendar.forEach((cm) => quarterMonths.set(quarterOf(cm), cm)); // last month in calendar order wins -> latest month of quarter

  for (const outlet of OUTLETS) {
    let trend = r.float(60, 72);
    for (const [qKey, cm] of quarterMonths) {
      const active = activeAt(employees, monthEnd(cm.date)).filter((e) => e.outletId === outlet.id);
      if (active.length === 0) continue;
      trend += r.gaussLike(0.4, 2.2);
      trend = Math.max(48, Math.min(88, trend));
      const respondents = Math.round(active.length * r.float(0.55, 0.9));
      const eNPS = Math.round(trend * 1.4 - 90 + r.gaussLike(0, 6));
      out.push({
        quarterKey: qKey,
        monthKey: cm.key,
        outletId: outlet.id,
        bu: outlet.bu,
        state: outlet.state,
        respondents,
        headcount: active.length,
        engagementScore: Math.round(trend),
        eNPS: Math.max(-100, Math.min(100, eNPS)),
        grievances: {
          Compensation: r.int(0, 3),
          Conduct: r.int(0, 2),
          Workload: r.int(0, 3),
          Facilities: r.int(0, 2),
          Other: r.int(0, 2),
        },
        recognitionsGiven: r.int(2, Math.max(3, Math.round(active.length * 0.4))),
      });
    }
  }
  return out;
}

function generateIncidents(calendar: CalendarMonth[], employees: Employee[], r: Rand): IncidentRecord[] {
  const out: IncidentRecord[] = [];
  const severities: IncidentRecord['severity'][] = ['Near Miss', 'First Aid', 'Medical Treatment', 'Lost Time Injury'];
  for (const outlet of OUTLETS) {
    calendar.forEach((cm) => {
      const active = activeAt(employees, monthEnd(cm.date)).filter((e) => e.outletId === outlet.id);
      if (active.length === 0) return;
      const hoursWorked = active.length * 22 * 8;
      const riskFactor = outlet.bu === 'Warehouse & Logistics' ? 1.6 : outlet.bu === 'F&B Outlets' ? 1.3 : outlet.bu === 'Retail Stores' ? 1.0 : 0.4;
      severities.forEach((sev) => {
        const baseRate: Record<string, number> = { 'Near Miss': 0.06, 'First Aid': 0.03, 'Medical Treatment': 0.012, 'Lost Time Injury': 0.004 };
        const count = r.next() < baseRate[sev] * riskFactor * active.length ? r.int(1, sev === 'Near Miss' ? 3 : 1) : 0;
        if (count === 0 && r.next() > 0.05) return;
        out.push({
          monthKey: cm.key,
          outletId: outlet.id,
          bu: outlet.bu,
          state: outlet.state,
          severity: sev,
          count,
          hoursWorked,
          daysLost: sev === 'Lost Time Injury' ? count * r.int(2, 12) : 0,
          complianceAuditScore: Math.round(r.float(78, 99)),
        });
      });
    });
  }
  return out;
}

function generateBusiness(calendar: CalendarMonth[], employees: Employee[], r: Rand): BusinessPerformanceRecord[] {
  const out: BusinessPerformanceRecord[] = [];
  for (const outlet of OUTLETS) {
    if (outlet.bu === 'Corporate Support' || outlet.bu === 'Marketing & Digital') continue; // no direct revenue/footfall
    let revBase = r.float(180000, 420000);
    calendar.forEach((cm) => {
      const active = activeAt(employees, monthEnd(cm.date)).filter((e) => e.outletId === outlet.id);
      const seasonal = [1.15, 0.95, 0.98, 1.0, 1.0, 1.05, 1.0, 1.0, 1.0, 1.05, 1.1, 1.3][cm.month - 1];
      revBase *= 1 + r.gaussLike(0.004, 0.02);
      const revenue = Math.round(revBase * seasonal * (0.9 + active.length / 60));
      const visitors = Math.round((revenue / r.float(45, 85)) * r.float(0.9, 1.1));
      out.push({ monthKey: cm.key, outletId: outlet.id, bu: outlet.bu, state: outlet.state, revenue, visitors });
    });
  }
  return out;
}

let cached: Dataset | null = null;

export function getDataset(): Dataset {
  if (cached) return cached;
  const calendar = buildCalendar();
  const employees = simulateEmployees(calendar);
  const r = new Rand(777001);
  cached = {
    outlets: OUTLETS,
    calendar,
    employees,
    overtime: generateOvertime(calendar, employees, r),
    attendance: generateAttendance(calendar, employees, r),
    recruitment: generateRecruitment(calendar, employees, r),
    training: generateTraining(calendar, employees, r),
    engagement: generateEngagement(calendar, employees, r),
    incidents: generateIncidents(calendar, employees, r),
    business: generateBusiness(calendar, employees, r),
  };
  return cached;
}
