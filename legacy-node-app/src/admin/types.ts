export interface OutletRow {
  id: string;
  name: string;
  bu: string;
  state: string;
  departments: string; // JSON-encoded array, as stored
  openedYear: number;
}

export function parseDepartments(o: OutletRow): string[] {
  try {
    return JSON.parse(o.departments) as string[];
  } catch {
    return [];
  }
}

export type FieldKind = 'text' | 'number' | 'month' | 'date' | 'select' | 'outlet' | 'department' | 'derived';

export interface FieldConfig {
  key: string;
  label: string;
  kind: FieldKind;
  options?: string[]; // for kind 'select'
  required?: boolean;
  step?: string; // for number inputs, e.g. "0.1"
}

export interface EntityConfig {
  title: string;
  description: string;
  endpoint: string;
  fields: FieldConfig[]; // used to render the form
  columns: string[]; // subset of field keys (plus 'id') shown in the table
  defaultValues?: () => Record<string, unknown>;
  /** Reshape a saved row before it's fed into the edit form (e.g. JSON array -> comma string). */
  transformForEdit?: (row: Record<string, unknown>) => Record<string, unknown>;
  /** Reshape form values before they're sent to the API (inverse of transformForEdit). */
  transformBeforeSave?: (values: Record<string, unknown>) => Record<string, unknown>;
}

export function currentMonthKey(): string {
  const d = new Date();
  return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
}
