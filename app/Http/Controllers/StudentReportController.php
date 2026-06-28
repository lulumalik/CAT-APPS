<?php

namespace App\Http\Controllers;

use App\Models\StudentReport;
use App\Models\User;
use App\Services\AutoStudentReportService;
use App\Services\WeeklyStudentReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StudentReportController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentReport::query()
            ->with(['student:id,name', 'creator:id,name', 'bimbleClass:id,name']);

        if ($studentId = $request->input('student_id')) {
            $query->where('student_user_id', $studentId);
            $student = User::find($studentId);
            if ($student) {
                app(WeeklyStudentReportService::class)->syncMissingWeeks($student);
            }
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

        app(WeeklyStudentReportService::class)->syncForDate(
            User::findOrFail($data['student_user_id']),
            $report->report_date,
            $request->user()->id,
            true,
        );

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

        $report = app(WeeklyStudentReportService::class)->syncForWeek(
            $student,
            $start,
            $request->user()->id,
            true,
        );

        if (! $report) {
            return response()->json([
                'message' => 'Belum ada laporan harian pada minggu ini.',
            ], 422);
        }

        $report->load(['student:id,name', 'creator:id,name']);

        return response()->json($this->serialize($report), 201);
    }

    public function destroy(StudentReport $report)
    {
        $report->delete();

        return response()->json(['success' => true]);
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
