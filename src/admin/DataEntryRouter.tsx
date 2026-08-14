import { ChecklistPage } from './ChecklistPage';
import { EntityTable } from './EntityTable';
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

export function DataEntryRouter({ page }: { page: DataEntryPageKey; onNavigate: (p: DataEntryPageKey) => void }) {
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
