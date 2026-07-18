<?php

namespace App\Http\Controllers;

use App\Models\RegistrationProgress;
use App\Services\RegistrationFormPdfService;
use App\Support\RegistrationTemplatePath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class RegistrationFormPdfController extends Controller
{
    public function __construct(
        private readonly RegistrationFormPdfService $forms
    ) {}

    public function catalog(Request $request)
    {
        return response()->json([
            'pages' => $this->forms->pageCatalog(),
        ]);
    }

    public function downloadTemplate(Request $request)
    {
        return $this->streamTemplatePdf();
    }

    public function downloadAll(Request $request)
    {
        $user = $request->user();
        $progress = $this->resolveProgress($user->id);

        return $this->streamPdf($user, $progress, null);
    }

    public function downloadPage(Request $request, string $slug)
    {
        if ($this->forms->findPageBySlug($slug) === null) {
            abort(404);
        }

        $user = $request->user();
        $progress = $this->resolveProgress($user->id);

        return $this->streamPdf($user, $progress, $slug);
    }

    private function resolveProgress(int $userId): ?RegistrationProgress
    {
        if (! Schema::hasTable('registration_progress')) {
            return null;
        }

        return RegistrationProgress::where('user_id', $userId)->first();
    }

    private function streamPdf(\App\Models\User $user, ?RegistrationProgress $progress, ?string $slug)
    {
        try {
            $pdf = $this->forms->makePdf($user, $progress, $slug);
            $filename = $this->forms->downloadFilename($user, $slug);

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
                'message' => 'Gagal membuat PDF berkas pendaftaran. Silakan coba lagi.',
            ], 500);
        }
    }

    private function streamTemplatePdf()
    {
        $path = RegistrationTemplatePath::resolve();
        if ($path === null) {
            return response()->json([
                'message' => 'File surat pernyataan orang tua belum tersedia di server.',
            ], 404);
        }

        $filename = (string) config('registration_forms.template_pdf_download_name', 'surat-pernyataan-orang-tua.pdf');

        return response()->download($path, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
