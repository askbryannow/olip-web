# People Analytics — HR & Workforce Dashboard

A multi-page HR/people analytics dashboard built on **Laravel + Livewire**, backed by a SQLite database. Enter your real numbers each month through the built-in data-entry forms, and every dashboard page recalculates from them server-side — no spreadsheet wrangling, no separate JS frontend build, and no manual chart rebuilding required.

A one-time seeder loads the 16-outlet reference list plus a sample demo dataset, so the dashboard is never blank while you're getting real data in.

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

Global filters (month, business unit, state, outlet, employment type) apply across every page and persist across navigation.

## Data Entry

A **Data Entry** section in the sidebar lets you maintain the real dataset:

- **Employees** — the employee master. Add new hires here; record a resignation/termination by editing the employee and setting an exit date & type.
- **Outlets** — the branch/outlet reference list (usually set up once).
- **Overtime, Attendance & Leave, Recruitment, Training & HRDC, Engagement Surveys, Safety Incidents, Business Performance** — one form per table, scoped by month + outlet + department, matching the underlying data model.
- **Monthly Checklist** — shows which outlet/table combinations are still missing data for the current month, so you know exactly what to fill in before regenerating the dashboard.

The whole app sits behind a single admin password (see setup below) since it holds payroll and personal data.

## Tech stack

- **Laravel 13** (PHP 8.4+) for routing, the ORM (SQLite), and the data model.
- **Livewire** for the interactive dashboard and forms — server-rendered, no API layer or separate frontend build.
- **Chart.js** (via Alpine.js, bundled with Livewire) for charts, themed from the same design tokens as the rest of the UI.
- **Tailwind CSS v4** for styling, with a light/dark design-token system.

## Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set `ADMIN_PASSWORD` to whatever password you want to gate the app with (defaults to `changeme` — change this before any real use).

```bash
php artisan migrate --seed   # creates tables, seeds the 16 outlets + sample demo data
npm run build                # compile CSS/JS (or `npm run dev` while developing)
php artisan serve            # http://127.0.0.1:8000
```

Log in with the `ADMIN_PASSWORD` you set above.

### Resetting the database

```bash
php artisan migrate:fresh --seed
```

This drops all tables, recreates them, and re-seeds the outlet list + sample data. Use it any time you want to start over during setup — it will wipe any real data you've entered, so avoid it once you're using the app for real.

## Previous implementation

An earlier version of this app was built with React + TypeScript + Recharts on the frontend and an Express + SQLite API on the backend. That implementation is preserved for reference under [`legacy-node-app/`](./legacy-node-app) but is no longer developed — the Laravel/Livewire app above is the current, actively maintained version.
