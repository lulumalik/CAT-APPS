<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\StudentDashboardReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudentDashboardPdfController extends Controller
{
    public function __construct(
        private StudentDashboardReportService $reportService,
    ) {}

    public function downloadMine(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'user') {
            abort(403, 'Hanya peserta yang dapat mengunduh laporan dashboard.');
        }

        return $this->streamPdf($user, $request->input('date'));
    }

    public function downloadForStaff(Request $request, User $student)
    {
        if ($request->user()->role !== 'admin') {
            abort(403);
        }

        if ($student->role !== 'user') {
            return response()->json(['message' => 'Akun ini bukan peserta.'], 422);
        }

        return $this->streamPdf($student, $request->input('date'));
    }

    private function streamPdf(User $student, ?string $dailyDate = null)
    {
        $data = $this->reportService->build($student, $dailyDate);
        $programLabel = $this->programLabel($student->program_category);

        $pdf = Pdf::loadView('pdf.student-dashboard', [
            'data' => $data,
            'programLabel' => $programLabel,
        ])->setPaper('a4', 'portrait');

        $dateLabel = now('Asia/Jakarta')->format('Y-m-d');
        $filename = 'Laporan-Dashboard-'.Str::slug($student->name ?: 'peserta').'-'.$dateLabel.'.pdf';

        return response()->streamDownload(
            static function () use ($pdf) {
                echo $pdf->output();
            },
            $filename,
            ['Content-Type' => 'application/pdf']
        );
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
