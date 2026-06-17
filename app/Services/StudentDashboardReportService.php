<?php

namespace App\Services;

use App\Http\Controllers\ProgressController;
use App\Models\StudentReport;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class StudentDashboardReportService
{
    /**
     * @return array<string, mixed>
     */
    public function build(User $student, ?string $fromDate = null, ?string $toDate = null): array
    {
        $progress = app(ProgressController::class)->progressDataForStudent($student);
        $startDate = $fromDate ?: now('Asia/Jakarta')->toDateString();
        $endDate = $toDate ?: $startDate;

        $dailyReports = [];
        $weeklyReports = [];

        if (Schema::hasTable('student_reports')) {
            $dailyReports = StudentReport::query()
                ->where('student_user_id', $student->id)
                ->where('type', StudentReport::TYPE_DAILY)
                ->whereBetween('report_date', [$startDate, $endDate])
                ->with(['creator:id,name', 'bimbleClass:id,name'])
                ->orderByDesc('report_date')
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->limit(50)
                ->get()
                ->map(fn (StudentReport $r) => $this->serializeReport($r))
                ->all();

            $weeklyReports = StudentReport::query()
                ->where('student_user_id', $student->id)
                ->where('type', StudentReport::TYPE_WEEKLY)
                ->with(['creator:id,name', 'bimbleClass:id,name'])
                ->orderByDesc('report_date')
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->limit(20)
                ->get()
                ->map(fn (StudentReport $r) => $this->serializeReport($r))
                ->all();
        }

        return [
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'username' => $student->username,
                'email' => $student->email,
                'program_category' => $student->program_category,
            ],
            'generated_at' => Carbon::now('Asia/Jakarta')->format('d M Y H:i'),
            'daily_range_start' => $startDate,
            'daily_range_end' => $endDate,
            'progress' => $progress,
            'daily_reports' => $dailyReports,
            'weekly_reports' => $weeklyReports,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeReport(StudentReport $report): array
    {
        return [
            'title' => $report->title,
            'summary' => $report->summary,
            'categories' => $report->categories ?? [],
            'report_date' => $report->report_date
                ? Carbon::parse($report->report_date)->timezone('Asia/Jakarta')->format('d M Y')
                : null,
            'created_at' => $report->created_at?->timezone('Asia/Jakarta')->format('d M Y H:i'),
            'class_name' => $report->bimbleClass?->name,
            'created_by' => $report->creator?->name,
        ];
    }
}
