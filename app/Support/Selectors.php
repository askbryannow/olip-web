<?php

namespace App\Support;

use App\Models\AttendanceRecord;
use App\Models\BusinessPerformanceRecord;
use App\Models\Employee;
use App\Models\EngagementRecord;
use App\Models\IncidentRecord;
use App\Models\Outlet;
use App\Models\OvertimeRecord;
use App\Models\RecruitmentRecord;
use App\Models\TrainingRecord;
use Illuminate\Support\Collection;

/**
 * Chart/KPI aggregation, mirroring the frontend's src/lib/selectors.ts function-for-function.
 * Every method takes the same $filters array (monthKey/bu/state/outletId/employmentType) and
 * either a CalendarMonth or a window size in months.
 */
class Selectors
{
    // ---------- calendar ----------

    public static function endMonth(array $filters): CalendarMonth
    {
        $calendar = Calendar::trailing();
        if (($filters['monthKey'] ?? 'ALL') !== 'ALL') {
            $found = collect($calendar)->firstWhere('key', $filters['monthKey']);
            if ($found) {
                return $found;
            }
        }

        return $calendar[count($calendar) - 1];
    }

    /** @return CalendarMonth[] */
    public static function trailingMonths(array $filters, int $n = 12): array
    {
        $calendar = Calendar::trailing();
        $end = self::endMonth($filters);
        $endIdx = collect($calendar)->search(fn ($c) => $c->key === $end->key);
        $startIdx = max(0, $endIdx - $n + 1);

        return array_slice($calendar, $startIdx, $endIdx - $startIdx + 1);
    }

    public static function deptScope(array $filters): array
    {
        $outlets = Outlet::query()->matchingOutletDims($filters)->get();

        return $outlets->flatMap(fn ($o) => $o->departments)->unique()->values()->all();
    }

    // ---------- headcount / employees ----------

    public static function headcountAt(array $filters, \DateTimeInterface $date): int
    {
        return Employee::query()
            ->matchingDims($filters)->matchingEmploymentType($filters)
            ->activeAt(\Carbon\CarbonImmutable::parse($date))
            ->count();
    }

    public static function hiresInMonth(array $filters, CalendarMonth $cm): Collection
    {
        return Employee::query()
            ->matchingDims($filters)->matchingEmploymentType($filters)
            ->whereYear('join_date', $cm->year)->whereMonth('join_date', $cm->month)
            ->get();
    }

    public static function resignationsInMonth(array $filters, CalendarMonth $cm): Collection
    {
        return Employee::query()
            ->matchingDims($filters)->matchingEmploymentType($filters)
            ->whereNotNull('exit_date')
            ->whereYear('exit_date', $cm->year)->whereMonth('exit_date', $cm->month)
            ->get();
    }

    public static function headcountTrend(array $filters, int $n = 12): array
    {
        return array_map(fn (CalendarMonth $cm) => [
            'month' => $cm->label, 'key' => $cm->key,
            'headcount' => self::headcountAt($filters, $cm->endOfMonth()),
        ], self::trailingMonths($filters, $n));
    }

    public static function hireResignTrend(array $filters, int $n = 12): array
    {
        return array_map(function (CalendarMonth $cm) use ($filters) {
            $hires = self::hiresInMonth($filters, $cm)->count();
            $resigns = self::resignationsInMonth($filters, $cm)->count();

            return ['month' => $cm->label, 'key' => $cm->key, 'hires' => $hires, 'resignations' => $resigns, 'netGrowth' => $hires - $resigns];
        }, self::trailingMonths($filters, $n));
    }

    public static function turnoverRateTrailing(array $filters, int $months = 12): float
    {
        $cal = self::trailingMonths($filters, $months);
        $resignCount = collect($cal)->sum(fn ($cm) => self::resignationsInMonth($filters, $cm)->count());
        $avgHc = collect($cal)->avg(fn ($cm) => self::headcountAt($filters, $cm->endOfMonth()));

        return $avgHc > 0 ? ($resignCount / $avgHc) * 100 : 0;
    }

    public static function genderMix(array $filters, \DateTimeInterface $date): array
    {
        $emps = Employee::query()->matchingDims($filters)->matchingEmploymentType($filters)
            ->activeAt(\Carbon\CarbonImmutable::parse($date))->get();
        $male = $emps->where('gender', 'Male')->count();

        return ['male' => $male, 'female' => $emps->count() - $male, 'total' => $emps->count()];
    }

    public static function employmentTypeMix(array $filters, \DateTimeInterface $date): array
    {
        $emps = Employee::query()->matchingDims($filters)->matchingEmploymentType($filters)
            ->activeAt(\Carbon\CarbonImmutable::parse($date))->get()
            ->groupBy('employment_type');

        return collect(Options::EMPLOYMENT_TYPES)
            ->map(fn ($t) => ['type' => $t, 'count' => $emps->get($t, collect())->count()])
            ->all();
    }

    public static function headcountByBU(array $filters, \DateTimeInterface $date): array
    {
        $emps = Employee::query()->matchingDims($filters)->matchingEmploymentType($filters)
            ->activeAt(\Carbon\CarbonImmutable::parse($date))->get()
            ->groupBy('bu');

        return $emps->map(fn ($list, $bu) => ['bu' => $bu, 'headcount' => $list->count()])
            ->values()->sortByDesc('headcount')->values()->all();
    }

    // ---------- payroll ----------

    public static function basePayFor(array $filters, CalendarMonth $cm): float
    {
        return (float) Employee::query()->matchingDims($filters)->matchingEmploymentType($filters)
            ->activeAt($cm->endOfMonth())->sum('base_salary');
    }

    public static function overtimeFor(array $filters, CalendarMonth $cm): array
    {
        $recs = OvertimeRecord::query()->matchingDims($filters)->where('month_key', $cm->key)->get();

        return ['hours' => (float) $recs->sum('overtime_hours'), 'pay' => (float) $recs->sum('overtime_pay'), 'budgetHours' => (float) $recs->sum('budget_hours')];
    }

    public static function statutoryFor(float $basePay): float
    {
        return $basePay * 0.175;
    }

    public static function payrollCostFor(array $filters, CalendarMonth $cm): float
    {
        $base = self::basePayFor($filters, $cm);

        return $base + self::overtimeFor($filters, $cm)['pay'] + self::statutoryFor($base);
    }

    public static function revenueFor(array $filters, CalendarMonth $cm): float
    {
        return (float) BusinessPerformanceRecord::query()->matchingDims($filters)->where('month_key', $cm->key)->sum('revenue');
    }

    public static function payrollTrend(array $filters, int $n = 12): array
    {
        return array_map(function (CalendarMonth $cm) use ($filters) {
            $base = self::basePayFor($filters, $cm);
            $ot = self::overtimeFor($filters, $cm)['pay'];
            $statutory = self::statutoryFor($base);

            return ['month' => $cm->label, 'key' => $cm->key, 'base' => $base, 'overtime' => $ot, 'statutory' => $statutory, 'total' => $base + $ot + $statutory];
        }, self::trailingMonths($filters, $n));
    }

    public static function overtimeByDept(array $filters, CalendarMonth $cm): array
    {
        $recs = OvertimeRecord::query()->matchingDims($filters)->where('month_key', $cm->key)->get()->groupBy('department');

        return $recs->map(fn ($list, $dept) => [
            'department' => $dept,
            'actualHours' => (float) $list->sum('overtime_hours'),
            'budgetHours' => (float) $list->sum('budget_hours'),
            'pay' => (float) $list->sum('overtime_pay'),
        ])->values()->sortByDesc('actualHours')->values()->all();
    }

    public static function payrollPctRevenueByOutlet(array $filters, CalendarMonth $cm): array
    {
        $outlets = Outlet::query()->matchingOutletDims($filters)->whereIn('bu', Options::OPERATING_BUS)->get();

        return $outlets->map(function ($o) use ($filters, $cm) {
            $outletFilters = array_merge($filters, ['outletId' => $o->id]);
            $payroll = self::payrollCostFor($outletFilters, $cm);
            $revenue = self::revenueFor($outletFilters, $cm);

            return ['outlet' => $o->name, 'payroll' => $payroll, 'revenue' => $revenue, 'pct' => $revenue > 0 ? ($payroll / $revenue) * 100 : 0];
        })->sortByDesc('pct')->values()->all();
    }

    // ---------- attendance ----------

    public static function attendanceRecords(array $filters, CalendarMonth $cm): Collection
    {
        return AttendanceRecord::query()->matchingDims($filters)->where('month_key', $cm->key)->get();
    }

    public static function absenteeismTrend(array $filters, int $n = 12): array
    {
        return array_map(function (CalendarMonth $cm) use ($filters) {
            $recs = self::attendanceRecords($filters, $cm);
            $totalAvailable = $recs->sum(fn ($r) => $r->headcount * $r->working_days);
            $totalAbsent = $recs->sum('absent_days');

            return ['month' => $cm->label, 'key' => $cm->key, 'rate' => $totalAvailable > 0 ? ($totalAbsent / $totalAvailable) * 100 : 0];
        }, self::trailingMonths($filters, $n));
    }

    public static function leaveByType(array $filters, int $n = 12): array
    {
        $cal = self::trailingMonths($filters, $n);
        $recs = collect($cal)->flatMap(fn ($cm) => self::attendanceRecords($filters, $cm));
        $types = [
            'Annual' => 'leave_annual', 'Medical' => 'leave_medical', 'Emergency' => 'leave_emergency',
            'Unpaid' => 'leave_unpaid', 'Maternity/Paternity' => 'leave_maternity_paternity',
        ];

        return collect($types)->map(fn ($col, $label) => ['type' => $label, 'days' => (float) $recs->sum($col)])->values()->all();
    }

    public static function lateClockInsTrend(array $filters, int $n = 12): array
    {
        return array_map(fn (CalendarMonth $cm) => [
            'month' => $cm->label, 'key' => $cm->key,
            'late' => (float) self::attendanceRecords($filters, $cm)->sum('late_clock_ins'),
        ], self::trailingMonths($filters, $n));
    }

    public static function lateClockInsByDept(array $filters, CalendarMonth $cm): array
    {
        return self::attendanceRecords($filters, $cm)->groupBy('department')
            ->map(fn ($list, $dept) => ['department' => $dept, 'late' => (float) $list->sum('late_clock_ins')])
            ->values()->sortByDesc('late')->values()->all();
    }

    // ---------- recruitment ----------

    public static function recruitmentRecords(array $filters, array $months): Collection
    {
        $keys = collect($months)->pluck('key');

        return RecruitmentRecord::query()->matchingDims($filters)->whereIn('month_key', $keys)->get();
    }

    public static function recruitmentFunnel(array $filters, int $n = 3): array
    {
        $recs = self::recruitmentRecords($filters, self::trailingMonths($filters, $n));

        return [
            ['label' => 'Applied', 'value' => (float) $recs->sum('applied')],
            ['label' => 'Screened', 'value' => (float) $recs->sum('screened')],
            ['label' => 'Interviewed', 'value' => (float) $recs->sum('interviewed')],
            ['label' => 'Offered', 'value' => (float) $recs->sum('offered')],
            ['label' => 'Joined', 'value' => (float) $recs->sum('joined')],
        ];
    }

    public static function timeToHireTrend(array $filters, int $n = 12): array
    {
        return array_map(function (CalendarMonth $cm) use ($filters) {
            $recs = self::recruitmentRecords($filters, [$cm])->filter(fn ($r) => $r->joined > 0);
            $weighted = $recs->sum(fn ($r) => $r->avg_time_to_hire_days * $r->joined);
            $totalJoined = $recs->sum('joined');

            return ['month' => $cm->label, 'key' => $cm->key, 'days' => $totalJoined > 0 ? $weighted / $totalJoined : 0];
        }, self::trailingMonths($filters, $n));
    }

    public static function costPerHireTrend(array $filters, int $n = 12): array
    {
        return array_map(function (CalendarMonth $cm) use ($filters) {
            $recs = self::recruitmentRecords($filters, [$cm])->filter(fn ($r) => $r->joined > 0);
            $weighted = $recs->sum(fn ($r) => $r->cost_per_hire * $r->joined);
            $totalJoined = $recs->sum('joined');

            return ['month' => $cm->label, 'key' => $cm->key, 'cost' => $totalJoined > 0 ? $weighted / $totalJoined : 0];
        }, self::trailingMonths($filters, $n));
    }

    public static function vacancyAging(array $filters, CalendarMonth $cm): array
    {
        $recs = self::recruitmentRecords($filters, [$cm])->filter(fn ($r) => $r->open_vacancies > 0)->groupBy('department');

        return $recs->map(fn ($list, $dept) => [
            'department' => $dept,
            'openVacancies' => (float) $list->sum('open_vacancies'),
            'avgAgeDays' => (float) $list->avg('avg_vacancy_age_days'),
        ])->values()->sortByDesc('openVacancies')->values()->all();
    }

    // ---------- turnover ----------

    public static function turnoverTrend(array $filters, int $n = 12): array
    {
        return array_map(function (CalendarMonth $cm) use ($filters) {
            $resigns = self::resignationsInMonth($filters, $cm);
            $voluntary = $resigns->where('exit_type', 'Voluntary')->count();
            $involuntary = $resigns->count() - $voluntary;
            $hc = self::headcountAt($filters, $cm->endOfMonth());

            return [
                'month' => $cm->label, 'key' => $cm->key, 'voluntary' => $voluntary, 'involuntary' => $involuntary,
                'total' => $resigns->count(), 'rate' => $hc > 0 ? ($resigns->count() / $hc) * 100 : 0,
            ];
        }, self::trailingMonths($filters, $n));
    }

    public static function earlyAttritionRate(array $filters, int $n = 12): float
    {
        $cal = self::trailingMonths($filters, $n);
        $resigns = collect($cal)->flatMap(fn ($cm) => self::resignationsInMonth($filters, $cm));
        if ($resigns->isEmpty()) {
            return 0;
        }
        $early = $resigns->filter(fn ($e) => $e->exit_date && $e->join_date->diffInDays($e->exit_date) < 365)->count();

        return ($early / $resigns->count()) * 100;
    }

    public static function turnoverHeatmapByDept(array $filters, int $n = 6): array
    {
        $cal = self::trailingMonths($filters, $n);
        $depts = self::deptScope($filters);

        $cells = [];
        foreach ($depts as $dept) {
            foreach ($cal as $cm) {
                $resigns = self::resignationsInMonth($filters, $cm)->where('department', $dept)->count();
                $hc = Employee::query()->matchingDims($filters)->where('department', $dept)
                    ->activeAt($cm->endOfMonth())->count();
                $cells[$dept][$cm->label] = $hc > 0 ? round(($resigns / $hc) * 1000) / 10 : 0;
            }
        }

        return ['months' => collect($cal)->pluck('label')->all(), 'depts' => $depts, 'cells' => $cells];
    }

    // ---------- training ----------

    public static function trainingRecords(array $filters, array $months): Collection
    {
        $keys = collect($months)->pluck('key');

        return TrainingRecord::query()->matchingDims($filters)->matchingEmploymentType($filters)
            ->whereIn('month_key', $keys)->get();
    }

    public static function trainingHoursTrend(array $filters, int $n = 12): array
    {
        return array_map(function (CalendarMonth $cm) use ($filters) {
            $recs = self::trainingRecords($filters, [$cm]);
            $hours = $recs->sum('training_hours');
            $hc = $recs->sum('headcount');

            return ['month' => $cm->label, 'key' => $cm->key, 'hoursPerEmployee' => $hc > 0 ? $hours / $hc : 0];
        }, self::trailingMonths($filters, $n));
    }

    public static function complianceTrend(array $filters, int $n = 12): array
    {
        return array_map(function (CalendarMonth $cm) use ($filters) {
            $recs = self::trainingRecords($filters, [$cm]);
            $due = $recs->sum('mandatory_due');
            $done = $recs->sum('mandatory_completed');

            return ['month' => $cm->label, 'key' => $cm->key, 'rate' => $due > 0 ? ($done / $due) * 100 : 0];
        }, self::trailingMonths($filters, $n));
    }

    public static function hrdcClaimsTrend(array $filters, int $n = 12): array
    {
        return array_map(function (CalendarMonth $cm) use ($filters) {
            $recs = self::trainingRecords($filters, [$cm]);

            return ['month' => $cm->label, 'key' => $cm->key, 'eligible' => (float) $recs->sum('hrdc_claim_eligible'), 'submitted' => (float) $recs->sum('hrdc_claim_submitted')];
        }, self::trailingMonths($filters, $n));
    }

    // ---------- engagement ----------

    public static function engagementRecords(array $filters): Collection
    {
        return EngagementRecord::query()->matchingDims($filters)->get();
    }

    public static function engagementTrend(array $filters): array
    {
        $byQ = self::engagementRecords($filters)->groupBy('quarter_key');

        return $byQ->map(fn ($list, $q) => [
            'quarter' => $q,
            'score' => (float) $list->avg('engagement_score'),
            'eNPS' => (float) $list->avg('e_nps'),
            'respondents' => (float) $list->sum('respondents'),
            'recognitions' => (float) $list->sum('recognitions_given'),
        ])->values()->sortBy('quarter')->values()->slice(-8)->values()->all();
    }

    public static function grievanceBreakdown(array $filters): array
    {
        $outletCount = max(1, Outlet::count());
        $recs = self::engagementRecords($filters)->slice(-$outletCount * 2);
        $cats = [
            'Compensation' => 'grievance_compensation', 'Conduct' => 'grievance_conduct', 'Workload' => 'grievance_workload',
            'Facilities' => 'grievance_facilities', 'Other' => 'grievance_other',
        ];

        return collect($cats)->map(fn ($col, $label) => ['category' => $label, 'count' => (float) $recs->sum($col)])->values()->all();
    }

    // ---------- safety ----------

    public static function incidentRecords(array $filters, array $months): Collection
    {
        $keys = collect($months)->pluck('key');

        return IncidentRecord::query()->matchingDims($filters)->whereIn('month_key', $keys)->get();
    }

    public static function ltifrTrend(array $filters, int $n = 12): array
    {
        $cumIncidents = 0;
        $cumHours = 0;

        return array_map(function (CalendarMonth $cm) use ($filters, &$cumIncidents, &$cumHours) {
            $recs = self::incidentRecords($filters, [$cm]);
            $lti = $recs->where('severity', 'Lost Time Injury')->sum('count');
            $hours = $recs->unique('outlet_id')->sum('hours_worked');
            $cumIncidents += $lti;
            $cumHours += $hours;

            return ['month' => $cm->label, 'key' => $cm->key, 'ltifr' => $cumHours > 0 ? ($cumIncidents * 1000000) / $cumHours : 0];
        }, self::trailingMonths($filters, $n));
    }

    public static function incidentsByOutlet(array $filters, int $n = 6): array
    {
        $recs = self::incidentRecords($filters, self::trailingMonths($filters, $n))->groupBy('outlet_id');
        $outlets = Outlet::all()->keyBy('id');

        return $recs->map(fn ($list, $outletId) => [
            'outlet' => $outlets->get($outletId)?->name ?? $outletId,
            'count' => (float) $list->sum('count'),
            'lti' => (float) $list->where('severity', 'Lost Time Injury')->sum('count'),
        ])->filter(fn ($r) => $r['count'] > 0)->sortByDesc('count')->values()->all();
    }

    public static function complianceByOutlet(array $filters, CalendarMonth $cm): array
    {
        $recs = self::incidentRecords($filters, [$cm])->groupBy('outlet_id');
        $outlets = Outlet::all()->keyBy('id');

        return $recs->map(fn ($list, $outletId) => [
            'outlet' => $outlets->get($outletId)?->name ?? $outletId,
            'score' => (float) $list->avg('compliance_audit_score'),
        ])->sortBy('score')->values()->all();
    }

    // ---------- business performance ----------

    public static function businessTrend(array $filters, int $n = 12): array
    {
        return array_map(fn (CalendarMonth $cm) => [
            'month' => $cm->label,
            'key' => $cm->key,
            'revenue' => self::revenueFor($filters, $cm),
            'visitors' => (float) BusinessPerformanceRecord::query()->matchingDims($filters)->where('month_key', $cm->key)->sum('visitors'),
            'headcount' => self::headcountAt($filters, $cm->endOfMonth()),
            'labourCost' => self::payrollCostFor($filters, $cm),
        ], self::trailingMonths($filters, $n));
    }
}
