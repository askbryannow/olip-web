import { ChecklistPage } from './ChecklistPage';
import { EntityTable } from './EntityTable';
import { BackendRequiredNotice } from './BackendRequiredNotice';
import { useAppData } from '../lib/AppDataContext';
import {
  outletsConfig,
  employeesConfig,
  overtimeConfig,
  attendanceConfig,
  recruitmentConfig,
  trainingConfig,
  engagementConfig,
  incidentsConfig,
  businessConfig,
} from './entityConfigs';

export type DataEntryPageKey =
  | 'checklist'
  | 'outlets'
  | 'employees'
  | 'overtime'
  | 'attendance'
  | 'recruitment'
  | 'training'
  | 'engagement'
  | 'incidents'
  | 'business';

const TITLES: Record<DataEntryPageKey, string> = {
  checklist: 'Monthly Checklist',
  outlets: 'Outlets',
  employees: 'Employees',
  overtime: 'Overtime',
  attendance: 'Attendance & Leave',
  recruitment: 'Recruitment',
  training: 'Training & HRDC',
  engagement: 'Engagement Surveys',
  incidents: 'Safety Incidents',
  business: 'Business Performance',
};

export function DataEntryRouter({ page }: { page: DataEntryPageKey; onNavigate: (p: DataEntryPageKey) => void }) {
  const { source } = useAppData();
  if (source === 'standalone') {
    return <BackendRequiredNotice title={TITLES[page]} />;
  }

  switch (page) {
    case 'checklist':
      return <ChecklistPage />;
    case 'outlets':
      return <EntityTable config={outletsConfig} />;
    case 'employees':
      return <EntityTable config={employeesConfig} />;
    case 'overtime':
      return <EntityTable config={overtimeConfig} />;
    case 'attendance':
      return <EntityTable config={attendanceConfig} />;
    case 'recruitment':
      return <EntityTable config={recruitmentConfig} />;
    case 'training':
      return <EntityTable config={trainingConfig} />;
    case 'engagement':
      return <EntityTable config={engagementConfig} />;
    case 'incidents':
      return <EntityTable config={incidentsConfig} />;
    case 'business':
      return <EntityTable config={businessConfig} />;
    default:
      return null;
  }
}
