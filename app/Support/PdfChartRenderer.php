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

        $dots = self::buildPercentDots($points);
        $svg = self::percentGridLines().self::renderLineSegments($dots, $color).self::renderMarkers($dots, $color);

        $dateStart = self::fmtDate($points[0]['date']);
        $dateEnd = self::fmtDate($points[count($points) - 1]['date']);

        return self::chartLayout(
            $svg,
            '0%',
            '50%',
            '100%',
            $dateStart,
            $dateEnd,
            '',
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

        $svg = self::fractionGridLines();
        $legend = '';

        foreach ($clean as $s) {
            $dots = [];
            foreach ($s['points'] as $p) {
                $dots[] = [
                    'x' => self::xForDate($p['date'], $allDates),
                    'y' => self::yForValue($p['value'], $max),
                ];
            }

            $svg .= self::renderLineSegments($dots, $s['color']);
            $svg .= self::renderMarkers($dots, $s['color']);

            $unitSuffix = ($s['unit'] && $valueMode !== 'percent') ? ' ('.e($s['unit']).')' : '';
            $legend .= sprintf(
                '<div class="legend-item"><span class="legend-dot" style="background:%s;"></span>%s%s</div>',
                $s['color'],
                e($s['label']),
                $unitSuffix
            );
        }

        $yTop = $valueMode === 'percent' ? '100%' : (string) round($max);
        $yMid = $valueMode === 'percent' ? '50%' : (string) round($max / 2);
        $yBot = $valueMode === 'percent' ? '0%' : '0';

        return self::chartLayout(
            $svg,
            $yTop,
            $yMid,
            $yBot,
            self::fmtDate($allDates[0] ?? ''),
            self::fmtDate($allDates[count($allDates) - 1] ?? ''),
            $legend,
        );
    }

    /**
     * @param  list<array{percent: float, date: string}>  $points
     * @return list<array{x: float, y: float}>
     */
    private static function buildPercentDots(array $points): array
    {
        $n = count($points);
        $dots = [];
        foreach ($points as $i => $p) {
            $dots[] = [
                'x' => $n === 1 ? self::W / 2 : ($i / ($n - 1)) * self::W,
                'y' => self::yForPercent($p['percent']),
            ];
        }

        return $dots;
    }

    /**
     * @param  list<array{x: float, y: float}>  $dots
     */
    private static function renderLineSegments(array $dots, string $color): string
    {
        if (count($dots) < 2) {
            return '';
        }

        $lines = '';
        for ($i = 1; $i < count($dots); $i++) {
            $a = $dots[$i - 1];
            $b = $dots[$i];
            $lines .= sprintf(
                '<line x1="%.2f" y1="%.2f" x2="%.2f" y2="%.2f" stroke="%s" stroke-width="2.5"/>',
                $a['x'],
                $a['y'],
                $b['x'],
                $b['y'],
                $color,
            );
        }

        return $lines;
    }

    /**
     * DomPDF renders rects more reliably than circles.
     *
     * @param  list<array{x: float, y: float}>  $dots
     */
    private static function renderMarkers(array $dots, string $color): string
    {
        $markers = '';
        foreach ($dots as $d) {
            $size = 7;
            $markers .= sprintf(
                '<rect x="%.2f" y="%.2f" width="%d" height="%d" fill="%s" stroke="#ffffff" stroke-width="1"/>',
                $d['x'] - ($size / 2),
                $d['y'] - ($size / 2),
                $size,
                $size,
                $color,
            );
        }

        return $markers;
    }

    private static function chartLayout(
        string $svgBody,
        string $yTop,
        string $yMid,
        string $yBot,
        string $dateStart,
        string $dateEnd,
        string $legend,
    ): string {
        return <<<HTML
<div class="chart-wrap">
  <table class="chart-table" cellpadding="0" cellspacing="0">
    <tr>
      <td class="chart-yaxis">
        <div>{$yTop}</div>
        <div>{$yMid}</div>
        <div>{$yBot}</div>
      </td>
      <td class="chart-plot">
        <svg xmlns="http://www.w3.org/2000/svg" width="320" height="160" viewBox="0 0 320 160">
          {$svgBody}
        </svg>
        <table class="chart-dates-table" width="100%" cellpadding="0" cellspacing="0">
          <tr>
            <td align="left">{$dateStart}</td>
            <td align="right">{$dateEnd}</td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <div class="chart-legend">{$legend}</div>
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
            $lines .= sprintf('<line x1="0" y1="%.2f" x2="%d" y2="%.2f" stroke="#dde1e6" stroke-width="1"/>', $y, self::W, $y);
        }

        return $lines;
    }

    private static function fractionGridLines(): string
    {
        $lines = '';
        foreach ([0, 0.25, 0.5, 0.75, 1] as $frac) {
            $y = self::yForFrac($frac);
            $lines .= sprintf('<line x1="0" y1="%.2f" x2="%d" y2="%.2f" stroke="#dde1e6" stroke-width="1"/>', $y, self::W, $y);
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
        if ($index === false) {
            return self::W / 2;
        }

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
