<?php

namespace Database\Seeders;

use App\Models\BlipEmbassy;
use Illuminate\Database\Seeder;

/** The five Embassies of the BLIP Leadership Passport, per Volume 0 §5. */
class BlipEmbassySeeder extends Seeder
{
    public function run(): void
    {
        $embassies = [
            [
                'id' => 'the-top-penang',
                'sequence' => 1,
                'name' => 'The Top Penang',
                'stamp_name' => 'Customer Stamp',
                'theme' => 'Customer Experience',
                'description' => 'Walk the guest journey, own a live complaint, run a floor briefing.',
            ],
            [
                'id' => 'corporate-hq',
                'sequence' => 2,
                'name' => 'Corporate HQ',
                'stamp_name' => 'Strategic Stamp',
                'theme' => 'Strategy & Governance',
                'description' => 'Purchasing, Finance, Warehouse, Tapaulah and the C-Suite Decision Room.',
            ],
            [
                'id' => 'wet-world-pedas',
                'sequence' => 3,
                'name' => 'Wet World Pedas',
                'stamp_name' => 'Hospitality Stamp',
                'theme' => 'Hospitality',
                'description' => 'Standards audit, a safety walk, co-lead a peak trading day.',
            ],
            [
                'id' => 'jungle-gym',
                'sequence' => 4,
                'name' => 'Jungle Gym',
                'stamp_name' => 'Growth Stamp',
                'theme' => 'Growth',
                'description' => 'Size a real growth opportunity, build a costed case, coach a junior team member.',
            ],
            [
                'id' => 'genting-operations',
                'sequence' => 5,
                'name' => 'Genting Operations',
                'stamp_name' => 'Commercial Stamp',
                'theme' => 'Commercial',
                'description' => 'Read the outlet P&L, hunt cost without touching safety, review a live vendor.',
            ],
        ];

        foreach ($embassies as $embassy) {
            BlipEmbassy::query()->updateOrCreate(['id' => $embassy['id']], $embassy);
        }
    }
}
