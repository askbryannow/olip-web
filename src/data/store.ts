import type { Dataset, Employee } from './types';
import { buildMockDataset } from './generate';
import { buildCalendar } from './calendar';
import { api, ApiError, NetworkUnavailableError, getToken } from '../lib/api';

export type DataSource = 'live' | 'demo' | 'standalone';

interface RawEmployee extends Omit<Employee, 'joinDate' | 'exitDate'> {
  joinDate: string;
  exitDate: string | null;
}

function transformApiDataset(raw: Omit<Dataset, 'calendar'>): Dataset {
  const employees: Employee[] = (raw.employees as unknown as RawEmployee[]).map((e) => ({
    ...e,
    joinDate: new Date(e.joinDate),
    exitDate: e.exitDate ? new Date(e.exitDate) : null,
  }));
  return {
    ...raw,
    employees,
    calendar: buildCalendar(new Date(), 24),
  };
}

let dataset: Dataset | null = null;
let source: DataSource = 'standalone';

export function getDataset(): Dataset {
  if (!dataset) throw new Error('Dataset not loaded yet — call loadDataset() first');
  return dataset;
}

export function getDataSource(): DataSource {
  return source;
}

export type LoadResult = { status: 'ready'; source: DataSource } | { status: 'login-required' };

/** Loads the dashboard's dataset: live API data if reachable & authenticated, otherwise the seeded demo dataset. */
export async function loadDataset(): Promise<LoadResult> {
  if (import.meta.env.VITE_FORCE_DEMO === 'true') {
    // Static/offline preview build (e.g. a standalone HTML export) — never attempt a network call.
    dataset = buildMockDataset();
    source = 'standalone';
    return { status: 'ready', source };
  }

  if (!getToken()) {
    // No session yet — try an unauthenticated ping to tell "backend down" apart from "backend needs login".
    // A dev-server proxy error (e.g. 502) resolves as a normal response rather than throwing, so check res.ok too.
    try {
      const res = await fetch('/api/health');
      if (!res.ok) throw new Error('unreachable');
    } catch {
      dataset = buildMockDataset();
      source = 'standalone';
      return { status: 'ready', source };
    }
    return { status: 'login-required' };
  }

  try {
    const raw = await api.get<Omit<Dataset, 'calendar'>>('/dataset');
    if (raw.employees.length > 0) {
      dataset = transformApiDataset(raw);
      source = 'live';
    } else {
      dataset = buildMockDataset();
      source = 'demo';
    }
    return { status: 'ready', source };
  } catch (err) {
    if (err instanceof ApiError && err.status === 401) {
      return { status: 'login-required' };
    }
    if (err instanceof NetworkUnavailableError) {
      dataset = buildMockDataset();
      source = 'standalone';
      return { status: 'ready', source };
    }
    // Unexpected server error — still show something rather than a blank app.
    dataset = buildMockDataset();
    source = 'demo';
    return { status: 'ready', source };
  }
}

export async function refreshDataset(): Promise<LoadResult> {
  return loadDataset();
}
