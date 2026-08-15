import { crudRouter, type ColumnSpec } from './generic.js';

const dims: ColumnSpec[] = [
  { name: 'bu', type: 'text', required: true },
  { name: 'department', type: 'text', required: true },
  { name: 'state', type: 'text', required: true },
];

export const outletsRouter = crudRouter(
  'outlets',
  [
    { name: 'name', type: 'text', required: true },
    { name: 'bu', type: 'text', required: true },
    { name: 'state', type: 'text', required: true },
    { name: 'departments', type: 'text', required: true },
    { name: 'openedYear', type: 'real', required: true },
  ],
  'out',
);

export const employeesRouter = crudRouter(
  'employees',
  [
    { name: 'name', type: 'text', required: true },
    { name: 'gender', type: 'text', required: true },
    { name: 'bu', type: 'text', required: true },
    { name: 'department', type: 'text', required: true },
    { name: 'outletId', type: 'text', required: true },
    { name: 'state', type: 'text', required: true },
    { name: 'employmentType', type: 'text', required: true },
    { name: 'level', type: 'text', required: true },
    { name: 'joinDate', type: 'text', required: true },
    { name: 'exitDate', type: 'text' },
    { name: 'exitType', type: 'text' },
    { name: 'baseSalary', type: 'real', required: true },
  ],
  'emp',
);

export const overtimeRouter = crudRouter(
  'overtime',
  [
    { name: 'monthKey', type: 'text', required: true },
    { name: 'outletId', type: 'text', required: true },
    ...dims,
    { name: 'overtimeHours', type: 'real', required: true },
    { name: 'overtimePay', type: 'real', required: true },
    { name: 'budgetHours', type: 'real', required: true },
  ],
  'ot',
);

export const attendanceRouter = crudRouter(
  'attendance',
  [
    { name: 'monthKey', type: 'text', required: true },
    { name: 'outletId', type: 'text', required: true },
    ...dims,
    { name: 'workingDays', type: 'real', required: true },
    { name: 'absentDays', type: 'real', required: true },
    { name: 'lateClockIns', type: 'real', required: true },
    { name: 'headcount', type: 'real', required: true },
    { name: 'leaveAnnual', type: 'real' },
    { name: 'leaveMedical', type: 'real' },
    { name: 'leaveEmergency', type: 'real' },
    { name: 'leaveUnpaid', type: 'real' },
    { name: 'leaveMaternityPaternity', type: 'real' },
  ],
  'att',
);

export const recruitmentRouter = crudRouter(
  'recruitment',
  [
    { name: 'monthKey', type: 'text', required: true },
    { name: 'outletId', type: 'text', required: true },
    ...dims,
    { name: 'applied', type: 'real', required: true },
    { name: 'screened', type: 'real', required: true },
    { name: 'interviewed', type: 'real', required: true },
    { name: 'offered', type: 'real', required: true },
    { name: 'joined', type: 'real', required: true },
    { name: 'openVacancies', type: 'real', required: true },
    { name: 'avgVacancyAgeDays', type: 'real', required: true },
    { name: 'avgTimeToHireDays', type: 'real', required: true },
    { name: 'costPerHire', type: 'real', required: true },
  ],
  'rec',
);

export const trainingRouter = crudRouter(
  'training',
  [
    { name: 'monthKey', type: 'text', required: true },
    { name: 'outletId', type: 'text', required: true },
    ...dims,
    { name: 'employmentType', type: 'text', required: true },
    { name: 'headcount', type: 'real', required: true },
    { name: 'trainingHours', type: 'real', required: true },
    { name: 'mandatoryDue', type: 'real', required: true },
    { name: 'mandatoryCompleted', type: 'real', required: true },
    { name: 'hrdcClaimEligible', type: 'real', required: true },
    { name: 'hrdcClaimSubmitted', type: 'real', required: true },
  ],
  'trn',
);

export const engagementRouter = crudRouter(
  'engagement',
  [
    { name: 'quarterKey', type: 'text', required: true },
    { name: 'monthKey', type: 'text', required: true },
    { name: 'outletId', type: 'text', required: true },
    { name: 'bu', type: 'text', required: true },
    { name: 'state', type: 'text', required: true },
    { name: 'respondents', type: 'real', required: true },
    { name: 'headcount', type: 'real', required: true },
    { name: 'engagementScore', type: 'real', required: true },
    { name: 'eNPS', type: 'real', required: true },
    { name: 'grievanceCompensation', type: 'real' },
    { name: 'grievanceConduct', type: 'real' },
    { name: 'grievanceWorkload', type: 'real' },
    { name: 'grievanceFacilities', type: 'real' },
    { name: 'grievanceOther', type: 'real' },
    { name: 'recognitionsGiven', type: 'real', required: true },
  ],
  'eng',
);

export const incidentsRouter = crudRouter(
  'incidents',
  [
    { name: 'monthKey', type: 'text', required: true },
    { name: 'outletId', type: 'text', required: true },
    { name: 'bu', type: 'text', required: true },
    { name: 'state', type: 'text', required: true },
    { name: 'severity', type: 'text', required: true },
    { name: 'count', type: 'real', required: true },
    { name: 'hoursWorked', type: 'real', required: true },
    { name: 'daysLost', type: 'real' },
    { name: 'complianceAuditScore', type: 'real', required: true },
  ],
  'inc',
);

export const businessRouter = crudRouter(
  'business_performance',
  [
    { name: 'monthKey', type: 'text', required: true },
    { name: 'outletId', type: 'text', required: true },
    { name: 'bu', type: 'text', required: true },
    { name: 'state', type: 'text', required: true },
    { name: 'revenue', type: 'real', required: true },
    { name: 'visitors', type: 'real', required: true },
  ],
  'biz',
);
