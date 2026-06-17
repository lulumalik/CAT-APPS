<?php

namespace App\Http\Controllers;

use App\Models\StudentGuardian;
use App\Models\User;
use App\Services\StudentDashboardReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class StudentDashboardPdfController extends Controller
{
    public function __construct(
        private StudentDashboardReportService $reportService,
    ) {}

    public function downloadForStudent(Request $request, User $student)
    {
        if ($student->role !== 'user') {
            return response()->json(['message' => 'Akun ini bukan peserta.'], 422);
        }

        $this->authorizeParentOrStaff($request, $student);

        return $this->streamPdf($student, $this->resolveReportDateRange($request));
    }

    private function authorizeParentOrStaff(Request $request, User $student): void
    {
        $user = $request->user();

        if ($user->id === $student->id) {
            abort(403, 'Hanya orang tua yang dapat mengunduh laporan perkembangan.');
        }

        if (in_array($user->role, ['admin', 'mentor'], true)) {
            return;
        }

        if ($user->role === 'parent' && Schema::hasTable('student_guardians')) {
            $linked = StudentGuardian::where('guardian_user_id', $user->id)
                ->where('student_user_id', $student->id)
                ->where('invite_status', StudentGuardian::STATUS_ACCEPTED)
                ->exists();
            if ($linked) {
                return;
            }
        }

        abort(403, 'Tidak punya akses ke laporan peserta ini.');
    }

    /**
     * @return array{from_date:string,to_date:string}
     */
    private function resolveReportDateRange(Request $request): array
    {
        $validated = $request->validate([
            'from_date' => 'nullable|date_format:Y-m-d',
            'to_date' => 'nullable|date_format:Y-m-d',
            'date' => 'nullable|date_format:Y-m-d',
        ]);

        $fromDate = $validated['from_date'] ?? $validated['date'] ?? null;
        $toDate = $validated['to_date'] ?? $validated['date'] ?? null;

        $today = now('Asia/Jakarta')->toDateString();
        $fromDate = $fromDate ?: $today;
        $toDate = $toDate ?: $fromDate;

        $from = Carbon::createFromFormat('Y-m-d', $fromDate, 'Asia/Jakarta')->startOfDay();
        $to = Carbon::createFromFormat('Y-m-d', $toDate, 'Asia/Jakarta')->startOfDay();

        if ($to->lt($from)) {
            abort(422, 'Tanggal To tidak boleh lebih kecil dari From.');
        }

        if ($from->diffInDays($to) > 13) {
            abort(422, 'Rentang tanggal maksimal 14 hari.');
        }

        return [
            'from_date' => $from->toDateString(),
            'to_date' => $to->toDateString(),
        ];
    }

    /**
     * @param  array{from_date:string,to_date:string}  $dateRange
     */
    private function streamPdf(User $student, array $dateRange)
    {
        try {
            $data = $this->reportService->build($student, $dateRange['from_date'], $dateRange['to_date']);
            $programLabel = $this->programLabel($student->program_category);

            $pdf = Pdf::loadView('pdf.student-dashboard', [
                'data' => $data,
                'programLabel' => $programLabel,
            ])->setPaper('a4', 'portrait');

            $fromDate = $data['daily_range_start'] ?? now('Asia/Jakarta')->format('Y-m-d');
            $toDate = $data['daily_range_end'] ?? $fromDate;
            $dateLabel = $fromDate === $toDate ? $fromDate : $fromDate.'-to-'.$toDate;
            $filename = 'Laporan-Perkembangan-'.Str::slug($student->name ?: 'peserta').'-'.$dateLabel.'.pdf';

            return response()->streamDownload(
                static function () use ($pdf) {
                    echo $pdf->output();
                },
                $filename,
                ['Content-Type' => 'application/pdf']
            );
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Gagal membuat PDF. Silakan coba lagi.',
            ], 500);
        }
    }

    private function programLabel(?string $program): string
    {
        $normalized = User::normalizeProgramCategory($program);

        return match ($normalized) {
            User::PROGRAM_VIP => 'VIP',
            User::PROGRAM_BIMBINGAN_ONLINE => 'Bimbingan Online',
            User::PROGRAM_TRY_OUT => 'Try Out',
            default => 'Regular',
        };
    }
}
