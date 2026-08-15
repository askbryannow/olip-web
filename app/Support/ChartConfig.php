<?php

namespace App\Support;

/**
 * Builds Chart.js config arrays styled consistently with the app's design tokens.
 * Color values use CSS custom-property placeholders (e.g. 'var(--series-1)') which
 * the frontend (resources/js/app.js) resolves to real colors — and to real
 * tooltip/tick formatter functions — at render time (see resolveConfig()).
 */
class ChartConfig
{
    public const SERIES = ['var(--series-1)', 'var(--series-2)', 'var(--series-3)', 'var(--series-4)', 'var(--series-5)', 'var(--series-6)', 'var(--series-7)', 'var(--series-8)'];

    private static function baseOptions(string $format = 'number'): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'animation' => ['duration' => 400],
            'plugins' => [
                'legend' => ['display' => false],
                'tooltip' => [
                    'backgroundColor' => 'var(--surface-card)',
                    'titleColor' => 'var(--text-primary)',
                    'bodyColor' => 'var(--text-secondary)',
                    'borderColor' => 'var(--border-hairline)',
                    'borderWidth' => 1,
                    'padding' => 10,
                    'callbacks' => ['label' => "__tooltipfmt:$format"],
                ],
            ],
        ];
    }

    /** @param array<int, array{label: string, data: array, color?: string, stack?: string, fill?: bool}> $datasets */
    public static function bar(array $labels, array $datasets, string $format = 'number', bool $horizontal = false, bool $stacked = false): array
    {
        $options = self::baseOptions($format);
        $options['indexAxis'] = $horizontal ? 'y' : 'x';
        $valueAxis = $horizontal ? 'x' : 'y';
        $categoryAxis = $horizontal ? 'y' : 'x';
        $options['scales'] = [
            $categoryAxis => ['grid' => ['display' => false], 'ticks' => ['color' => 'var(--text-muted)', 'font' => ['size' => 11]]],
            $valueAxis => [
                'stacked' => $stacked,
                'grid' => ['color' => 'var(--gridline)', 'drawTicks' => false],
                'ticks' => ['color' => 'var(--text-muted)', 'font' => ['size' => 11], 'callback' => "__tickfmt:$format"],
                'border' => ['display' => false],
            ],
        ];
        if ($stacked) {
            $options['scales'][$categoryAxis]['stacked'] = true;
        }
        if (count($datasets) > 1) {
            $options['plugins']['legend']['display'] = true;
            $options['plugins']['legend']['labels'] = ['color' => 'var(--text-secondary)', 'boxWidth' => 8, 'boxHeight' => 8, 'usePointStyle' => true, 'font' => ['size' => 11]];
        }

        return [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                // Only assign a `stack` id for genuinely stacked charts — Chart.js overlaps any
                // datasets that share one, so grouped/side-by-side bars must each get their own.
                'datasets' => array_map(fn ($d, $i) => array_filter([
                    'label' => $d['label'],
                    'data' => $d['data'],
                    'backgroundColor' => $d['color'] ?? self::SERIES[$i % count(self::SERIES)],
                    'borderRadius' => 4,
                    'borderSkipped' => false,
                    'stack' => $d['stack'] ?? ($stacked ? 'stack-0' : null),
                    'maxBarThickness' => 26,
                ], fn ($v) => $v !== null), $datasets, array_keys($datasets)),
            ],
            'options' => $options,
        ];
    }

    /** Single-series bar with a distinct color per category (e.g. headcount by BU). */
    public static function categoricalBar(array $labels, array $data, string $format = 'number', bool $horizontal = true): array
    {
        $config = self::bar($labels, [['label' => 'Value', 'data' => $data]], $format, $horizontal);
        $config['data']['datasets'][0]['backgroundColor'] = array_map(fn ($i) => self::SERIES[$i % count(self::SERIES)], array_keys($labels));

        return $config;
    }

    /** @param array<int, array{label: string, data: array, color?: string, dashed?: bool}> $datasets */
    public static function line(array $labels, array $datasets, string $format = 'number'): array
    {
        $options = self::baseOptions($format);
        $options['scales'] = [
            'x' => ['grid' => ['display' => false], 'ticks' => ['color' => 'var(--text-muted)', 'font' => ['size' => 11], 'maxRotation' => 0]],
            'y' => [
                'grid' => ['color' => 'var(--gridline)', 'drawTicks' => false],
                'ticks' => ['color' => 'var(--text-muted)', 'font' => ['size' => 11], 'callback' => "__tickfmt:$format"],
                'border' => ['display' => false],
            ],
        ];
        if (count($datasets) > 1) {
            $options['plugins']['legend']['display'] = true;
            $options['plugins']['legend']['labels'] = ['color' => 'var(--text-secondary)', 'boxWidth' => 8, 'boxHeight' => 8, 'usePointStyle' => true, 'font' => ['size' => 11]];
        }

        return [
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => array_map(fn ($d, $i) => [
                    'label' => $d['label'],
                    'data' => $d['data'],
                    'borderColor' => $d['color'] ?? self::SERIES[$i % count(self::SERIES)],
                    'backgroundColor' => $d['color'] ?? self::SERIES[$i % count(self::SERIES)],
                    'borderWidth' => 2,
                    'pointRadius' => 0,
                    'pointHoverRadius' => 4,
                    'tension' => 0.35,
                    'borderDash' => ($d['dashed'] ?? false) ? [4, 3] : [],
                ], $datasets, array_keys($datasets)),
            ],
            'options' => $options,
        ];
    }

    /** Stacked area (filled line) chart, e.g. payroll cost trend. */
    public static function area(array $labels, array $datasets, string $format = 'currency'): array
    {
        $config = self::line($labels, $datasets, $format);
        $config['options']['scales']['y']['stacked'] = true;
        foreach ($config['data']['datasets'] as $i => &$ds) {
            $ds['fill'] = $i === 0 ? 'origin' : '-1';
            $ds['backgroundColor'] = $ds['borderColor'];
            $ds['tension'] = 0.3;
        }

        return $config;
    }
}
