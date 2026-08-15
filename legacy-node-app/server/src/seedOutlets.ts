// One-time seed of the outlet reference list (structural config, not operational data).
// Run with: npm run seed --workspace=server (or `npm run seed` from /server)
import { db } from './db.js';

const RETAIL = ['Sales', 'Store Operations', 'Customer Service', 'Maintenance'];
const FNB = ['F&B Service', 'Kitchen', 'Customer Service', 'Maintenance'];
const WAREHOUSE = ['Warehouse', 'Logistics', 'Maintenance'];
const CORP = ['Human Resources', 'Finance', 'IT'];
const MKT = ['Marketing'];

const OUTLETS = [
  { id: 'ret-mv', name: 'Retail – Mid Valley', bu: 'Retail Stores', state: 'Selangor', departments: RETAIL, openedYear: 2016 },
  { id: 'ret-sp', name: 'Retail – Sunway Pyramid', bu: 'Retail Stores', state: 'Selangor', departments: RETAIL, openedYear: 2017 },
  { id: 'ret-pv', name: 'Retail – Pavilion KL', bu: 'Retail Stores', state: 'Kuala Lumpur', departments: RETAIL, openedYear: 2015 },
  { id: 'ret-gp', name: 'Retail – Gurney Plaza', bu: 'Retail Stores', state: 'Penang', departments: RETAIL, openedYear: 2018 },
  { id: 'ret-jb', name: 'Retail – JB City Square', bu: 'Retail Stores', state: 'Johor', departments: RETAIL, openedYear: 2019 },
  { id: 'ret-ip', name: 'Retail – Ipoh Parade', bu: 'Retail Stores', state: 'Perak', departments: RETAIL, openedYear: 2020 },
  { id: 'ret-mk', name: 'Retail – Melaka Mall', bu: 'Retail Stores', state: 'Melaka', departments: RETAIL, openedYear: 2021 },
  { id: 'fnb-klcc', name: 'F&B – KLCC', bu: 'F&B Outlets', state: 'Kuala Lumpur', departments: FNB, openedYear: 2016 },
  { id: 'fnb-1u', name: 'F&B – 1 Utama', bu: 'F&B Outlets', state: 'Selangor', departments: FNB, openedYear: 2017 },
  { id: 'fnb-qb', name: 'F&B – Queensbay', bu: 'F&B Outlets', state: 'Penang', departments: FNB, openedYear: 2019 },
  { id: 'fnb-kk', name: 'F&B – Kota Kinabalu City', bu: 'F&B Outlets', state: 'Sabah', departments: FNB, openedYear: 2020 },
  { id: 'fnb-kch', name: 'F&B – Kuching Central', bu: 'F&B Outlets', state: 'Sarawak', departments: FNB, openedYear: 2021 },
  { id: 'wh-sa', name: 'Warehouse – Shah Alam', bu: 'Warehouse & Logistics', state: 'Selangor', departments: WAREHOUSE, openedYear: 2015 },
  { id: 'wh-jb', name: 'Warehouse – Johor', bu: 'Warehouse & Logistics', state: 'Johor', departments: WAREHOUSE, openedYear: 2019 },
  { id: 'hq-corp', name: 'HQ – Corporate (KL)', bu: 'Corporate Support', state: 'Kuala Lumpur', departments: CORP, openedYear: 2015 },
  { id: 'hq-mkt', name: 'HQ – Marketing (KL)', bu: 'Marketing & Digital', state: 'Kuala Lumpur', departments: MKT, openedYear: 2015 },
];

const insert = db.prepare(
  'INSERT OR IGNORE INTO outlets (id, name, bu, state, departments, openedYear) VALUES (?, ?, ?, ?, ?, ?)',
);
let inserted = 0;
for (const o of OUTLETS) {
  const result = insert.run(o.id, o.name, o.bu, o.state, JSON.stringify(o.departments), o.openedYear);
  if (result.changes > 0) inserted++;
}
console.log(`Seeded ${inserted} outlet(s) (${OUTLETS.length - inserted} already existed).`);
