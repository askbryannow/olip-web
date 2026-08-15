import { useEffect, useState } from 'react';
import { api } from '../lib/api';
import type { OutletRow } from './types';

let cache: OutletRow[] | null = null;
const listeners = new Set<(o: OutletRow[]) => void>();

export async function refetchOutlets(): Promise<OutletRow[]> {
  const rows = await api.get<OutletRow[]>('/outlets');
  cache = rows;
  listeners.forEach((l) => l(rows));
  return rows;
}

export function useOutlets(): OutletRow[] | null {
  const [outlets, setOutlets] = useState<OutletRow[] | null>(cache);

  useEffect(() => {
    listeners.add(setOutlets);
    if (!cache) void refetchOutlets().catch(() => setOutlets([]));
    return () => {
      listeners.delete(setOutlets);
    };
  }, []);

  return outlets;
}
