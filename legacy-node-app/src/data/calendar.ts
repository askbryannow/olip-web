import type { CalendarMonth } from './types';

export function buildCalendar(referenceDate: Date, windowMonths = 24): CalendarMonth[] {
  const months: CalendarMonth[] = [];
  for (let i = windowMonths - 1; i >= 0; i--) {
    const d = new Date(referenceDate.getFullYear(), referenceDate.getMonth() - i, 1);
    const key = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
    const label = d.toLocaleDateString('en-GB', { month: 'short', year: 'numeric' });
    months.push({
      key,
      label,
      year: d.getFullYear(),
      month: d.getMonth() + 1,
      date: d,
      isCurrent: i === 0,
    });
  }
  return months;
}
