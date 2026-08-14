// Core dimensional types for the HR & business performance data model.

export type BusinessUnit =
  | 'Retail Stores'
  | 'F&B Outlets'
  | 'Warehouse & Logistics'
  | 'Corporate Support'
  | 'Marketing & Digital';

export type EmploymentType = 'Permanent' | 'Contract' | 'Part-Time' | 'Intern';

export type Gender = 'Male' | 'Female';

export type MYState =
  | 'Selangor'
  | 'Kuala Lumpur'
  | 'Penang'
  | 'Johor'
  | 'Perak'
  | 'Sabah'
  | 'Sarawak'
  | 'Melaka';

export type Department =
  | 'Sales'
  | 'Store Operations'
  | 'F&B Service'
  | 'Kitchen'
  | 'Warehouse'
  | 'Logistics'
  | 'Human Resources'
  | 'Finance'
  | 'IT'
  | 'Marketing'
  | 'Customer Service'
  | 'Maintenance';

export interface Outlet {
  id: string;
  name: string;
  bu: BusinessUnit;
  state: MYState;
  departments: Department[];
  openedYear: number;
}

export interface CalendarMonth {
  key: string; // '2025-01'
  label: string; // 'Jan 2025'
  year: number;
  month: number; // 1-12
  date: Date;
  isCurrent: boolean;
}

export type ExitType = 'Voluntary' | 'Involuntary' | null;

export interface Employee {
  id: string;
  name: string;
  gender: Gender;
  bu: BusinessUnit;
  department: Department;
  outletId: string;
  state: MYState;
  employmentType: EmploymentType;
  level: 'Executive' | 'Senior Executive' | 'Manager' | 'Senior Manager' | 'Head';
  joinDate: Date;
  exitDate: Date | null;
  exitType: ExitType;
  baseSalary: number; // monthly MYR
}

export interface OvertimeRecord {
  monthKey: string;
  outletId: string;
  bu: BusinessUnit;
  department: Department;
  state: MYState;
  overtimeHours: number;
  overtimePay: number;
  budgetHours: number;
}

export interface AttendanceRecord {
  monthKey: string;
  outletId: string;
  bu: BusinessUnit;
  department: Department;
  state: MYState;
  workingDays: number;
  absentDays: number;
  lateClockIns: number;
  headcount: number;
  leaveDays: {
    Annual: number;
    Medical: number;
    Emergency: number;
    Unpaid: number;
    'Maternity/Paternity': number;
  };
}

export interface RecruitmentRecord {
  monthKey: string;
  outletId: string;
  bu: BusinessUnit;
  department: Department;
  state: MYState;
  applied: number;
  screened: number;
  interviewed: number;
  offered: number;
  joined: number;
  openVacancies: number;
  avgVacancyAgeDays: number;
  avgTimeToHireDays: number;
  costPerHire: number;
}

export interface TrainingRecord {
  monthKey: string;
  outletId: string;
  bu: BusinessUnit;
  department: Department;
  state: MYState;
  employmentType: EmploymentType;
  headcount: number;
  trainingHours: number;
  mandatoryDue: number;
  mandatoryCompleted: number;
  hrdcClaimEligible: number;
  hrdcClaimSubmitted: number;
}

export interface EngagementRecord {
  quarterKey: string; // '2025-Q1'
  monthKey: string; // representative month for sorting/filtering (last month of quarter)
  outletId: string;
  bu: BusinessUnit;
  state: MYState;
  respondents: number;
  headcount: number;
  engagementScore: number; // 0-100
  eNPS: number; // -100..100
  grievances: { Compensation: number; Conduct: number; Workload: number; Facilities: number; Other: number };
  recognitionsGiven: number;
}

export type IncidentSeverity = 'Near Miss' | 'First Aid' | 'Medical Treatment' | 'Lost Time Injury';

export interface IncidentRecord {
  monthKey: string;
  outletId: string;
  bu: BusinessUnit;
  state: MYState;
  severity: IncidentSeverity;
  count: number;
  hoursWorked: number;
  daysLost: number;
  complianceAuditScore: number; // 0-100, per outlet per month
}

export interface BusinessPerformanceRecord {
  monthKey: string;
  outletId: string;
  bu: BusinessUnit;
  state: MYState;
  revenue: number;
  visitors: number;
}

export interface Filters {
  monthKey: string | 'ALL';
  bu: BusinessUnit | 'ALL';
  state: MYState | 'ALL';
  outletId: string | 'ALL';
  employmentType: EmploymentType | 'ALL';
}

export interface Dataset {
  outlets: Outlet[];
  calendar: CalendarMonth[];
  employees: Employee[];
  overtime: OvertimeRecord[];
  attendance: AttendanceRecord[];
  recruitment: RecruitmentRecord[];
  training: TrainingRecord[];
  engagement: EngagementRecord[];
  incidents: IncidentRecord[];
  business: BusinessPerformanceRecord[];
}
