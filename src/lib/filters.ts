import type { CalendarMonth, Dataset, Employee, Filters } from '../data/types';

export const DEFAULT_FILTERS: Filters = {
  monthKey: 'ALL',
  bu: 'ALL',
  state: 'ALL',
  outletId: 'ALL',
  employmentType: 'ALL',
};

export function endMonth(filters: Filters, calendar: CalendarMonth[]): CalendarMonth {
  if (filters.monthKey !== 'ALL') {
    const m = calendar.find((c) => c.key === filters.monthKey);
    if (m) return m;
  }
  return calendar[calendar.length - 1];
}

export function trailingMonths(filters: Filters, calendar: CalendarMonth[], n = 12): CalendarMonth[] {
  const end = endMonth(filters, calendar);
  const endIdx = calendar.findIndex((c) => c.key === end.key);
  const startIdx = Math.max(0, endIdx - n + 1);
  return calendar.slice(startIdx, endIdx + 1);
}

export function matchesDims(
  row: { bu: string; state: string; outletId?: string },
  filters: Filters,
): boolean {
  if (filters.bu !== 'ALL' && row.bu !== filters.bu) return false;
  if (filters.state !== 'ALL' && row.state !== filters.state) return false;
  if (filters.outletId !== 'ALL' && row.outletId !== undefined && row.outletId !== filters.outletId) return false;
  return true;
}

export function matchesEmployee(e: Employee, filters: Filters, atDate: Date): boolean {
  if (!matchesDims(e, filters)) return false;
  if (filters.employmentType !== 'ALL' && e.employmentType !== filters.employmentType) return false;
  return e.joinDate <= atDate && (e.exitDate === null || e.exitDate > atDate);
}

export function activeEmployeesAt(dataset: Dataset, filters: Filters, atDate: Date): Employee[] {
  return dataset.employees.filter((e) => matchesEmployee(e, filters, atDate));
}

export function monthEndDate(cm: CalendarMonth): Date {
  return new Date(cm.year, cm.month, 0, 23, 59, 59);
}
export function monthStartDate(cm: CalendarMonth): Date {
  return new Date(cm.year, cm.month - 1, 1);
}

export function fmtMYR(n: number, compact = true): string {
  if (compact && Math.abs(n) >= 1_000_000) return `RM ${(n / 1_000_000).toFixed(2)}M`;
  if (compact && Math.abs(n) >= 1_000) return `RM ${(n / 1_000).toFixed(1)}K`;
  return `RM ${Math.round(n).toLocaleString('en-MY')}`;
}
export function fmtNum(n: number): string {
  return Math.round(n).toLocaleString('en-MY');
}
export function fmtPct(n: number, digits = 1): string {
  return `${n.toFixed(digits)}%`;
}
