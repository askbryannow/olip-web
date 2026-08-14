import { useState } from 'react';
import { FiltersProvider } from './lib/FiltersContext';
import { AppDataProvider, useAppData } from './lib/AppDataContext';
import { Sidebar, type PageKey } from './components/Sidebar';
import { FilterBar } from './components/FilterBar';
import { DataSourceBanner } from './components/DataSourceBanner';
import { LoginPage } from './components/LoginPage';
import { ExecutiveOverview } from './pages/ExecutiveOverview';
import { RecruitmentPage } from './pages/RecruitmentPage';
import { TurnoverPage } from './pages/TurnoverPage';
import { PayrollPage } from './pages/PayrollPage';
import { AttendancePage } from './pages/AttendancePage';
import { LearningPage } from './pages/LearningPage';
import { EngagementPage } from './pages/EngagementPage';
import { SafetyPage } from './pages/SafetyPage';
import { BusinessPerformancePage } from './pages/BusinessPerformancePage';
import { DataEntryRouter, type DataEntryPageKey } from './admin/DataEntryRouter';

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

function DashboardShell() {
  const [section, setSection] = useState<'dashboard' | 'data-entry'>('dashboard');
  const [page, setPage] = useState<PageKey>('exec');
  const [entryPage, setEntryPage] = useState<DataEntryPageKey>('checklist');
  const Page = PAGES[page];

  return (
    <div className="flex h-screen w-screen overflow-hidden bg-[var(--surface-2)]">
      <Sidebar
        section={section}
        page={page}
        entryPage={entryPage}
        onNavigate={(p) => {
          setSection('dashboard');
          setPage(p);
        }}
        onNavigateEntry={(p) => {
          setSection('data-entry');
          setEntryPage(p);
        }}
      />
      <div className="flex-1 flex flex-col min-w-0 overflow-hidden">
        <DataSourceBanner />
        {section === 'dashboard' ? (
          <>
            <FilterBar />
            <main className="flex-1 overflow-y-auto scrollbar-thin px-5 py-5">
              <Page />
            </main>
          </>
        ) : (
          <main className="flex-1 overflow-y-auto scrollbar-thin px-5 py-5">
            <DataEntryRouter page={entryPage} onNavigate={setEntryPage} />
          </main>
        )}
      </div>
    </div>
  );
}

function Gate() {
  const { status } = useAppData();

  if (status === 'loading') {
    return (
      <div className="flex h-screen w-screen items-center justify-center bg-[var(--surface-2)] text-[13px] text-[var(--text-muted)]">
        Loading…
      </div>
    );
  }
  if (status === 'login-required') {
    return <LoginPage />;
  }
  return (
    <FiltersProvider>
      <DashboardShell />
    </FiltersProvider>
  );
}

function App() {
  return (
    <AppDataProvider>
      <Gate />
    </AppDataProvider>
  );
}

export default App;
