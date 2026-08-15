<?php

namespace App\Support;

class Colors {
    private const SEQ_STEPS = ['#cde2fb', '#b7d3f6', '#9ec5f4', '#86b6ef', '#6da7ec', '#5598e7', '#3987e5', '#2a78d6', '#256abf', '#1c5cab', '#184f95', '#104281', '#0d366b'];

    public static function seqStep(float $value, float $min, float $max): string
    {
        if ($max === $min) {
            return self::SEQ_STEPS[(int) floor(count(self::SEQ_STEPS) / 2)];
        }
        $t = max(0, min(1, ($value - $min) / ($max - $min)));

        return self::SEQ_STEPS[(int) round($t * (count(self::SEQ_STEPS) - 1))];
    }

    /** Whether black (light-mode) text reads better than white on this sequential step. */
    public static function seqStepIsLight(float $value, float $min, float $max): bool
    {
        if ($max === $min) {
            return true;
        }
        $t = max(0, min(1, ($value - $min) / ($max - $min)));

        return $t < 0.6;
    }
}
