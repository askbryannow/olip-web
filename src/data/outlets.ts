import type { BusinessUnit, Department, Outlet } from './types';

const RETAIL_DEPTS: Department[] = ['Sales', 'Store Operations', 'Customer Service', 'Maintenance'];
const FNB_DEPTS: Department[] = ['F&B Service', 'Kitchen', 'Customer Service', 'Maintenance'];
const WAREHOUSE_DEPTS: Department[] = ['Warehouse', 'Logistics', 'Maintenance'];
const CORP_DEPTS: Department[] = ['Human Resources', 'Finance', 'IT'];
const MKT_DEPTS: Department[] = ['Marketing'];

export const OUTLETS: Outlet[] = [
  { id: 'ret-mv', name: 'Retail – Mid Valley', bu: 'Retail Stores', state: 'Selangor', departments: RETAIL_DEPTS, openedYear: 2016 },
  { id: 'ret-sp', name: 'Retail – Sunway Pyramid', bu: 'Retail Stores', state: 'Selangor', departments: RETAIL_DEPTS, openedYear: 2017 },
  { id: 'ret-pv', name: 'Retail – Pavilion KL', bu: 'Retail Stores', state: 'Kuala Lumpur', departments: RETAIL_DEPTS, openedYear: 2015 },
  { id: 'ret-gp', name: 'Retail – Gurney Plaza', bu: 'Retail Stores', state: 'Penang', departments: RETAIL_DEPTS, openedYear: 2018 },
  { id: 'ret-jb', name: 'Retail – JB City Square', bu: 'Retail Stores', state: 'Johor', departments: RETAIL_DEPTS, openedYear: 2019 },
  { id: 'ret-ip', name: 'Retail – Ipoh Parade', bu: 'Retail Stores', state: 'Perak', departments: RETAIL_DEPTS, openedYear: 2020 },
  { id: 'ret-mk', name: 'Retail – Melaka Mall', bu: 'Retail Stores', state: 'Melaka', departments: RETAIL_DEPTS, openedYear: 2021 },
  { id: 'fnb-klcc', name: 'F&B – KLCC', bu: 'F&B Outlets', state: 'Kuala Lumpur', departments: FNB_DEPTS, openedYear: 2016 },
  { id: 'fnb-1u', name: 'F&B – 1 Utama', bu: 'F&B Outlets', state: 'Selangor', departments: FNB_DEPTS, openedYear: 2017 },
  { id: 'fnb-qb', name: 'F&B – Queensbay', bu: 'F&B Outlets', state: 'Penang', departments: FNB_DEPTS, openedYear: 2019 },
  { id: 'fnb-kk', name: 'F&B – Kota Kinabalu City', bu: 'F&B Outlets', state: 'Sabah', departments: FNB_DEPTS, openedYear: 2020 },
  { id: 'fnb-kch', name: 'F&B – Kuching Central', bu: 'F&B Outlets', state: 'Sarawak', departments: FNB_DEPTS, openedYear: 2021 },
  { id: 'wh-sa', name: 'Warehouse – Shah Alam', bu: 'Warehouse & Logistics', state: 'Selangor', departments: WAREHOUSE_DEPTS, openedYear: 2015 },
  { id: 'wh-jb', name: 'Warehouse – Johor', bu: 'Warehouse & Logistics', state: 'Johor', departments: WAREHOUSE_DEPTS, openedYear: 2019 },
  { id: 'hq-corp', name: 'HQ – Corporate (KL)', bu: 'Corporate Support', state: 'Kuala Lumpur', departments: CORP_DEPTS, openedYear: 2015 },
  { id: 'hq-mkt', name: 'HQ – Marketing (KL)', bu: 'Marketing & Digital', state: 'Kuala Lumpur', departments: MKT_DEPTS, openedYear: 2015 },
];

export const BUSINESS_UNITS: BusinessUnit[] = [
  'Retail Stores',
  'F&B Outlets',
  'Warehouse & Logistics',
  'Corporate Support',
  'Marketing & Digital',
];

export const STATES = ['Selangor', 'Kuala Lumpur', 'Penang', 'Johor', 'Perak', 'Sabah', 'Sarawak', 'Melaka'] as const;

export const EMPLOYMENT_TYPES = ['Permanent', 'Contract', 'Part-Time', 'Intern'] as const;
