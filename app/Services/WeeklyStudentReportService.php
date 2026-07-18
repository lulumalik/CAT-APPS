<?php

namespace App\Services;

use App\Models\StudentReport;
use App\Models\TestSubmission;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class WeeklyStudentReportService
{
    /**
     * Create or refresh the weekly summary for the week containing $start.
     * Returns null when the week has no daily reports.
     */
    public function syncForWeek(User $student, Carbon $start, ?int $createdBy = null, bool $notify = false): ?StudentReport
    {
        if (! Schema::hasTable('student_reports')) {
            return null;
        }

        $start = $this->weekStart($start);
        $end = $this->weekEnd($start);

        $dailies = StudentReport::query()
            ->where('student_user_id', $student->id)
            ->where('type', StudentReport::TYPE_DAILY)
            ->whereDate('report_date', '>=', $start->toDateString())
            ->whereDate('report_date', '<=', $end->toDateString())
            ->orderBy('report_date')
            ->get();

        if ($dailies->isEmpty()) {
            return null;
        }

        $categories = $this->buildCategoryNarratives($dailies);
        $metrics = $this->weeklyMetrics($student, $start, $end, $dailies->count());

        $weekly = StudentReport::query()
            ->where('student_user_id', $student->id)
            ->where('type', StudentReport::TYPE_WEEKLY)
            ->whereDate('period_start', $start->toDateString())
            ->first();

        $payload = [
            'report_date' => $end->toDateString(),
            'period_start' => $start->toDateString(),
            'period_end' => $end->toDateString(),
            'title' => sprintf('Ringkasan Mingguan %s – %s', $start->translatedFormat('d M'), $end->translatedFormat('d M Y')),
            'summary' => $this->weeklySummaryText($dailies->count(), $metrics),
            'categories' => $categories,
            'metrics' => array_merge($metrics, ['auto_source' => 'weekly_sync']),
        ];

        if ($weekly) {
            $weekly->update($payload);

            return $weekly->fresh();
        }

        $weekly = StudentReport::create(array_merge($payload, [
            'student_user_id' => $student->id,
            'created_by' => $createdBy,
            'type' => StudentReport::TYPE_WEEKLY,
        ]));

        if ($notify) {
            app(AutoStudentReportService::class)->notify($weekly, 'Ringkasan mingguan tersedia');
        }

        return $weekly;
    }

    public function syncForDate(User $student, Carbon|string|null $date, ?int $createdBy = null, bool $notify = false): ?StudentReport
    {
        $anchor = $date
            ? Carbon::parse($date)->timezone($this->timezone())
            : Carbon::now($this->timezone());

        return $this->syncForWeek($student, $anchor, $createdBy, $notify);
    }

    /**
     * Backfill weekly summaries for weeks that already have daily reports.
     */
    public function syncMissingWeeks(User $student, ?int $createdBy = null): void
    {
        if (! Schema::hasTable('student_reports')) {
            return;
        }

        $weekStarts = StudentReport::query()
            ->where('student_user_id', $student->id)
            ->where('type', StudentReport::TYPE_DAILY)
            ->pluck('report_date')
            ->map(fn ($date) => $this->weekStart($date)->toDateString())
            ->unique()
            ->values();

        if ($weekStarts->isEmpty()) {
            return;
        }

        $existingWeekStarts = StudentReport::query()
            ->where('student_user_id', $student->id)
            ->where('type', StudentReport::TYPE_WEEKLY)
            ->pluck('period_start')
            ->map(fn ($date) => Carbon::parse($date)->toDateString());

        foreach ($weekStarts as $weekStart) {
            if ($existingWeekStarts->contains($weekStart)) {
                continue;
            }

            $this->syncForWeek($student, Carbon::parse($weekStart)->timezone($this->timezone()), $createdBy, true);
        }
    }

    /**
     * @param  Collection<int, StudentReport>  $dailies
     * @return array<string, string>
     */
    private function buildCategoryNarratives(Collection $dailies): array
    {
        $narratives = [];

        $akademik = $this->buildAkademikNarrative($dailies);
        if ($akademik !== '') {
            $narratives['akademik'] = $akademik;
        }

        foreach ($this->otherCategoryNotes($dailies) as $key => $text) {
            if (! isset($narratives[$key])) {
                $narratives[$key] = $text;
            }
        }

        return $narratives;
    }

    /**
     * @param  Collection<int, StudentReport>  $dailies
     */
    private function buildAkademikNarrative(Collection $dailies): string
    {
        $pointsBySubject = $this->collectAkademikPoints($dailies);
        if ($pointsBySubject === []) {
            return '';
        }

        $paragraphs = [];
        foreach ($pointsBySubject as $subject => $entries) {
            $paragraph = $this->buildMeasurementNarrative(
                $subject,
                $entries,
                '',
                'desc',
            );
            if ($paragraph !== '') {
                $paragraphs[] = $paragraph;
            }
        }

        return implode("\n\n", $paragraphs);
    }

    /**
     * @param  Collection<int, StudentReport>  $dailies
     * @return array<string, list<array{date:string,value:float}>>
     */
    private function collectAkademikPoints(Collection $dailies): array
    {
        $bySubject = [];

        foreach ($dailies->sortBy(fn (StudentReport $daily) => [$daily->report_date?->toDateString(), $daily->id]) as $daily) {
            $metrics = $daily->metrics ?? [];
            $date = Carbon::parse($daily->report_date)->timezone($this->timezone())->toDateString();

            if (in_array($metrics['auto_source'] ?? '', ['test_submission', 'exam_submission'], true)) {
                $scaled = $metrics['scaled_score'] ?? $metrics['percent'] ?? null;
                if (! is_numeric($scaled)) {
                    continue;
                }

                $subject = (string) ($metrics['subject_label'] ?? $metrics['test_name'] ?? $metrics['exam_name'] ?? 'Akademik');
                $dedupeKey = (string) ($metrics['submission_id'] ?? "{$date}:{$scaled}");
                $bySubject[$subject]['entries'][$dedupeKey] = [
                    'date' => $date,
                    'value' => (float) $scaled,
                ];

                continue;
            }

            $note = trim((string) (($daily->categories ?? [])['akademik'] ?? ''));
            if ($note === '') {
                continue;
            }

            $parsed = $this->parseAkademikCategory($note);
            if (! $parsed) {
                continue;
            }

            $dedupeKey = "{$date}:{$parsed['value']}";
            $bySubject[$parsed['label']]['entries'][$dedupeKey] = [
                'date' => $date,
                'value' => $parsed['value'],
            ];
        }

        $normalized = [];
        foreach ($bySubject as $subject => $group) {
            $entries = array_values($group['entries'] ?? []);
            if ($entries !== []) {
                $normalized[$subject] = $entries;
            }
        }

        return $normalized;
    }

    /**
     * @param  list<array{date:string,value:float,label?:string,unit?:string|null}>  $entries
     */
    private function buildMeasurementNarrative(
        string $label,
        array $entries,
        string $unit,
        string $sort,
        bool $isPercent = false,
    ): string {
        if ($entries === []) {
            return '';
        }

        usort($entries, fn (array $a, array $b) => strcmp($a['date'], $b['date']));

        $values = array_column($entries, 'value');
        $min = min($values);
        $max = max($values);
        $first = $entries[0]['value'];
        $last = $entries[array_key_last($entries)]['value'];
        $lowerIsBetter = $sort === 'asc';

        $best = $lowerIsBetter ? $min : $max;
        $worst = $lowerIsBetter ? $max : $min;
        $unitSuffix = $this->formatUnitSuffix($unit, $isPercent);

        if ($lowerIsBetter) {
            $rangeSentence = sprintf(
                '%s: waktu terbaik %s%s dan waktu terlama %s%s selama pekan ini.',
                $label,
                $this->formatScore($best),
                $unitSuffix,
                $this->formatScore($worst),
                $unitSuffix,
            );
        } else {
            $rangeSentence = sprintf(
                '%s: nilai terendah %s%s dan terbaik %s%s selama pekan ini.',
                $label,
                $this->formatScore($worst),
                $unitSuffix,
                $this->formatScore($best),
                $unitSuffix,
            );
        }

        if (count($entries) === 1) {
            return $rangeSentence.' Hanya ada satu pencatatan pekan ini, sehingga tren kenaikan/penurunan belum bisa dinilai.';
        }

        return $rangeSentence.' '.$this->assessTrend($first, $last, $lowerIsBetter, $unit, $isPercent);
    }

    private function assessTrend(
        float $first,
        float $last,
        bool $lowerIsBetter,
        string $unit,
        bool $isPercent = false,
    ): string {
        $unitSuffix = $this->formatUnitSuffix($unit, $isPercent);
        $firstText = $this->formatScore($first).$unitSuffix;
        $lastText = $this->formatScore($last).$unitSuffix;

        if (abs($first - $last) < 0.001) {
            return sprintf(
                'Dibanding awal pekan (%s) hingga akhir pekan (%s), capaian stagnan — belum terlihat progress signifikan.',
                $firstText,
                $lastText,
            );
        }

        $improved = $lowerIsBetter ? ($last < $first) : ($last > $first);

        if ($improved) {
            return sprintf(
                'Dibanding awal pekan (%s) ke akhir pekan (%s), capaian menunjukkan peningkatan — ini termasuk progress positif.',
                $firstText,
                $lastText,
            );
        }

        return sprintf(
            'Dibanding awal pekan (%s) ke akhir pekan (%s), capaian menurun — perlu perhatian dan latihan lebih lanjut.',
            $firstText,
            $lastText,
        );
    }

    /**
     * @param  Collection<int, StudentReport>  $dailies
     * @return array<string, string>
     */
    private function otherCategoryNotes(Collection $dailies): array
    {
        $notes = [];

        foreach ($dailies as $daily) {
            foreach (($daily->categories ?? []) as $key => $note) {
                if ($key === 'akademik') {
                    continue;
                }

                $text = trim((string) $note);
                if ($text === '') {
                    continue;
                }

                $notes[$key] = isset($notes[$key])
                    ? $notes[$key].' '.$text
                    : $text;
            }
        }

        return $notes;
    }

    /**
     * @return array{label:string,value:float}|null
     */
    private function parseAkademikCategory(string $note): ?array
    {
        if (preg_match('/^(.+?)\s*[—–-]\s*.+?:\s*([\d.,]+)\s*$/u', trim($note), $matches)) {
            return [
                'label' => trim($matches[1]),
                'value' => (float) str_replace(',', '.', $matches[2]),
            ];
        }

        if (preg_match('/^(.+?)\s*[—–-]\s*([\d.,]+)\s*$/u', trim($note), $matches)) {
            return [
                'label' => trim($matches[1]),
                'value' => (float) str_replace(',', '.', $matches[2]),
            ];
        }

        if (preg_match('/^(.+?)\s*[—–-]\s*([\d.,]+)\s*%/u', trim($note), $matches)) {
            return [
                'label' => trim($matches[1]),
                'value' => (float) str_replace(',', '.', $matches[2]),
            ];
        }

        if (preg_match('/([\d.,]+)\s*%/u', $note, $matches)) {
            return [
                'label' => 'Akademik',
                'value' => (float) str_replace(',', '.', $matches[1]),
            ];
        }

        return null;
    }

    private function formatScore(float $value): string
    {
        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
    }

    private function formatUnitSuffix(string $unit, bool $isPercent = false): string
    {
        if ($isPercent || $unit === '%') {
            return '%';
        }

        return $unit !== '' ? " {$unit}" : '';
    }

    /**
     * @return array<string, mixed>
     */
    private function weeklyMetrics(User $student, Carbon $start, Carbon $end, int $dailyCount): array
    {
        $tesAvg = null;
        if (Schema::hasTable('test_submissions')) {
            $subs = TestSubmission::query()
                ->where('user_id', $student->id)
                ->whereNotNull('score')
                ->whereBetween('submitted_at', [$start, $end])
                ->with('testDefinition:id,question_ids')
                ->get();

            $scaledScores = [];
            foreach ($subs as $sub) {
                $total = count($sub->testDefinition?->question_ids ?? []);
                if ($total > 0) {
                    $scaledScores[] = (int) round(((float) $sub->score / $total) * 100);
                }
            }
            if ($scaledScores !== []) {
                $tesAvg = (int) round(array_sum($scaledScores) / count($scaledScores));
            }
        }

        return [
            'daily_count' => $dailyCount,
            'tes_rata' => $tesAvg,
        ];
    }

    /**
     * @param  array<string, mixed>  $metrics
     */
    private function weeklySummaryText(int $dailyCount, array $metrics): string
    {
        $parts = [sprintf('%d laporan harian tercatat pekan ini.', $dailyCount)];
        if ($metrics['tes_rata'] !== null) {
            $parts[] = sprintf('Rata-rata nilai tes pekan ini %d.', (int) $metrics['tes_rata']);
        }

        return implode(' ', $parts);
    }

    private function timezone(): string
    {
        return 'Asia/Jakarta';
    }

    private function weekStart(Carbon|string $date): Carbon
    {
        return Carbon::parse($date)->timezone($this->timezone())->startOfWeek();
    }

    private function weekEnd(Carbon $start): Carbon
    {
        return $start->copy()->endOfWeek();
    }
}
