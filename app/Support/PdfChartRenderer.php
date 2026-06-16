<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PdfChartRenderer
{
    private const PALETTE = ['#2F6BFF', '#9DB359', '#E8833A', '#8B5CF6', '#EC4899', '#14B8A6', '#F59E0B', '#0EA5E9'];

    private const CHART_WIDTH = 520;

    private const CHART_HEIGHT = 220;

    /**
     * Single-series line chart (Nilai Tes).
     *
     * @param  list<array{percent?: float|int, date?: string, label?: string}>  $data
     */
    public static function lineChart(array $data, string $color = '#2F6BFF', string $emptyText = 'Belum ada data.'): string
    {
        $points = [];
        foreach ($data as $row) {
            if (! isset($row['percent'])) {
                continue;
            }
            $points[] = [
                'label' => self::fmtDate((string) ($row['date'] ?? '')),
                'value' => (float) $row['percent'],
            ];
        }

        if ($points === []) {
            return self::emptyBox($emptyText);
        }

        $datasets = [[
            'label' => 'Nilai Tes (%)',
            'data' => array_column($points, 'value'),
            'borderColor' => $color,
            'backgroundColor' => $color,
            'fill' => false,
            'borderWidth' => 2.5,
            'pointRadius' => 4,
            'pointBackgroundColor' => $color,
            'pointBorderColor' => '#ffffff',
            'pointBorderWidth' => 1,
            'lineTension' => 0.15,
        ]];

        return self::renderChartImage(
            array_column($points, 'label'),
            $datasets,
            ['beginAtZero' => true, 'max' => 100],
            $emptyText,
        );
    }

    /**
     * Multi-series line chart (Nilai per Mata Pelajaran / Hasil Jasmani).
     *
     * @param  list<array{label?: string, unit?: string|null, points?: list<array{date?: string, percent?: float|int, value?: float|int}>}>  $series
     */
    public static function multiLineChart(array $series, string $valueMode = 'percent', string $emptyText = 'Belum ada data.'): string
    {
        $clean = [];
        foreach ($series as $idx => $s) {
            $pts = [];
            foreach ($s['points'] ?? [] as $p) {
                $val = $p['percent'] ?? $p['value'] ?? null;
                $date = (string) ($p['date'] ?? '');
                if ($date === '' || $val === null || ! is_numeric($val)) {
                    continue;
                }
                $pts[$date] = (float) $val;
            }
            if ($pts !== []) {
                $unit = $s['unit'] ?? null;
                $label = (string) ($s['label'] ?? 'Seri');
                if ($unit && $valueMode !== 'percent') {
                    $label .= ' ('.$unit.')';
                }
                $clean[] = [
                    'label' => $label,
                    'color' => self::PALETTE[$idx % count(self::PALETTE)],
                    'points' => $pts,
                ];
            }
        }

        if ($clean === []) {
            return self::emptyBox($emptyText);
        }

        $allDates = [];
        foreach ($clean as $s) {
            foreach (array_keys($s['points']) as $date) {
                $allDates[$date] = true;
            }
        }
        $allDates = array_keys($allDates);
        sort($allDates);
        $labels = array_map(fn ($d) => self::fmtDate($d), $allDates);

        $datasets = [];
        foreach ($clean as $s) {
            $values = [];
            foreach ($allDates as $date) {
                $values[] = $s['points'][$date] ?? null;
            }
            $datasets[] = [
                'label' => $s['label'],
                'data' => $values,
                'borderColor' => $s['color'],
                'backgroundColor' => $s['color'],
                'fill' => false,
                'borderWidth' => 2.5,
                'pointRadius' => 4,
                'pointBackgroundColor' => $s['color'],
                'pointBorderColor' => '#ffffff',
                'pointBorderWidth' => 1,
                'lineTension' => 0.15,
                'spanGaps' => false,
            ];
        }

        $max = null;
        if ($valueMode === 'percent') {
            $max = 100;
        }

        return self::renderChartImage($labels, $datasets, ['beginAtZero' => true, 'max' => $max], $emptyText);
    }

    /**
     * @param  list<string>  $labels
     * @param  list<array<string, mixed>>  $datasets
     * @param  array{beginAtZero?: bool, max?: int|null}  $yAxis
     */
    private static function renderChartImage(array $labels, array $datasets, array $yAxis, string $fallbackText): string
    {
        $yTicks = ['beginAtZero' => (bool) ($yAxis['beginAtZero'] ?? true), 'fontSize' => 10, 'fontColor' => '#9ca3af'];
        if (isset($yAxis['max']) && $yAxis['max'] !== null) {
            $yTicks['max'] = $yAxis['max'];
        }

        $config = [
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => $datasets,
            ],
            'options' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'fontSize' => 10,
                        'fontColor' => '#4b5563',
                        'boxWidth' => 14,
                        'padding' => 12,
                    ],
                ],
                'scales' => [
                    'yAxes' => [[
                        'ticks' => $yTicks,
                        'gridLines' => ['color' => '#eef0f2', 'zeroLineColor' => '#dde1e6'],
                    ]],
                    'xAxes' => [[
                        'ticks' => ['fontSize' => 10, 'fontColor' => '#9ca3af'],
                        'gridLines' => ['display' => false],
                    ]],
                ],
                'layout' => ['padding' => ['top' => 8, 'right' => 12, 'bottom' => 0, 'left' => 4]],
            ],
        ];

        $png = self::fetchChartPng($config);
        if ($png === null) {
            return self::fallbackTable($labels, $datasets, $fallbackText);
        }

        $encoded = base64_encode($png);

        return '<img src="data:image/png;base64,'.$encoded.'" alt="Chart" class="chart-image" width="'.self::CHART_WIDTH.'" height="'.self::CHART_HEIGHT.'" />';
    }

    /**
     * @param  array<string, mixed>  $config
     */
    private static function fetchChartPng(array $config): ?string
    {
        try {
            $query = http_build_query([
                'width' => self::CHART_WIDTH,
                'height' => self::CHART_HEIGHT,
                'format' => 'png',
                'backgroundColor' => 'white',
                'chart' => json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ]);

            $response = Http::timeout(20)
                ->retry(2, 300)
                ->get('https://quickchart.io/chart?'.$query);

            if ($response->successful() && str_starts_with((string) $response->header('Content-Type'), 'image/')) {
                return $response->body();
            }

            Log::warning('QuickChart PDF chart request failed.', [
                'status' => $response->status(),
                'content_type' => $response->header('Content-Type'),
            ]);
        } catch (\Throwable $e) {
            Log::warning('QuickChart PDF chart request error.', ['error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * @param  list<string>  $labels
     * @param  list<array<string, mixed>>  $datasets
     */
    private static function fallbackTable(array $labels, array $datasets, string $title): string
    {
        $rows = '';
        foreach ($labels as $i => $label) {
            $cells = '<td>'.e($label).'</td>';
            foreach ($datasets as $dataset) {
                $val = $dataset['data'][$i] ?? null;
                $cells .= '<td>'.e($val === null ? '—' : (string) $val).'</td>';
            }
            $rows .= '<tr>'.$cells.'</tr>';
        }

        $headers = '<th>Tanggal</th>';
        foreach ($datasets as $dataset) {
            $headers .= '<th>'.e((string) ($dataset['label'] ?? '-')).'</th>';
        }

        return <<<HTML
<div class="chart-fallback">
  <div class="muted" style="margin-bottom:6px;">{$title} (tampilan tabel)</div>
  <table class="data-table">
    <thead><tr>{$headers}</tr></thead>
    <tbody>{$rows}</tbody>
  </table>
</div>
HTML;
    }

    private static function emptyBox(string $text): string
    {
        return '<div class="chart-empty">'.e($text).'</div>';
    }

    private static function fmtDate(string $date): string
    {
        if ($date === '') {
            return '';
        }
        $parts = explode('-', $date);
        if (count($parts) === 3) {
            return $parts[2].'/'.$parts[1];
        }

        return $date;
    }
}
