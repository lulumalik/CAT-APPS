<?php

namespace App\Services;

use App\Http\Controllers\DashboardController;
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
    public function build(User $student, ?string $dailyDate = null): array
    {
        $overview = app(DashboardController::class)->studentOverviewData($student->id);
        $progress = app(ProgressController::class)->progressDataForStudent($student);
        $date = $dailyDate ?: now('Asia/Jakarta')->toDateString();

        $dailyReports = [];
        $weeklyReports = [];

        if (Schema::hasTable('student_reports')) {
            $dailyReports = StudentReport::query()
                ->where('student_user_id', $student->id)
                ->where('type', StudentReport::TYPE_DAILY)
                ->whereDate('report_date', $date)
                ->with(['creator:id,name', 'bimbleClass:id,name'])
                ->orderByDesc('report_date')
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->limit(50)
                ->get()
                ->map(fn ($r) => $this->serializeReport($r))
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
                ->map(fn ($r) => $this->serializeReport($r))
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
            'daily_date' => $date,
            'overview' => $overview,
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
            'report_date' => $report->report_date?->format('d M Y'),
            'created_at' => $report->created_at?->timezone('Asia/Jakarta')->format('d M Y H:i'),
            'class_name' => $report->bimbleClass?->name,
            'created_by' => $report->creator?->name,
        ];
    }
}
