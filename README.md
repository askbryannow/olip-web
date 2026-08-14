# People Analytics — HR & Workforce Dashboard

A multi-page HR/people analytics dashboard with a real backend: a React/TypeScript/Tailwind/Recharts frontend backed by an Express + SQLite API. Enter your real numbers each month through the built-in data-entry forms, and the dashboard recalculates from them — no spreadsheet wrangling or manual chart rebuilding required.

If the API server isn't running or has no records yet, the dashboard falls back to a seeded demo dataset so it's never blank — a banner at the top always tells you which mode you're in.

## Pages

- **Executive Overview** — KPI tiles (headcount, turnover, payroll cost & % of revenue, overtime, time to hire, revenue/employee, safety incident rate), headcount by business unit, hires/resignations/net growth trend, employment type & gender mix.
- **People & Business Performance** — revenue, visitors, headcount, and labour cost indexed on one scale.
- **Recruitment** — applicant funnel, aging vacancies, time to hire, cost per hire.
- **Turnover** — monthly trend, voluntary vs involuntary, early attrition, turnover-rate heatmap by department.
- **Payroll & Overtime** — payroll cost trend, overtime actual vs budget by department, payroll % of revenue by outlet.
- **Attendance & Leave** — absenteeism trend, leave by type, late clock-ins.
- **Learning & Development** — training hours per employee, mandatory compliance, HRD Corp claims.
- **Engagement** — survey scores over time, eNPS, grievances, recognition.
- **Safety** — LTIFR trend, incidents by location, compliance score by outlet.

Global filters (month, business unit, state, outlet, employment type) apply across every page.

## Data Entry

A **Data Entry** section in the sidebar lets you maintain the real dataset:

- **Employees** — the employee master. Add new hires here; record a resignation/termination by editing the employee and setting an exit date & type.
- **Outlets** — the branch/outlet reference list (usually set up once).
- **Overtime, Attendance & Leave, Recruitment, Training & HRDC, Engagement Surveys, Safety Incidents, Business Performance** — one form per table, scoped by month + outlet + department, matching the underlying data model.
- **Monthly Checklist** — shows which outlet/table combinations are still missing data for the current month, so you know exactly what to fill in before regenerating the dashboard.

The whole app sits behind a single admin password (see setup below) since it holds payroll and personal data.

## Setup

```bash
npm install                 # frontend deps
npm install --prefix server # backend deps
cp server/.env.example server/.env   # set ADMIN_PASSWORD and JWT_SECRET
npm run seed                # one-time: seed the 16-outlet reference list
npm run dev:all             # runs the Vite dev server (5173) + API server (4000) together
```

Open http://localhost:5173 and sign in with the password from `server/.env`.

Run them separately instead with `npm run dev` (frontend) and `npm run dev:server` (backend) if you prefer two terminals.

## Monthly workflow

1. Open **Data Entry → Monthly Checklist** and see what's outstanding for the current month.
2. Fill in each table (overtime, attendance, recruitment, training, engagement, incidents, business performance) for every outlet. Record any new hires/exits on the **Employees** page as they happen — they don't need a separate monthly entry.
3. Click "Refresh dashboard data" (or reload the app) — the Executive Overview and every drill-through page now reflect the new month.

## Data model

`src/data/types.ts` defines the shape: employee master, overtime, attendance, recruitment, training, engagement, incidents, business performance, plus a calendar table computed from the real current date. `server/src/db.ts` mirrors this as SQLite tables. `src/lib/selectors.ts` aggregates the data per page, respecting the active filters, against whichever dataset is loaded (live API data, or the seeded demo data as fallback — see `src/data/store.ts`).

## Deployment

This now needs a running Node process, not just static hosting:

```bash
npm run build                 # builds the frontend into dist/
npm run build --prefix server # compiles the API server
NODE_ENV=production ADMIN_PASSWORD=... JWT_SECRET=... PORT=4000 node server/dist/index.js
```

In production the API server also serves the built frontend from `dist/`, so a single process on one port is enough. Point `DATABASE_PATH` at a persistent volume if your host's filesystem isn't durable across deploys.
