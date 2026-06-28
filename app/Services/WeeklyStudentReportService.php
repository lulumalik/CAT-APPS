<?php

namespace App\Services;

use App\Models\RegistrationProgress;
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

        $categories = $this->mergeCategories($dailies);
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
    private function mergeCategories(Collection $dailies): array
    {
        $merged = [];
        foreach ($dailies as $daily) {
            foreach (($daily->categories ?? []) as $key => $note) {
                if (! $note) {
                    continue;
                }
                $merged[$key] = isset($merged[$key])
                    ? $merged[$key]."\n• ".$note
                    : '• '.$note;
            }
        }

        return $merged;
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

            $percents = [];
            foreach ($subs as $sub) {
                $total = count($sub->testDefinition?->question_ids ?? []);
                if ($total > 0) {
                    $percents[] = round(((float) $sub->score / $total) * 100, 1);
                }
            }
            if ($percents !== []) {
                $tesAvg = round(array_sum($percents) / count($percents), 1);
            }
        }

        $jasmaniFilled = 0;
        if (Schema::hasTable('registration_progress')) {
            $progress = RegistrationProgress::where('user_id', $student->id)->first();
            $jasmaniFilled = collect($progress?->physical_data ?? [])
                ->filter(fn ($v) => is_numeric($v) || (is_array($v) && isset($v['value'])))
                ->count();
        }

        return [
            'daily_count' => $dailyCount,
            'tes_rata' => $tesAvg,
            'jasmani_terisi' => $jasmaniFilled,
        ];
    }

    /**
     * @param  array<string, mixed>  $metrics
     */
    private function weeklySummaryText(int $dailyCount, array $metrics): string
    {
        $parts = [sprintf('%d laporan harian tercatat pekan ini.', $dailyCount)];
        if ($metrics['tes_rata'] !== null) {
            $parts[] = sprintf('Rata-rata nilai tes pekan ini %s%%.', $metrics['tes_rata']);
        }
        if (($metrics['jasmani_terisi'] ?? 0) > 0) {
            $parts[] = sprintf('%d komponen jasmani sudah terisi.', $metrics['jasmani_terisi']);
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
