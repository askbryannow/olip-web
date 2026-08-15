<?php

namespace Database\Seeders;

use App\Models\Outlet;
use Illuminate\Database\Seeder;

class OutletSeeder extends Seeder
{
    /** The outlet/branch reference list — structural config, not operational data. */
    public function run(): void
    {
        $retail = ['Sales', 'Store Operations', 'Customer Service', 'Maintenance'];
        $fnb = ['F&B Service', 'Kitchen', 'Customer Service', 'Maintenance'];
        $warehouse = ['Warehouse', 'Logistics', 'Maintenance'];
        $corp = ['Human Resources', 'Finance', 'IT'];
        $mkt = ['Marketing'];

        $outlets = [
            ['id' => 'ret-mv', 'name' => 'Retail – Mid Valley', 'bu' => 'Retail Stores', 'state' => 'Selangor', 'departments' => $retail, 'opened_year' => 2016],
            ['id' => 'ret-sp', 'name' => 'Retail – Sunway Pyramid', 'bu' => 'Retail Stores', 'state' => 'Selangor', 'departments' => $retail, 'opened_year' => 2017],
            ['id' => 'ret-pv', 'name' => 'Retail – Pavilion KL', 'bu' => 'Retail Stores', 'state' => 'Kuala Lumpur', 'departments' => $retail, 'opened_year' => 2015],
            ['id' => 'ret-gp', 'name' => 'Retail – Gurney Plaza', 'bu' => 'Retail Stores', 'state' => 'Penang', 'departments' => $retail, 'opened_year' => 2018],
            ['id' => 'ret-jb', 'name' => 'Retail – JB City Square', 'bu' => 'Retail Stores', 'state' => 'Johor', 'departments' => $retail, 'opened_year' => 2019],
            ['id' => 'ret-ip', 'name' => 'Retail – Ipoh Parade', 'bu' => 'Retail Stores', 'state' => 'Perak', 'departments' => $retail, 'opened_year' => 2020],
            ['id' => 'ret-mk', 'name' => 'Retail – Melaka Mall', 'bu' => 'Retail Stores', 'state' => 'Melaka', 'departments' => $retail, 'opened_year' => 2021],
            ['id' => 'fnb-klcc', 'name' => 'F&B – KLCC', 'bu' => 'F&B Outlets', 'state' => 'Kuala Lumpur', 'departments' => $fnb, 'opened_year' => 2016],
            ['id' => 'fnb-1u', 'name' => 'F&B – 1 Utama', 'bu' => 'F&B Outlets', 'state' => 'Selangor', 'departments' => $fnb, 'opened_year' => 2017],
            ['id' => 'fnb-qb', 'name' => 'F&B – Queensbay', 'bu' => 'F&B Outlets', 'state' => 'Penang', 'departments' => $fnb, 'opened_year' => 2019],
            ['id' => 'fnb-kk', 'name' => 'F&B – Kota Kinabalu City', 'bu' => 'F&B Outlets', 'state' => 'Sabah', 'departments' => $fnb, 'opened_year' => 2020],
            ['id' => 'fnb-kch', 'name' => 'F&B – Kuching Central', 'bu' => 'F&B Outlets', 'state' => 'Sarawak', 'departments' => $fnb, 'opened_year' => 2021],
            ['id' => 'wh-sa', 'name' => 'Warehouse – Shah Alam', 'bu' => 'Warehouse & Logistics', 'state' => 'Selangor', 'departments' => $warehouse, 'opened_year' => 2015],
            ['id' => 'wh-jb', 'name' => 'Warehouse – Johor', 'bu' => 'Warehouse & Logistics', 'state' => 'Johor', 'departments' => $warehouse, 'opened_year' => 2019],
            ['id' => 'hq-corp', 'name' => 'HQ – Corporate (KL)', 'bu' => 'Corporate Support', 'state' => 'Kuala Lumpur', 'departments' => $corp, 'opened_year' => 2015],
            ['id' => 'hq-mkt', 'name' => 'HQ – Marketing (KL)', 'bu' => 'Marketing & Digital', 'state' => 'Kuala Lumpur', 'departments' => $mkt, 'opened_year' => 2015],
        ];

        foreach ($outlets as $outlet) {
            Outlet::updateOrCreate(['id' => $outlet['id']], $outlet);
        }

        $this->command?->info(count($outlets).' outlet(s) seeded.');
    }
}
