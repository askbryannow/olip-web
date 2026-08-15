<?php

namespace Database\Seeders;

use App\Models\AttendanceRecord;
use App\Models\BusinessPerformanceRecord;
use App\Models\Employee;
use App\Models\EngagementRecord;
use App\Models\IncidentRecord;
use App\Models\Outlet;
use App\Models\OvertimeRecord;
use App\Models\RecruitmentRecord;
use App\Models\TrainingRecord;
use App\Support\Calendar;
use App\Support\Options;
use Illuminate\Database\Seeder;

/**
 * A modest, varied sample dataset — not a full historical simulation — so a fresh
 * install has something on every chart. Seeds 5 outlets (one per BU) across the
 * trailing 6 months. Safe to run again; skips outlets that already have employees.
 */
class SampleDataSeeder extends Seeder
{
    private array $firstNamesM = ['Ahmad', 'Muhammad', 'Wei Jian', 'Kai Xuan', 'Arjun', 'Kumar', 'Farid', 'Kok Wei'];
    private array $firstNamesF = ['Nur Aisyah', 'Siti Fatimah', 'Wei Ling', 'Mei Yee', 'Priya', 'Kavitha', 'Farah', 'Su Ann'];
    private array $lastNames = ['bin Abdullah', 'binti Ahmad', 'Tan', 'Lim', 'Wong', 'Lee', 'a/l Muthu', 'a/p Krishnan'];

    public function run(): void
    {
        mt_srand(42);

        $seedOutletIds = ['ret-mv', 'fnb-klcc', 'wh-sa', 'hq-corp', 'hq-mkt'];
        $outlets = Outlet::whereIn('id', $seedOutletIds)->get()->keyBy('id');
        $months = Calendar::trailing(windowMonths: 12);

        foreach ($seedOutletIds as $outletId) {
            $outlet = $outlets->get($outletId);
            if (! $outlet || Employee::where('outlet_id', $outletId)->exists()) {
                continue;
            }

            $employees = $this->seedEmployees($outlet, $months);
            $this->seedOvertimeAndAttendance($outlet, $months, $employees);
            $this->seedRecruitment($outlet, $months);
            $this->seedTraining($outlet, $months, $employees);
            $this->seedIncidents($outlet, $months, count($employees));
            if (in_array($outlet->bu, Options::OPERATING_BUS, true)) {
                $this->seedBusiness($outlet, $months, count($employees));
            }
            $this->seedEngagement($outlet, count($employees));
        }

        $this->command?->info('Sample data seeded for: '.implode(', ', $seedOutletIds));
    }

    private function weightedPick(array $weights): string
    {
        $total = array_sum($weights);
        $r = mt_rand(1, $total);
        foreach ($weights as $key => $weight) {
            if ($r <= $weight) {
                return $key;
            }
            $r -= $weight;
        }

        return array_key_first($weights);
    }

    private function name(string $gender): string
    {
        $first = $gender === 'Male' ? $this->firstNamesM[array_rand($this->firstNamesM)] : $this->firstNamesF[array_rand($this->firstNamesF)];

        return $first.' '.$this->lastNames[array_rand($this->lastNames)];
    }

    private function seedEmployees(Outlet $outlet, array $months): array
    {
        $employees = [];
        $windowStart = $months[0]->startOfMonth();
        $frontline = ['Sales', 'Store Operations', 'F&B Service', 'Kitchen', 'Warehouse', 'Logistics', 'Customer Service', 'Maintenance'];
        $levels = Options::LEVELS;
        $levelSalary = [
            'Executive' => [2600, 4000], 'Senior Executive' => [4000, 5800], 'Manager' => [6200, 9200],
            'Senior Manager' => [9800, 14000], 'Head' => [15000, 21000],
        ];

        foreach ($outlet->departments as $department) {
            $count = mt_rand(4, 9);
            for ($i = 0; $i < $count; $i++) {
                $gender = in_array($department, ['Kitchen', 'Warehouse', 'Logistics', 'Maintenance'], true)
                    ? (mt_rand(1, 100) <= 74 ? 'Male' : 'Female')
                    : (mt_rand(1, 100) <= 50 ? 'Male' : 'Female');

                $employmentType = in_array($department, $frontline, true)
                    ? $this->weightedPick(['Permanent' => 55, 'Contract' => 15, 'Part-Time' => 25, 'Intern' => 5])
                    : 'Permanent';

                $level = mt_rand(1, 100) <= 42 ? 'Executive' : ($levels[mt_rand(1, 3)]);
                [$lo, $hi] = $levelSalary[$level];
                $joinDate = $windowStart->copy()->subDays(mt_rand(60, 900));

                $exitDate = null;
                $exitType = null;
                if (mt_rand(1, 100) <= 12) {
                    $exitDate = now()->subDays(mt_rand(1, 150));
                    $exitType = mt_rand(1, 100) <= 70 ? 'Voluntary' : 'Involuntary';
                }

                $employees[] = Employee::create([
                    'name' => $this->name($gender),
                    'gender' => $gender,
                    'bu' => $outlet->bu,
                    'department' => $department,
                    'outlet_id' => $outlet->id,
                    'state' => $outlet->state,
                    'employment_type' => $employmentType,
                    'level' => $level,
                    'join_date' => $joinDate,
                    'exit_date' => $exitDate,
                    'exit_type' => $exitType,
                    'base_salary' => round(mt_rand($lo * 100, $hi * 100) / 100, 2),
                ]);
            }
        }

        return $employees;
    }

    private function seedOvertimeAndAttendance(Outlet $outlet, array $months, array $employees): void
    {
        $frontline = ['Sales', 'Store Operations', 'F&B Service', 'Kitchen', 'Warehouse', 'Logistics', 'Customer Service', 'Maintenance'];

        foreach ($outlet->departments as $department) {
            $deptHeadcount = count(array_filter($employees, fn ($e) => $e->department === $department && ! $e->exit_date));
            if ($deptHeadcount === 0) {
                continue;
            }

            foreach ($months as $m) {
                if (in_array($department, $frontline, true)) {
                    $hours = round($deptHeadcount * mt_rand(400, 1400) / 100, 2);
                    OvertimeRecord::updateOrCreate(
                        ['month_key' => $m->key, 'outlet_id' => $outlet->id, 'department' => $department],
                        [
                            'bu' => $outlet->bu, 'state' => $outlet->state,
                            'overtime_hours' => $hours,
                            'overtime_pay' => round($hours * mt_rand(1200, 1800) / 100, 2),
                            'budget_hours' => round($hours * mt_rand(80, 125) / 100, 2),
                        ],
                    );
                }

                AttendanceRecord::updateOrCreate(
                    ['month_key' => $m->key, 'outlet_id' => $outlet->id, 'department' => $department],
                    [
                        'bu' => $outlet->bu, 'state' => $outlet->state,
                        'working_days' => 22,
                        'absent_days' => round($deptHeadcount * 22 * mt_rand(150, 500) / 10000, 2),
                        'late_clock_ins' => round($deptHeadcount * mt_rand(30, 180) / 100, 2),
                        'headcount' => $deptHeadcount,
                        'leave_annual' => round($deptHeadcount * mt_rand(60, 110) / 100, 2),
                        'leave_medical' => round($deptHeadcount * mt_rand(30, 70) / 100, 2),
                        'leave_emergency' => round($deptHeadcount * mt_rand(5, 20) / 100, 2),
                        'leave_unpaid' => round($deptHeadcount * mt_rand(0, 15) / 100, 2),
                        'leave_maternity_paternity' => mt_rand(1, 100) <= 15 ? mt_rand(1, 3) : 0,
                    ],
                );
            }
        }
    }

    private function seedRecruitment(Outlet $outlet, array $months): void
    {
        foreach ($outlet->departments as $department) {
            foreach ($months as $m) {
                if (mt_rand(1, 100) > 55) {
                    continue;
                }
                $joined = mt_rand(0, 2);
                $offered = $joined + mt_rand(0, 2);
                $interviewed = $offered + mt_rand(2, 6);
                $screened = $interviewed + mt_rand(3, 10);
                $applied = $screened + mt_rand(5, 25);

                RecruitmentRecord::updateOrCreate(
                    ['month_key' => $m->key, 'outlet_id' => $outlet->id, 'department' => $department],
                    [
                        'bu' => $outlet->bu, 'state' => $outlet->state,
                        'applied' => $applied, 'screened' => $screened, 'interviewed' => $interviewed,
                        'offered' => $offered, 'joined' => $joined,
                        'open_vacancies' => mt_rand(0, 4),
                        'avg_vacancy_age_days' => mt_rand(8, 55),
                        'avg_time_to_hire_days' => mt_rand(14, 45),
                        'cost_per_hire' => mt_rand(600, 2400),
                    ],
                );
            }
        }
    }

    private function seedTraining(Outlet $outlet, array $months, array $employees): void
    {
        foreach ($outlet->departments as $department) {
            foreach (Options::EMPLOYMENT_TYPES as $type) {
                $headcount = count(array_filter($employees, fn ($e) => $e->department === $department && $e->employment_type === $type && ! $e->exit_date));
                if ($headcount === 0) {
                    continue;
                }
                foreach ($months as $m) {
                    $hours = round($headcount * mt_rand(80, 450) / 100, 2);
                    TrainingRecord::updateOrCreate(
                        ['month_key' => $m->key, 'outlet_id' => $outlet->id, 'department' => $department, 'employment_type' => $type],
                        [
                            'bu' => $outlet->bu, 'state' => $outlet->state,
                            'headcount' => $headcount, 'training_hours' => $hours,
                            'mandatory_due' => $headcount,
                            'mandatory_completed' => round($headcount * mt_rand(70, 100) / 100),
                            'hrdc_claim_eligible' => round($hours * mt_rand(35, 55), 2),
                            'hrdc_claim_submitted' => round($hours * mt_rand(35, 55) * mt_rand(55, 95) / 100, 2),
                        ],
                    );
                }
            }
        }
    }

    private function seedIncidents(Outlet $outlet, array $months, int $headcount): void
    {
        if ($headcount === 0) {
            return;
        }
        $riskFactor = match ($outlet->bu) {
            'Warehouse & Logistics' => 1.6, 'F&B Outlets' => 1.3, 'Retail Stores' => 1.0, default => 0.4,
        };

        foreach ($months as $m) {
            $hoursWorked = $headcount * 22 * 8;
            foreach (Options::SEVERITIES as $severity) {
                $baseRate = match ($severity) {
                    'Near Miss' => 0.06, 'First Aid' => 0.03, 'Medical Treatment' => 0.012, default => 0.004,
                };
                if ((mt_rand(1, 1000) / 1000) >= $baseRate * $riskFactor * $headcount) {
                    continue;
                }
                $count = mt_rand(1, $severity === 'Near Miss' ? 3 : 1);
                IncidentRecord::updateOrCreate(
                    ['month_key' => $m->key, 'outlet_id' => $outlet->id, 'severity' => $severity],
                    [
                        'bu' => $outlet->bu, 'state' => $outlet->state,
                        'count' => $count, 'hours_worked' => $hoursWorked,
                        'days_lost' => $severity === 'Lost Time Injury' ? $count * mt_rand(2, 12) : 0,
                        'compliance_audit_score' => mt_rand(78, 99),
                    ],
                );
            }
        }
    }

    private function seedBusiness(Outlet $outlet, array $months, int $headcount): void
    {
        $revBase = mt_rand(180000, 420000);
        foreach ($months as $m) {
            $seasonal = [1.15, 0.95, 0.98, 1.0, 1.0, 1.05, 1.0, 1.0, 1.0, 1.05, 1.1, 1.3][$m->month - 1];
            $revBase = round($revBase * (1 + mt_rand(-200, 240) / 10000));
            $revenue = round($revBase * $seasonal * (0.9 + $headcount / 60));
            BusinessPerformanceRecord::updateOrCreate(
                ['month_key' => $m->key, 'outlet_id' => $outlet->id],
                [
                    'bu' => $outlet->bu, 'state' => $outlet->state,
                    'revenue' => $revenue,
                    'visitors' => round($revenue / mt_rand(45, 85)),
                ],
            );
        }
    }

    private function seedEngagement(Outlet $outlet, int $headcount): void
    {
        if ($headcount === 0) {
            return;
        }
        $quarter = Calendar::trailing(windowMonths: 1)[0];
        $quarterNum = (int) ceil($quarter->month / 3);
        $quarterKey = $quarter->year.'-Q'.$quarterNum;
        $score = mt_rand(60, 78);

        EngagementRecord::updateOrCreate(
            ['quarter_key' => $quarterKey, 'outlet_id' => $outlet->id],
            [
                'month_key' => $quarter->key, 'bu' => $outlet->bu, 'state' => $outlet->state,
                'respondents' => round($headcount * mt_rand(55, 90) / 100),
                'headcount' => $headcount,
                'engagement_score' => $score,
                'e_nps' => max(-100, min(100, round($score * 1.4 - 90))),
                'grievance_compensation' => mt_rand(0, 3), 'grievance_conduct' => mt_rand(0, 2),
                'grievance_workload' => mt_rand(0, 3), 'grievance_facilities' => mt_rand(0, 2), 'grievance_other' => mt_rand(0, 2),
                'recognitions_given' => mt_rand(2, max(3, (int) round($headcount * 0.4))),
            ],
        );
    }
}
