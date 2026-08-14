import { Router } from 'express';
import { issueToken, verifyPassword } from '../auth.js';

export const authRouter = Router();

authRouter.post('/login', async (req, res) => {
  const { password } = req.body as { password?: string };
  if (!password || !(await verifyPassword(password))) {
    res.status(401).json({ error: 'Incorrect password' });
    return;
  }
  res.json({ token: issueToken() });
});
