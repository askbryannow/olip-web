<?php

namespace App\Support;

/** Global dashboard filters (month/BU/state/outlet/employment type), persisted in the session. */
class Filters
{
    public const DEFAULTS = [
        'monthKey' => 'ALL',
        'bu' => 'ALL',
        'state' => 'ALL',
        'outletId' => 'ALL',
        'employmentType' => 'ALL',
    ];

    public static function current(): array
    {
        return array_merge(self::DEFAULTS, session('filters', []));
    }

    public static function set(array $filters): void
    {
        session(['filters' => array_merge(self::current(), $filters)]);
    }

    public static function reset(): void
    {
        session(['filters' => self::DEFAULTS]);
    }
}
