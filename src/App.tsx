import { useState } from 'react';
import { FiltersProvider } from './lib/FiltersContext';
import { Sidebar, type PageKey } from './components/Sidebar';
import { FilterBar } from './components/FilterBar';
import { ExecutiveOverview } from './pages/ExecutiveOverview';
import { RecruitmentPage } from './pages/RecruitmentPage';
import { TurnoverPage } from './pages/TurnoverPage';
import { PayrollPage } from './pages/PayrollPage';
import { AttendancePage } from './pages/AttendancePage';
import { LearningPage } from './pages/LearningPage';
import { EngagementPage } from './pages/EngagementPage';
import { SafetyPage } from './pages/SafetyPage';
import { BusinessPerformancePage } from './pages/BusinessPerformancePage';

const PAGES: Record<PageKey, React.ComponentType> = {
  exec: ExecutiveOverview,
  recruitment: RecruitmentPage,
  turnover: TurnoverPage,
  payroll: PayrollPage,
  attendance: AttendancePage,
  learning: LearningPage,
  engagement: EngagementPage,
  safety: SafetyPage,
  business: BusinessPerformancePage,
};

function App() {
  const [page, setPage] = useState<PageKey>('exec');
  const Page = PAGES[page];

  return (
    <FiltersProvider>
      <div className="flex h-screen w-screen overflow-hidden bg-[var(--surface-2)]">
        <Sidebar page={page} onNavigate={setPage} />
        <div className="flex-1 flex flex-col min-w-0 overflow-hidden">
          <FilterBar />
          <main className="flex-1 overflow-y-auto scrollbar-thin px-5 py-5">
            <Page />
          </main>
        </div>
      </div>
    </FiltersProvider>
  );
}

export default App;
