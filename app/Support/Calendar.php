<?php

namespace App\Support;

use Carbon\Carbon;
use Carbon\CarbonImmutable;

/**
 * A calendar month: value objects mirroring the frontend's CalendarMonth shape.
 *
 * @property-read string $key 'YYYY-MM'
 * @property-read string $label 'Aug 2026'
 */
class CalendarMonth
{
    public function __construct(
        public readonly string $key,
        public readonly string $label,
        public readonly int $year,
        public readonly int $month,
        public readonly CarbonImmutable $date,
        public readonly bool $isCurrent,
    ) {}

    public function startOfMonth(): CarbonImmutable
    {
        return $this->date->startOfMonth();
    }

    public function endOfMonth(): CarbonImmutable
    {
        return $this->date->endOfMonth();
    }
}

class Calendar
{
    /** @return CalendarMonth[] */
    public static function trailing(?Carbon $referenceDate = null, int $windowMonths = 24): array
    {
        $reference = CarbonImmutable::parse($referenceDate ?? now())->startOfMonth();
        $months = [];

        for ($i = $windowMonths - 1; $i >= 0; $i--) {
            $d = $reference->subMonths($i);
            $months[] = new CalendarMonth(
                key: $d->format('Y-m'),
                label: $d->format('M Y'),
                year: (int) $d->format('Y'),
                month: (int) $d->format('n'),
                date: $d,
                isCurrent: $i === 0,
            );
        }

        return $months;
    }

    public static function currentMonthKey(): string
    {
        return now()->format('Y-m');
    }

    public static function currentQuarterKey(): string
    {
        return now()->year.'-Q'.(int) ceil(now()->month / 3);
    }
}
