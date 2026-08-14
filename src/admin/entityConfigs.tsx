import type { EntityConfig } from './types';
import { currentMonthKey } from './types';

const BUSINESS_UNITS = ['Retail Stores', 'F&B Outlets', 'Warehouse & Logistics', 'Corporate Support', 'Marketing & Digital'];
const STATES = ['Selangor', 'Kuala Lumpur', 'Penang', 'Johor', 'Perak', 'Sabah', 'Sarawak', 'Melaka'];
const EMPLOYMENT_TYPES = ['Permanent', 'Contract', 'Part-Time', 'Intern'];
const LEVELS = ['Executive', 'Senior Executive', 'Manager', 'Senior Manager', 'Head'];
const SEVERITIES = ['Near Miss', 'First Aid', 'Medical Treatment', 'Lost Time Injury'];
const EXIT_TYPES = ['', 'Voluntary', 'Involuntary'];

export const outletsConfig: EntityConfig = {
  title: 'Outlets',
  description: 'The outlet/branch list used across every other table — usually set up once.',
  endpoint: '/outlets',
  fields: [
    { key: 'name', label: 'Name', kind: 'text', required: true },
    { key: 'bu', label: 'Business unit', kind: 'select', options: BUSINESS_UNITS, required: true },
    { key: 'state', label: 'State', kind: 'select', options: STATES, required: true },
    { key: 'departments', label: 'Departments (comma-separated)', kind: 'text', required: true },
    { key: 'openedYear', label: 'Opened year', kind: 'number', required: true },
  ],
  columns: ['name', 'bu', 'state', 'departments', 'openedYear'],
  defaultValues: () => ({}),
  transformForEdit: (row) => {
    let list: string[] = [];
    try {
      list = JSON.parse(row.departments as string);
    } catch {
      /* ignore */
    }
    return { ...row, departments: list.join(', ') };
  },
  transformBeforeSave: (values) => ({
    ...values,
    departments: JSON.stringify(
      String(values.departments ?? '')
        .split(',')
        .map((s) => s.trim())
        .filter(Boolean),
    ),
  }),
};

export const employeesConfig: EntityConfig = {
  title: 'Employees',
  description: 'The employee master. To record a resignation or termination, edit the employee and set an exit date & type.',
  endpoint: '/employees',
  fields: [
    { key: 'name', label: 'Name', kind: 'text', required: true },
    { key: 'gender', label: 'Gender', kind: 'select', options: ['Male', 'Female'], required: true },
    { key: 'outletId', label: 'Outlet', kind: 'outlet', required: true },
    { key: 'department', label: 'Department', kind: 'department', required: true },
    { key: 'bu', label: 'Business unit', kind: 'derived' },
    { key: 'state', label: 'State', kind: 'derived' },
    { key: 'employmentType', label: 'Employment type', kind: 'select', options: EMPLOYMENT_TYPES, required: true },
    { key: 'level', label: 'Level', kind: 'select', options: LEVELS, required: true },
    { key: 'joinDate', label: 'Join date', kind: 'date', required: true },
    { key: 'baseSalary', label: 'Base salary (MYR/mo)', kind: 'number', required: true },
    { key: 'exitDate', label: 'Exit date', kind: 'date' },
    { key: 'exitType', label: 'Exit type', kind: 'select', options: EXIT_TYPES },
  ],
  columns: ['name', 'outletId', 'department', 'employmentType', 'level', 'joinDate', 'exitDate', 'baseSalary'],
};

export const overtimeConfig: EntityConfig = {
  title: 'Overtime',
  description: 'Monthly overtime hours, pay, and budget by outlet/department.',
  endpoint: '/overtime',
  fields: [
    { key: 'monthKey', label: 'Month', kind: 'month', required: true },
    { key: 'outletId', label: 'Outlet', kind: 'outlet', required: true },
    { key: 'department', label: 'Department', kind: 'department', required: true },
    { key: 'bu', label: 'Business unit', kind: 'derived' },
    { key: 'state', label: 'State', kind: 'derived' },
    { key: 'overtimeHours', label: 'Overtime hours (actual)', kind: 'number', required: true },
    { key: 'overtimePay', label: 'Overtime pay (MYR)', kind: 'number', required: true },
    { key: 'budgetHours', label: 'Budgeted hours', kind: 'number', required: true },
  ],
  columns: ['monthKey', 'outletId', 'department', 'overtimeHours', 'overtimePay', 'budgetHours'],
  defaultValues: () => ({ monthKey: currentMonthKey() }),
};

export const attendanceConfig: EntityConfig = {
  title: 'Attendance & Leave',
  description: 'Monthly absenteeism, late clock-ins, and leave days consumed by type, per outlet/department.',
  endpoint: '/attendance',
  fields: [
    { key: 'monthKey', label: 'Month', kind: 'month', required: true },
    { key: 'outletId', label: 'Outlet', kind: 'outlet', required: true },
    { key: 'department', label: 'Department', kind: 'department', required: true },
    { key: 'bu', label: 'Business unit', kind: 'derived' },
    { key: 'state', label: 'State', kind: 'derived' },
    { key: 'headcount', label: 'Headcount', kind: 'number', required: true },
    { key: 'workingDays', label: 'Working days in month', kind: 'number', required: true },
    { key: 'absentDays', label: 'Absent days (total)', kind: 'number', required: true },
    { key: 'lateClockIns', label: 'Late clock-ins (count)', kind: 'number', required: true },
    { key: 'leaveAnnual', label: 'Annual leave (days)', kind: 'number' },
    { key: 'leaveMedical', label: 'Medical leave (days)', kind: 'number' },
    { key: 'leaveEmergency', label: 'Emergency leave (days)', kind: 'number' },
    { key: 'leaveUnpaid', label: 'Unpaid leave (days)', kind: 'number' },
    { key: 'leaveMaternityPaternity', label: 'Maternity/Paternity (days)', kind: 'number' },
  ],
  columns: ['monthKey', 'outletId', 'department', 'headcount', 'absentDays', 'lateClockIns'],
  defaultValues: () => ({ monthKey: currentMonthKey() }),
};

export const recruitmentConfig: EntityConfig = {
  title: 'Recruitment',
  description: 'Monthly applicant funnel, vacancies, and hiring cost by outlet/department.',
  endpoint: '/recruitment',
  fields: [
    { key: 'monthKey', label: 'Month', kind: 'month', required: true },
    { key: 'outletId', label: 'Outlet', kind: 'outlet', required: true },
    { key: 'department', label: 'Department', kind: 'department', required: true },
    { key: 'bu', label: 'Business unit', kind: 'derived' },
    { key: 'state', label: 'State', kind: 'derived' },
    { key: 'applied', label: 'Applied', kind: 'number', required: true },
    { key: 'screened', label: 'Screened', kind: 'number', required: true },
    { key: 'interviewed', label: 'Interviewed', kind: 'number', required: true },
    { key: 'offered', label: 'Offered', kind: 'number', required: true },
    { key: 'joined', label: 'Joined', kind: 'number', required: true },
    { key: 'openVacancies', label: 'Open vacancies', kind: 'number', required: true },
    { key: 'avgVacancyAgeDays', label: 'Avg vacancy age (days)', kind: 'number', required: true },
    { key: 'avgTimeToHireDays', label: 'Avg time to hire (days)', kind: 'number', required: true },
    { key: 'costPerHire', label: 'Cost per hire (MYR)', kind: 'number', required: true },
  ],
  columns: ['monthKey', 'outletId', 'department', 'applied', 'joined', 'openVacancies'],
  defaultValues: () => ({ monthKey: currentMonthKey() }),
};

export const trainingConfig: EntityConfig = {
  title: 'Training & HRDC',
  description: 'Monthly training hours, mandatory compliance, and HRD Corp claims by outlet/department/employment type.',
  endpoint: '/training',
  fields: [
    { key: 'monthKey', label: 'Month', kind: 'month', required: true },
    { key: 'outletId', label: 'Outlet', kind: 'outlet', required: true },
    { key: 'department', label: 'Department', kind: 'department', required: true },
    { key: 'employmentType', label: 'Employment type', kind: 'select', options: EMPLOYMENT_TYPES, required: true },
    { key: 'bu', label: 'Business unit', kind: 'derived' },
    { key: 'state', label: 'State', kind: 'derived' },
    { key: 'headcount', label: 'Headcount', kind: 'number', required: true },
    { key: 'trainingHours', label: 'Training hours (total)', kind: 'number', required: true },
    { key: 'mandatoryDue', label: 'Mandatory training due', kind: 'number', required: true },
    { key: 'mandatoryCompleted', label: 'Mandatory training completed', kind: 'number', required: true },
    { key: 'hrdcClaimEligible', label: 'HRDC eligible (MYR)', kind: 'number', required: true },
    { key: 'hrdcClaimSubmitted', label: 'HRDC submitted (MYR)', kind: 'number', required: true },
  ],
  columns: ['monthKey', 'outletId', 'department', 'employmentType', 'trainingHours', 'mandatoryCompleted'],
  defaultValues: () => ({ monthKey: currentMonthKey() }),
};

export const engagementConfig: EntityConfig = {
  title: 'Engagement Surveys',
  description: 'Quarterly engagement score, eNPS, grievances, and recognition by outlet.',
  endpoint: '/engagement',
  fields: [
    { key: 'quarterKey', label: 'Quarter (e.g. 2026-Q3)', kind: 'text', required: true },
    { key: 'monthKey', label: 'Representative month', kind: 'month', required: true },
    { key: 'outletId', label: 'Outlet', kind: 'outlet', required: true },
    { key: 'bu', label: 'Business unit', kind: 'derived' },
    { key: 'state', label: 'State', kind: 'derived' },
    { key: 'headcount', label: 'Headcount', kind: 'number', required: true },
    { key: 'respondents', label: 'Survey respondents', kind: 'number', required: true },
    { key: 'engagementScore', label: 'Engagement score (0-100)', kind: 'number', required: true },
    { key: 'eNPS', label: 'eNPS (-100 to 100)', kind: 'number', required: true },
    { key: 'grievanceCompensation', label: 'Grievances: Compensation', kind: 'number' },
    { key: 'grievanceConduct', label: 'Grievances: Conduct', kind: 'number' },
    { key: 'grievanceWorkload', label: 'Grievances: Workload', kind: 'number' },
    { key: 'grievanceFacilities', label: 'Grievances: Facilities', kind: 'number' },
    { key: 'grievanceOther', label: 'Grievances: Other', kind: 'number' },
    { key: 'recognitionsGiven', label: 'Recognitions given', kind: 'number', required: true },
  ],
  columns: ['quarterKey', 'outletId', 'engagementScore', 'eNPS', 'recognitionsGiven'],
  defaultValues: () => ({ monthKey: currentMonthKey() }),
};

export const incidentsConfig: EntityConfig = {
  title: 'Safety Incidents',
  description: 'Monthly incident counts by severity, hours worked, and compliance audit score, per outlet.',
  endpoint: '/incidents',
  fields: [
    { key: 'monthKey', label: 'Month', kind: 'month', required: true },
    { key: 'outletId', label: 'Outlet', kind: 'outlet', required: true },
    { key: 'bu', label: 'Business unit', kind: 'derived' },
    { key: 'state', label: 'State', kind: 'derived' },
    { key: 'severity', label: 'Severity', kind: 'select', options: SEVERITIES, required: true },
    { key: 'count', label: 'Count', kind: 'number', required: true },
    { key: 'hoursWorked', label: 'Hours worked (outlet, month)', kind: 'number', required: true },
    { key: 'daysLost', label: 'Days lost', kind: 'number' },
    { key: 'complianceAuditScore', label: 'Compliance audit score (0-100)', kind: 'number', required: true },
  ],
  columns: ['monthKey', 'outletId', 'severity', 'count', 'complianceAuditScore'],
  defaultValues: () => ({ monthKey: currentMonthKey() }),
};

export const businessConfig: EntityConfig = {
  title: 'Business Performance',
  description: 'Monthly revenue and visitor counts by outlet.',
  endpoint: '/business',
  fields: [
    { key: 'monthKey', label: 'Month', kind: 'month', required: true },
    { key: 'outletId', label: 'Outlet', kind: 'outlet', required: true },
    { key: 'bu', label: 'Business unit', kind: 'derived' },
    { key: 'state', label: 'State', kind: 'derived' },
    { key: 'revenue', label: 'Revenue (MYR)', kind: 'number', required: true },
    { key: 'visitors', label: 'Visitors', kind: 'number', required: true },
  ],
  columns: ['monthKey', 'outletId', 'revenue', 'visitors'],
  defaultValues: () => ({ monthKey: currentMonthKey() }),
};
