<?php

namespace App\Http\Controllers;

use App\Models\RegistrationProgress;
use App\Models\StudentReport;
use App\Models\TestSubmission;
use App\Models\User;
use App\Services\AutoStudentReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class StudentReportController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentReport::query()
            ->with(['student:id,name', 'creator:id,name', 'bimbleClass:id,name']);

        if ($studentId = $request->input('student_id')) {
            $query->where('student_user_id', $studentId);
        }
        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        $items = $query->orderByDesc('report_date')
            ->orderByDesc('id')
            ->limit(200)
            ->get()
            ->map(fn ($r) => $this->serialize($r));

        return response()->json(['items' => $items]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_user_id' => 'required|exists:users,id',
            'bimble_class_id' => 'nullable|exists:bimble_classes,id',
            'report_date' => 'nullable|date',
            'title' => 'required|string|max:160',
            'summary' => 'nullable|string|max:5000',
            'categories' => 'nullable|array',
            'categories.*' => 'nullable|string|max:2000',
            'metrics' => 'nullable|array',
        ]);

        $report = StudentReport::create([
            'student_user_id' => $data['student_user_id'],
            'bimble_class_id' => $data['bimble_class_id'] ?? null,
            'created_by' => $request->user()->id,
            'type' => StudentReport::TYPE_DAILY,
            'report_date' => $data['report_date'] ?? now()->toDateString(),
            'title' => $data['title'],
            'summary' => $data['summary'] ?? null,
            'categories' => $data['categories'] ?? null,
            'metrics' => $data['metrics'] ?? null,
        ]);

        app(AutoStudentReportService::class)->notify($report, 'Laporan harian baru');

        $report->load(['student:id,name', 'creator:id,name', 'bimbleClass:id,name']);

        return response()->json($this->serialize($report), 201);
    }

    /**
     * Build a weekly summary from the daily reports + progress snapshot within a period.
     */
    public function generateWeekly(Request $request)
    {
        $data = $request->validate([
            'student_user_id' => 'required|exists:users,id',
            'period_start' => 'nullable|date',
        ]);

        $student = User::findOrFail($data['student_user_id']);

        $start = isset($data['period_start'])
            ? Carbon::parse($data['period_start'])->startOfWeek()
            : now()->startOfWeek();
        $end = (clone $start)->endOfWeek();

        $dailies = StudentReport::query()
            ->where('student_user_id', $student->id)
            ->where('type', StudentReport::TYPE_DAILY)
            ->whereBetween('report_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('report_date')
            ->get();

        $categories = $this->mergeCategories($dailies);
        $metrics = $this->weeklyMetrics($student, $start, $end, $dailies->count());

        $report = StudentReport::create([
            'student_user_id' => $student->id,
            'created_by' => $request->user()->id,
            'type' => StudentReport::TYPE_WEEKLY,
            'report_date' => $end->toDateString(),
            'period_start' => $start->toDateString(),
            'period_end' => $end->toDateString(),
            'title' => sprintf('Ringkasan Mingguan %s – %s', $start->translatedFormat('d M'), $end->translatedFormat('d M Y')),
            'summary' => $this->weeklySummaryText($dailies->count(), $metrics),
            'categories' => $categories,
            'metrics' => $metrics,
        ]);

        app(AutoStudentReportService::class)->notify($report, 'Ringkasan mingguan tersedia');

        $report->load(['student:id,name', 'creator:id,name']);

        return response()->json($this->serialize($report), 201);
    }

    public function destroy(StudentReport $report)
    {
        $report->delete();

        return response()->json(['success' => true]);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, StudentReport>  $dailies
     * @return array<string, string>
     */
    private function mergeCategories($dailies): array
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

    private function serialize(StudentReport $report): array
    {
        return [
            'id' => $report->id,
            'type' => $report->type,
            'title' => $report->title,
            'summary' => $report->summary,
            'categories' => $report->categories ?? [],
            'metrics' => $report->metrics ?? [],
            'report_date' => $report->report_date?->toDateString(),
            'period_start' => $report->period_start?->toDateString(),
            'period_end' => $report->period_end?->toDateString(),
            'student' => $report->student ? ['id' => $report->student->id, 'name' => $report->student->name] : null,
            'class' => $report->bimbleClass ? ['id' => $report->bimbleClass->id, 'name' => $report->bimbleClass->name] : null,
            'created_by' => $report->creator?->name,
        ];
    }
}
