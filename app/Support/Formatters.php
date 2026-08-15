<?php

namespace App\Support;

class Formatters
{
    public static function myr(float $n, bool $compact = true): string
    {
        if ($compact && abs($n) >= 1_000_000) {
            return 'RM '.number_format($n / 1_000_000, 2).'M';
        }
        if ($compact && abs($n) >= 1_000) {
            return 'RM '.number_format($n / 1_000, 1).'K';
        }

        return 'RM '.number_format(round($n));
    }

    public static function num(float $n): string
    {
        return number_format(round($n));
    }

    public static function pct(float $n, int $digits = 1): string
    {
        return number_format($n, $digits).'%';
    }
}
