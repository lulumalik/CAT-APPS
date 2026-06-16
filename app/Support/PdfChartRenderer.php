<?php

namespace App\Support;

class PdfChartRenderer
{
    private const PALETTE = ['#2F6BFF', '#9DB359', '#E8833A', '#8B5CF6', '#EC4899', '#14B8A6', '#F59E0B', '#0EA5E9'];

    /**
     * @param  list<array{percent?: float|int, date?: string}>  $data
     */
    public static function lineChart(array $data, string $color = '#2F6BFF', string $emptyText = 'Belum ada data.'): string
    {
        $points = [];
        foreach ($data as $row) {
            if (! isset($row['percent'])) {
                continue;
            }
            $date = (string) ($row['date'] ?? '');
            if ($date === '') {
                continue;
            }
            $points[$date] = (float) $row['percent'];
        }

        if ($points === []) {
            return self::emptyBox($emptyText);
        }

        ksort($points);
        $dates = array_keys($points);

        return self::htmlChart(
            $dates,
            [[
                'label' => 'Nilai Tes (%)',
                'color' => $color,
                'values' => $points,
            ]],
            100,
            'percent',
        );
    }

    /**
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

        $dates = [];
        foreach ($clean as $s) {
            foreach (array_keys($s['values']) as $date) {
                $dates[$date] = true;
            }
        }
        $dates = array_keys($dates);
        sort($dates);

        $max = $valueMode === 'percent'
            ? 100
            : max(1, ...array_map(fn ($s) => max($s['values']), $clean));

        return self::htmlChart($dates, $clean, $max, $valueMode);
    }

    /**
     * @param  list<string>  $dates
     * @param  list<array{label: string, color: string, values: array<string, float>}>  $series
     */
    private static function htmlChart(array $dates, array $series, float $max, string $valueMode): string
    {
        $headerCells = '';
        foreach ($dates as $date) {
            $headerCells .= '<th class="chart-th">'.e(self::fmtDate($date)).'</th>';
        }

        $rows = '';
        foreach ($series as $s) {
            $cells = '';
            $prev = null;
            foreach ($dates as $date) {
                $val = $s['values'][$date] ?? null;
                $trend = '';
                if ($val !== null && $prev !== null) {
                    if ($val > $prev) {
                        $trend = '<div class="chart-trend up">▲</div>';
                    } elseif ($val < $prev) {
                        $trend = '<div class="chart-trend down">▼</div>';
                    } else {
                        $trend = '<div class="chart-trend flat">■</div>';
                    }
                }
                if ($val !== null) {
                    $prev = $val;
                }

                $display = $val === null
                    ? '—'
                    : ($valueMode === 'percent' ? rtrim(rtrim(number_format($val, 1), '0'), '.').'%' : rtrim(rtrim(number_format($val, 1), '0'), '.'));
                $width = $val === null ? 0 : self::barWidth($val, $max);

                $cells .= '<td class="chart-td">'
                    .'<div class="chart-value">'.e($display).'</div>'
                    .($val !== null
                        ? '<div class="chart-bar-track"><div class="chart-bar-fill" style="width:'.$width.'%;background:'.e($s['color']).';"></div></div>'
                        : '<div class="chart-bar-track chart-bar-empty"></div>')
                    .$trend
                    .'</td>';
            }

            $rows .= '<tr>'
                .'<td class="chart-series-label"><span class="chart-dot" style="background:'.e($s['color']).';"></span>'.e($s['label']).'</td>'
                .$cells
                .'</tr>';
        }

        $yMax = $valueMode === 'percent' ? '100%' : (string) round($max);
        $yMid = $valueMode === 'percent' ? '50%' : (string) round($max / 2);

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
        <table class="chart-data-table" width="100%" cellpadding="0" cellspacing="0">
          <thead>
            <tr>
              <th class="chart-th chart-th-left"></th>
              {$headerCells}
            </tr>
          </thead>
          <tbody>
            {$rows}
          </tbody>
        </table>
      </td>
    </tr>
  </table>
</div>
HTML;
    }

    private static function barWidth(float $value, float $max): float
    {
        if ($max <= 0) {
            return 0;
        }

        return min(100, round(($value / $max) * 100, 1));
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
