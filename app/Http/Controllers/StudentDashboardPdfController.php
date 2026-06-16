<?php

namespace App\Http\Controllers;

use App\Models\StudentGuardian;
use App\Models\User;
use App\Services\StudentDashboardReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
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

        return $this->streamPdf($student, $this->resolveReportDate($request));
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

    private function resolveReportDate(Request $request): ?string
    {
        $validated = $request->validate([
            'date' => 'nullable|date_format:Y-m-d',
        ]);

        return $validated['date'] ?? null;
    }

    private function streamPdf(User $student, ?string $dailyDate = null)
    {
        try {
            $data = $this->reportService->build($student, $dailyDate);
            $programLabel = $this->programLabel($student->program_category);

            $pdf = Pdf::loadView('pdf.student-dashboard', [
                'data' => $data,
                'programLabel' => $programLabel,
            ])->setPaper('a4', 'portrait');

            $dateLabel = $data['daily_date'] ?? now('Asia/Jakarta')->format('Y-m-d');
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
