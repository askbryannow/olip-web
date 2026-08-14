// Palette accessors mirroring the CSS custom properties in index.css (see dataviz skill palette.md).
export const CATEGORICAL = [
  'var(--series-1)',
  'var(--series-2)',
  'var(--series-3)',
  'var(--series-4)',
  'var(--series-5)',
  'var(--series-6)',
  'var(--series-7)',
  'var(--series-8)',
] as const;

export const STATUS = {
  good: 'var(--status-good)',
  warning: 'var(--status-warning)',
  serious: 'var(--status-serious)',
  critical: 'var(--status-critical)',
} as const;

export const SEQUENTIAL_BLUE = [
  'var(--seq-100)',
  'var(--seq-200)',
  'var(--seq-300)',
  'var(--seq-400)',
  'var(--seq-500)',
  'var(--seq-600)',
  'var(--seq-700)',
] as const;

export const INK = {
  primary: 'var(--text-primary)',
  secondary: 'var(--text-secondary)',
  muted: 'var(--text-muted)',
  grid: 'var(--gridline)',
  baseline: 'var(--baseline)',
  success: 'var(--success-text)',
};

export function seqStep(value: number, min: number, max: number): string {
  const steps = ['#cde2fb', '#b7d3f6', '#9ec5f4', '#86b6ef', '#6da7ec', '#5598e7', '#3987e5', '#2a78d6', '#256abf', '#1c5cab', '#184f95', '#104281', '#0d366b'];
  if (max === min) return steps[Math.floor(steps.length / 2)];
  const t = Math.max(0, Math.min(1, (value - min) / (max - min)));
  return steps[Math.round(t * (steps.length - 1))];
}
