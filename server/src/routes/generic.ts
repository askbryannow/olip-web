import { Router } from 'express';
import { db, nextId } from '../db.js';

export interface ColumnSpec {
  name: string;
  type: 'text' | 'real';
  required?: boolean;
}

/**
 * Builds a standard list/create/update/delete router for a flat SQLite table.
 * `idPrefix` seeds generated primary keys so they're recognizable in the DB.
 */
export function crudRouter(table: string, columns: ColumnSpec[], idPrefix: string): Router {
  const router = Router();
  const colNames = columns.map((c) => c.name);

  router.get('/', (req, res) => {
    const monthKey = typeof req.query.monthKey === 'string' ? req.query.monthKey : null;
    const rows = monthKey
      ? db.prepare(`SELECT * FROM ${table} WHERE monthKey = ? ORDER BY id`).all(monthKey)
      : db.prepare(`SELECT * FROM ${table} ORDER BY id`).all();
    res.json(rows);
  });

  router.post('/', (req, res) => {
    const body = req.body as Record<string, unknown>;
    for (const c of columns) {
      if (c.required && (body[c.name] === undefined || body[c.name] === null || body[c.name] === '')) {
        res.status(400).json({ error: `Missing required field: ${c.name}` });
        return;
      }
    }
    const id = typeof body.id === 'string' && body.id ? body.id : nextId(idPrefix);
    const cols = ['id', ...colNames];
    const placeholders = cols.map(() => '?').join(', ');
    const values = cols.map((c) => (c === 'id' ? id : (body[c] ?? null)));
    try {
      db.prepare(`INSERT INTO ${table} (${cols.join(', ')}) VALUES (${placeholders})`).run(...values);
      const row = db.prepare(`SELECT * FROM ${table} WHERE id = ?`).get(id);
      res.status(201).json(row);
    } catch (err) {
      res.status(400).json({ error: (err as Error).message });
    }
  });

  router.put('/:id', (req, res) => {
    const body = req.body as Record<string, unknown>;
    const existing = db.prepare(`SELECT * FROM ${table} WHERE id = ?`).get(req.params.id);
    if (!existing) {
      res.status(404).json({ error: 'Not found' });
      return;
    }
    const setClause = colNames.map((c) => `${c} = ?`).join(', ');
    const values = colNames.map((c) => (body[c] === undefined ? (existing as Record<string, unknown>)[c] : body[c]));
    try {
      db.prepare(`UPDATE ${table} SET ${setClause} WHERE id = ?`).run(...values, req.params.id);
      const row = db.prepare(`SELECT * FROM ${table} WHERE id = ?`).get(req.params.id);
      res.json(row);
    } catch (err) {
      res.status(400).json({ error: (err as Error).message });
    }
  });

  router.delete('/:id', (req, res) => {
    const result = db.prepare(`DELETE FROM ${table} WHERE id = ?`).run(req.params.id);
    if (result.changes === 0) {
      res.status(404).json({ error: 'Not found' });
      return;
    }
    res.status(204).end();
  });

  return router;
}
