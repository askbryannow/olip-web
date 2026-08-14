import Database from 'better-sqlite3';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const dbPath = process.env.DATABASE_PATH ?? path.join(__dirname, '..', 'data.db');

export const db = new Database(dbPath);
db.pragma('journal_mode = WAL');
db.pragma('foreign_keys = ON');

db.exec(`
CREATE TABLE IF NOT EXISTS outlets (
  id TEXT PRIMARY KEY,
  name TEXT NOT NULL,
  bu TEXT NOT NULL,
  state TEXT NOT NULL,
  departments TEXT NOT NULL, -- JSON array
  openedYear INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS employees (
  id TEXT PRIMARY KEY,
  name TEXT NOT NULL,
  gender TEXT NOT NULL,
  bu TEXT NOT NULL,
  department TEXT NOT NULL,
  outletId TEXT NOT NULL,
  state TEXT NOT NULL,
  employmentType TEXT NOT NULL,
  level TEXT NOT NULL,
  joinDate TEXT NOT NULL,
  exitDate TEXT,
  exitType TEXT,
  baseSalary REAL NOT NULL
);

CREATE TABLE IF NOT EXISTS overtime (
  id TEXT PRIMARY KEY,
  monthKey TEXT NOT NULL,
  outletId TEXT NOT NULL,
  bu TEXT NOT NULL,
  department TEXT NOT NULL,
  state TEXT NOT NULL,
  overtimeHours REAL NOT NULL,
  overtimePay REAL NOT NULL,
  budgetHours REAL NOT NULL,
  UNIQUE(monthKey, outletId, department)
);

CREATE TABLE IF NOT EXISTS attendance (
  id TEXT PRIMARY KEY,
  monthKey TEXT NOT NULL,
  outletId TEXT NOT NULL,
  bu TEXT NOT NULL,
  department TEXT NOT NULL,
  state TEXT NOT NULL,
  workingDays REAL NOT NULL,
  absentDays REAL NOT NULL,
  lateClockIns REAL NOT NULL,
  headcount REAL NOT NULL,
  leaveAnnual REAL NOT NULL DEFAULT 0,
  leaveMedical REAL NOT NULL DEFAULT 0,
  leaveEmergency REAL NOT NULL DEFAULT 0,
  leaveUnpaid REAL NOT NULL DEFAULT 0,
  leaveMaternityPaternity REAL NOT NULL DEFAULT 0,
  UNIQUE(monthKey, outletId, department)
);

CREATE TABLE IF NOT EXISTS recruitment (
  id TEXT PRIMARY KEY,
  monthKey TEXT NOT NULL,
  outletId TEXT NOT NULL,
  bu TEXT NOT NULL,
  department TEXT NOT NULL,
  state TEXT NOT NULL,
  applied REAL NOT NULL,
  screened REAL NOT NULL,
  interviewed REAL NOT NULL,
  offered REAL NOT NULL,
  joined REAL NOT NULL,
  openVacancies REAL NOT NULL,
  avgVacancyAgeDays REAL NOT NULL,
  avgTimeToHireDays REAL NOT NULL,
  costPerHire REAL NOT NULL,
  UNIQUE(monthKey, outletId, department)
);

CREATE TABLE IF NOT EXISTS training (
  id TEXT PRIMARY KEY,
  monthKey TEXT NOT NULL,
  outletId TEXT NOT NULL,
  bu TEXT NOT NULL,
  department TEXT NOT NULL,
  state TEXT NOT NULL,
  employmentType TEXT NOT NULL,
  headcount REAL NOT NULL,
  trainingHours REAL NOT NULL,
  mandatoryDue REAL NOT NULL,
  mandatoryCompleted REAL NOT NULL,
  hrdcClaimEligible REAL NOT NULL,
  hrdcClaimSubmitted REAL NOT NULL,
  UNIQUE(monthKey, outletId, department, employmentType)
);

CREATE TABLE IF NOT EXISTS engagement (
  id TEXT PRIMARY KEY,
  quarterKey TEXT NOT NULL,
  monthKey TEXT NOT NULL,
  outletId TEXT NOT NULL,
  bu TEXT NOT NULL,
  state TEXT NOT NULL,
  respondents REAL NOT NULL,
  headcount REAL NOT NULL,
  engagementScore REAL NOT NULL,
  eNPS REAL NOT NULL,
  grievanceCompensation REAL NOT NULL DEFAULT 0,
  grievanceConduct REAL NOT NULL DEFAULT 0,
  grievanceWorkload REAL NOT NULL DEFAULT 0,
  grievanceFacilities REAL NOT NULL DEFAULT 0,
  grievanceOther REAL NOT NULL DEFAULT 0,
  recognitionsGiven REAL NOT NULL,
  UNIQUE(quarterKey, outletId)
);

CREATE TABLE IF NOT EXISTS incidents (
  id TEXT PRIMARY KEY,
  monthKey TEXT NOT NULL,
  outletId TEXT NOT NULL,
  bu TEXT NOT NULL,
  state TEXT NOT NULL,
  severity TEXT NOT NULL,
  count REAL NOT NULL,
  hoursWorked REAL NOT NULL,
  daysLost REAL NOT NULL,
  complianceAuditScore REAL NOT NULL,
  UNIQUE(monthKey, outletId, severity)
);

CREATE TABLE IF NOT EXISTS business_performance (
  id TEXT PRIMARY KEY,
  monthKey TEXT NOT NULL,
  outletId TEXT NOT NULL,
  bu TEXT NOT NULL,
  state TEXT NOT NULL,
  revenue REAL NOT NULL,
  visitors REAL NOT NULL,
  UNIQUE(monthKey, outletId)
);
`);

export function nextId(prefix: string): string {
  return `${prefix}_${Date.now().toString(36)}${Math.random().toString(36).slice(2, 8)}`;
}
