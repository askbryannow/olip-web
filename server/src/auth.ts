import type { NextFunction, Request, Response } from 'express';
import jwt from 'jsonwebtoken';
import bcrypt from 'bcryptjs';

const JWT_SECRET = process.env.JWT_SECRET ?? 'dev-secret-change-me';
const ADMIN_PASSWORD = process.env.ADMIN_PASSWORD ?? 'changeme';
const TOKEN_TTL = '30d';

let cachedHash: string | null = null;
function adminPasswordHash(): string {
  if (!cachedHash) cachedHash = bcrypt.hashSync(ADMIN_PASSWORD, 8);
  return cachedHash;
}

export async function verifyPassword(password: string): Promise<boolean> {
  return bcrypt.compare(password, adminPasswordHash());
}

export function issueToken(): string {
  return jwt.sign({ role: 'admin' }, JWT_SECRET, { expiresIn: TOKEN_TTL });
}

export function requireAuth(req: Request, res: Response, next: NextFunction) {
  const header = req.headers.authorization;
  const token = header?.startsWith('Bearer ') ? header.slice(7) : null;
  if (!token) {
    res.status(401).json({ error: 'Unauthorized' });
    return;
  }
  try {
    jwt.verify(token, JWT_SECRET);
    next();
  } catch {
    res.status(401).json({ error: 'Invalid or expired token' });
  }
}
