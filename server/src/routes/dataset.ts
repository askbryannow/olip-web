import { Router } from 'express';
import { db } from '../db.js';

export const datasetRouter = Router();

datasetRouter.get('/', (_req, res) => {
  const outlets = (db.prepare('SELECT * FROM outlets').all() as Record<string, unknown>[]).map((o) => ({
    ...o,
    departments: JSON.parse(o.departments as string),
  }));
  const employees = db.prepare('SELECT * FROM employees').all();
  const overtime = db.prepare('SELECT * FROM overtime').all();
  const attendance = (db.prepare('SELECT * FROM attendance').all() as Record<string, unknown>[]).map((a) => ({
    monthKey: a.monthKey,
    outletId: a.outletId,
    bu: a.bu,
    department: a.department,
    state: a.state,
    workingDays: a.workingDays,
    absentDays: a.absentDays,
    lateClockIns: a.lateClockIns,
    headcount: a.headcount,
    leaveDays: {
      Annual: a.leaveAnnual,
      Medical: a.leaveMedical,
      Emergency: a.leaveEmergency,
      Unpaid: a.leaveUnpaid,
      'Maternity/Paternity': a.leaveMaternityPaternity,
    },
  }));
  const recruitment = db.prepare('SELECT * FROM recruitment').all();
  const training = db.prepare('SELECT * FROM training').all();
  const engagement = (db.prepare('SELECT * FROM engagement').all() as Record<string, unknown>[]).map((e) => ({
    quarterKey: e.quarterKey,
    monthKey: e.monthKey,
    outletId: e.outletId,
    bu: e.bu,
    state: e.state,
    respondents: e.respondents,
    headcount: e.headcount,
    engagementScore: e.engagementScore,
    eNPS: e.eNPS,
    grievances: {
      Compensation: e.grievanceCompensation,
      Conduct: e.grievanceConduct,
      Workload: e.grievanceWorkload,
      Facilities: e.grievanceFacilities,
      Other: e.grievanceOther,
    },
    recognitionsGiven: e.recognitionsGiven,
  }));
  const incidents = db.prepare('SELECT * FROM incidents').all();
  const business = db.prepare('SELECT * FROM business_performance').all();

  res.json({ outlets, employees, overtime, attendance, recruitment, training, engagement, incidents, business });
});
