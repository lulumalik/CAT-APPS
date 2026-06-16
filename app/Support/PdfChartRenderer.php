<?php

namespace App\Support;

class PdfChartRenderer
{
    private const PALETTE = ['#2F6BFF', '#9DB359', '#E8833A', '#8B5CF6', '#EC4899', '#14B8A6', '#F59E0B', '#0EA5E9'];

    private const W = 320;

    private const H = 160;

    /**
     * Single-series line chart (Nilai Tes).
     *
     * @param  list<array{percent?: float|int, date?: string}>  $data
     */
    public static function lineChart(array $data, string $color = '#2F6BFF', string $emptyText = 'Belum ada data.'): string
    {
        $points = [];
        foreach ($data as $row) {
            if (! isset($row['percent'])) {
                continue;
            }
            $points[] = [
                'percent' => (float) $row['percent'],
                'date' => (string) ($row['date'] ?? ''),
            ];
        }

        if ($points === []) {
            return self::emptyBox($emptyText);
        }

        $n = count($points);
        $dots = [];
        foreach ($points as $i => $p) {
            $dots[] = [
                'x' => $n === 1 ? self::W / 2 : ($i / ($n - 1)) * self::W,
                'y' => self::yForPercent($p['percent']),
            ];
        }

        $grid = self::percentGridLines();
        $polyline = implode(' ', array_map(fn ($d) => sprintf('%.2f,%.2f', $d['x'], $d['y']), $dots));
        $circles = '';
        foreach ($dots as $d) {
            $circles .= sprintf('<circle cx="%.2f" cy="%.2f" r="3.5" fill="%s"/>', $d['x'], $d['y'], $color);
        }

        $dateStart = self::fmtDate($points[0]['date']);
        $dateEnd = self::fmtDate($points[$n - 1]['date']);

        return self::wrapChart(<<<SVG
{$grid}
<polyline points="{$polyline}" fill="none" stroke="{$color}" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round"/>
{$circles}
SVG, <<<HTML
<div class="chart-dates"><span>{$dateStart}</span><span>{$dateEnd}</span></div>
HTML);
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
                $pts[] = ['date' => $date, 'value' => (float) $val];
            }
            if ($pts !== []) {
                $clean[] = [
                    'label' => (string) ($s['label'] ?? 'Seri'),
                    'unit' => $s['unit'] ?? null,
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
            foreach ($s['points'] as $p) {
                $allDates[$p['date']] = true;
            }
        }
        $allDates = array_keys($allDates);
        sort($allDates);

        $max = $valueMode === 'percent'
            ? 100
            : max(1, ...array_map(fn ($s) => max(array_column($s['points'], 'value')), $clean));

        $grid = self::fractionGridLines();
        $paths = '';
        $legend = '';

        foreach ($clean as $s) {
            $dots = [];
            foreach ($s['points'] as $p) {
                $dots[] = [
                    'x' => self::xForDate($p['date'], $allDates),
                    'y' => self::yForValue($p['value'], $max),
                ];
            }

            if (count($dots) > 1) {
                $polyline = implode(' ', array_map(fn ($d) => sprintf('%.2f,%.2f', $d['x'], $d['y']), $dots));
                $paths .= sprintf(
                    '<polyline points="%s" fill="none" stroke="%s" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round"/>',
                    $polyline,
                    $s['color']
                );
            }

            foreach ($dots as $d) {
                $paths .= sprintf('<circle cx="%.2f" cy="%.2f" r="3" fill="%s"/>', $d['x'], $d['y'], $s['color']);
            }

            $unitSuffix = ($s['unit'] && $valueMode !== 'percent') ? ' ('.e($s['unit']).')' : '';
            $legend .= sprintf(
                '<span class="legend-item"><span class="legend-dot" style="background:%s"></span>%s%s</span>',
                $s['color'],
                e($s['label']),
                $unitSuffix
            );
        }

        $yTop = $valueMode === 'percent' ? '100%' : (string) round($max);
        $yMid = $valueMode === 'percent' ? '50%' : (string) round($max / 2);
        $yBot = $valueMode === 'percent' ? '0%' : '0';

        $dateStart = self::fmtDate($allDates[0] ?? '');
        $dateEnd = self::fmtDate($allDates[count($allDates) - 1] ?? '');

        return <<<HTML
<div class="chart-row">
  <div class="chart-yaxis">
    <span>{$yTop}</span>
    <span>{$yMid}</span>
    <span>{$yBot}</span>
  </div>
  <div class="chart-main">
    <svg viewBox="0 0 320 160" class="chart-svg" xmlns="http://www.w3.org/2000/svg">
      {$grid}
      {$paths}
    </svg>
    <div class="chart-dates"><span>{$dateStart}</span><span>{$dateEnd}</span></div>
  </div>
</div>
<div class="chart-legend">{$legend}</div>
HTML;
    }

    private static function wrapChart(string $svgBody, string $footer = ''): string
    {
        return <<<HTML
<div class="chart-box">
  <svg viewBox="0 0 320 160" class="chart-svg" xmlns="http://www.w3.org/2000/svg">
    {$svgBody}
  </svg>
  {$footer}
</div>
HTML;
    }

    private static function emptyBox(string $text): string
    {
        return '<div class="chart-empty">'.e($text).'</div>';
    }

    private static function percentGridLines(): string
    {
        $lines = '';
        foreach ([0, 25, 50, 75, 100] as $g) {
            $y = self::yForPercent($g);
            $lines .= sprintf('<line x1="0" x2="%d" y1="%.2f" y2="%.2f" stroke="#eef0f2" stroke-width="1"/>', self::W, $y, $y);
        }

        return $lines;
    }

    private static function fractionGridLines(): string
    {
        $lines = '';
        foreach ([0, 0.25, 0.5, 0.75, 1] as $frac) {
            $y = self::yForFrac($frac);
            $lines .= sprintf('<line x1="0" x2="%d" y1="%.2f" y2="%.2f" stroke="#eef0f2" stroke-width="1"/>', self::W, $y, $y);
        }

        return $lines;
    }

    private static function yForPercent(float $percent): float
    {
        $clamped = min(100, max(0, $percent));

        return self::H - ($clamped / 100) * 150 - 5;
    }

    private static function yForFrac(float $frac): float
    {
        return self::H - min(1, max(0, $frac)) * 150 - 5;
    }

    private static function yForValue(float $value, float $max): float
    {
        return self::yForFrac($max > 0 ? $value / $max : 0);
    }

    /**
     * @param  list<string>  $allDates
     */
    private static function xForDate(string $date, array $allDates): float
    {
        $n = count($allDates);
        if ($n <= 1) {
            return self::W / 2;
        }
        $index = array_search($date, $allDates, true);

        return ($index / ($n - 1)) * self::W;
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
