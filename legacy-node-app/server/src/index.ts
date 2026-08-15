import 'dotenv/config';
import express from 'express';
import cors from 'cors';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { requireAuth } from './auth.js';
import { authRouter } from './routes/auth.js';
import { datasetRouter } from './routes/dataset.js';
import {
  outletsRouter,
  employeesRouter,
  overtimeRouter,
  attendanceRouter,
  recruitmentRouter,
  trainingRouter,
  engagementRouter,
  incidentsRouter,
  businessRouter,
} from './routes/entities.js';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const app = express();
const PORT = process.env.PORT ? Number(process.env.PORT) : 4000;

app.use(cors());
app.use(express.json());

app.get('/api/health', (_req, res) => res.json({ ok: true }));
app.use('/api/auth', authRouter);

// Everything below requires a valid session token — this dataset contains payroll & personal data.
app.use('/api', requireAuth);
app.use('/api/dataset', datasetRouter);
app.use('/api/outlets', outletsRouter);
app.use('/api/employees', employeesRouter);
app.use('/api/overtime', overtimeRouter);
app.use('/api/attendance', attendanceRouter);
app.use('/api/recruitment', recruitmentRouter);
app.use('/api/training', trainingRouter);
app.use('/api/engagement', engagementRouter);
app.use('/api/incidents', incidentsRouter);
app.use('/api/business', businessRouter);

// In production, serve the built frontend from the same process/port.
const distDir = path.join(__dirname, '..', '..', 'dist');
if (process.env.NODE_ENV === 'production') {
  app.use(express.static(distDir));
  app.get(/^(?!\/api).*/, (_req, res) => {
    res.sendFile(path.join(distDir, 'index.html'));
  });
}

app.listen(PORT, () => {
  console.log(`API server listening on http://localhost:${PORT}`);
});
