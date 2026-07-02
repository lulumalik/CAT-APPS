<?php

namespace App\Support;

class PdfChartRenderer
{
    private const PALETTE = ['#2F6BFF', '#9DB359', '#E8833A', '#8B5CF6', '#EC4899', '#14B8A6', '#F59E0B', '#0EA5E9'];

    private const BAR_AREA_HEIGHT = 110;

    /**
     * @param  list<array{percent?: float|int, value?: float|int, date?: string}>  $data
     */
    public static function lineChart(
        array $data,
        string $color = '#2F6BFF',
        string $emptyText = 'Belum ada data.',
        string $valueMode = 'percent',
        float $fixedMax = 100,
        string $seriesLabel = 'Nilai Tes (%)',
    ): string {
        $points = [];
        foreach ($data as $row) {
            $val = $row['value'] ?? $row['percent'] ?? null;
            if ($val === null) {
                continue;
            }
            $date = (string) ($row['date'] ?? '');
            if ($date === '') {
                continue;
            }
            $points[$date] = (float) $val;
        }

        if ($points === []) {
            return self::emptyBox($emptyText);
        }

        ksort($points, SORT_STRING);
        $dates = array_keys($points);
        $max = $valueMode === 'percent' ? 100 : $fixedMax;

        return self::htmlChart(
            $dates,
            [[
                'label' => $seriesLabel,
                'color' => $color,
                'values' => $points,
            ]],
            $max,
            $valueMode,
        );
    }

    /**
     * @param  list<array{label?: string, unit?: string|null, points?: list<array{date?: string, percent?: float|int, value?: float|int}>}>  $series
     */
    public static function multiLineChart(
        array $series,
        string $valueMode = 'percent',
        string $emptyText = 'Belum ada data.',
        float $fixedMax = 100,
    ): string {
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
                ksort($pts);
                $label = (string) ($s['label'] ?? 'Seri');
                $unit = $s['unit'] ?? null;
                if ($unit && $valueMode !== 'percent') {
                    $label .= ' ('.$unit.')';
                }
                $clean[] = [
                    'label' => $label,
                    'color' => self::PALETTE[$idx % count(self::PALETTE)],
                    'values' => $pts,
                ];
            }
        }

        if ($clean === []) {
            return self::emptyBox($emptyText);
        }

        $dates = self::sortedDateKeys($clean);

        $max = $valueMode === 'percent'
            ? 100
            : $fixedMax;

        return self::htmlChart($dates, $clean, $max, $valueMode, true);
    }

    /**
     * One vertical bar chart per series (e.g. each physical component).
     *
     * @param  list<array{label?: string, unit?: string|null, points?: list<array{date?: string, percent?: float|int, value?: float|int}>}>  $series
     */
    public static function splitSeriesCharts(array $series, string $valueMode = 'value', string $emptyText = 'Belum ada data.'): string
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
                ksort($pts);
                $label = (string) ($s['label'] ?? 'Komponen');
                $unit = $s['unit'] ?? null;
                if ($unit && $valueMode !== 'percent') {
                    $label .= ' ('.$unit.')';
                }
                $clean[] = [
                    'label' => $label,
                    'color' => self::PALETTE[$idx % count(self::PALETTE)],
                    'values' => $pts,
                ];
            }
        }

        if ($clean === []) {
            return self::emptyBox($emptyText);
        }

        $html = '';
        foreach ($clean as $s) {
            ksort($s['values']);
            $dates = array_keys($s['values']);
            $max = $valueMode === 'percent'
                ? 100
                : max(1, max($s['values']));

            $html .= '<div class="chart-split-block">'
                .'<h3 class="chart-split-title">'.e($s['label']).'</h3>'
                .self::htmlChart($dates, [['label' => $s['label'], 'color' => $s['color'], 'values' => $s['values']]], $max, $valueMode)
                .'</div>';
        }

        return $html;
    }

    /**
     * @param  list<string>  $dates
     * @param  list<array{label: string, color: string, values: array<string, float>}>  $series
     */
    private static function htmlChart(array $dates, array $series, float $max, string $valueMode, bool $showLegend = false): string
    {
        $dateCells = '';

        foreach ($dates as $date) {
            $bars = '';
            foreach ($series as $s) {
                $val = $s['values'][$date] ?? null;
                if ($val === null) {
                    $bars .= '<td class="chart-bar-slot"><div class="chart-bar-empty-slot"></div></td>';
                    continue;
                }

                $height = self::barHeightPx($val, $max);
                $display = $valueMode === 'percent'
                    ? rtrim(rtrim(number_format($val, 1), '0'), '.').'%'
                    : rtrim(rtrim(number_format($val, 1), '0'), '.');

                $bars .= '<td class="chart-bar-slot">'
                    .'<div class="chart-bar-value">'.e($display).'</div>'
                    .'<div class="chart-bar-track">'
                    .'<div class="chart-bar-fill" style="height:'.$height.'px;background:'.e($s['color']).';"></div>'
                    .'</div>'
                    .'</td>';
            }

            $dateCells .= '<td class="chart-date-col" align="center">'
                .'<table class="chart-bar-group" cellpadding="0" cellspacing="3" align="center"><tr valign="bottom">'.$bars.'</tr></table>'
                .'<div class="chart-date-label">'.e(self::fmtDate($date)).'</div>'
                .'</td>';
        }

        $legend = '';
        if ($showLegend) {
            foreach ($series as $s) {
                $legend .= '<td class="legend-cell">'
                    .'<span class="legend-dot" style="background:'.e($s['color']).';"></span>'
                    .'<span class="legend-label">'.e($s['label']).'</span>'
                    .'</td>';
            }
        }

        $yMax = $valueMode === 'percent' ? '100%' : (string) round($max);
        $yMid = $valueMode === 'percent' ? '50%' : (string) round($max / 2);

        $legendHtml = $legend !== ''
            ? '<table class="chart-legend" cellpadding="0" cellspacing="0"><tr>'.$legend.'</tr></table>'
            : '';

        return <<<HTML
<div class="chart-panel">
  <table class="chart-axis-table" cellpadding="0" cellspacing="0">
    <tr>
      <td class="chart-axis-labels">
        <div>{$yMax}</div>
        <div>{$yMid}</div>
        <div>0</div>
      </td>
      <td class="chart-axis-body">
        <table class="chart-dates-row" width="100%" cellpadding="0" cellspacing="0">
          <tr valign="bottom">
            {$dateCells}
          </tr>
        </table>
      </td>
    </tr>
  </table>
  {$legendHtml}
</div>
HTML;
    }

    private static function barHeightPx(float $value, float $max): int
    {
        if ($max <= 0) {
            return 0;
        }

        return max(4, (int) round(($value / $max) * self::BAR_AREA_HEIGHT));
    }

    private static function emptyBox(string $text): string
    {
        return '<div class="chart-empty">'.e($text).'</div>';
    }

    private static function sortedDateKeys(array $series): array
    {
        $dates = [];
        foreach ($series as $s) {
            foreach (array_keys($s['values'] ?? []) as $date) {
                $dates[$date] = true;
            }
        }
        $list = array_keys($dates);
        sort($list, SORT_STRING);

        return $list;
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
