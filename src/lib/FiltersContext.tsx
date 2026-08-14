import { createContext, useContext, useMemo, useState, type ReactNode } from 'react';
import type { Filters } from '../data/types';
import { DEFAULT_FILTERS } from './filters';

interface FiltersContextValue {
  filters: Filters;
  setFilters: (f: Filters) => void;
  patchFilters: (p: Partial<Filters>) => void;
  resetFilters: () => void;
}

const FiltersContext = createContext<FiltersContextValue | null>(null);

export function FiltersProvider({ children }: { children: ReactNode }) {
  const [filters, setFilters] = useState<Filters>(DEFAULT_FILTERS);

  const value = useMemo<FiltersContextValue>(
    () => ({
      filters,
      setFilters,
      patchFilters: (p) => setFilters((prev) => ({ ...prev, ...p })),
      resetFilters: () => setFilters(DEFAULT_FILTERS),
    }),
    [filters],
  );

  return <FiltersContext.Provider value={value}>{children}</FiltersContext.Provider>;
}

export function useFilters(): FiltersContextValue {
  const ctx = useContext(FiltersContext);
  if (!ctx) throw new Error('useFilters must be used within FiltersProvider');
  return ctx;
}
