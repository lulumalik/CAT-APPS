<?php

namespace App\Http\Controllers;

use App\Models\CertificateIssue;
use App\Models\CertificateTemplate;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CertificateController extends Controller
{
    private function supportedPrograms(): array
    {
        return User::canonicalProgramCategories();
    }

    private function defaultTemplate(string $program): array
    {
        return [
            'program_category' => $program,
            'title' => 'Sertifikat Kelulusan',
            'subtitle' => strtoupper(str_replace('_', ' ', $program)),
            'body_text' => 'Dengan ini menyatakan bahwa peserta telah menyelesaikan program dengan baik.',
            'sign_name' => 'Admin Program',
            'sign_position' => 'Penyelenggara',
            'theme_color' => '#1A1A1A',
        ];
    }

    public function templates()
    {
        $items = CertificateTemplate::query()->get()->keyBy('program_category');
        $result = [];
        foreach ($this->supportedPrograms() as $program) {
            $result[] = $items[$program] ?? $this->defaultTemplate($program);
        }

        return response()->json($result);
    }

    public function updateTemplate(Request $request, string $program)
    {
        if (! in_array($program, $this->supportedPrograms(), true)) {
            return response()->json(['message' => 'Program tidak valid.'], 422);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'body_text' => 'nullable|string|max:5000',
            'sign_name' => 'nullable|string|max:255',
            'sign_position' => 'nullable|string|max:255',
            'theme_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $template = CertificateTemplate::updateOrCreate(
            ['program_category' => $program],
            $data
        );

        return response()->json($template);
    }

    public function issue(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $target = User::query()->findOrFail($data['user_id']);
        $program = User::normalizeProgramCategory($target->program_category);

        if (! in_array($program, $this->supportedPrograms(), true)) {
            return response()->json(['message' => 'Program user tidak didukung untuk sertifikat.'], 422);
        }

        $template = CertificateTemplate::query()->where('program_category', $program)->first();
        $snapshot = $template
            ? $template->only(['title', 'subtitle', 'body_text', 'sign_name', 'sign_position', 'theme_color'])
            : collect($this->defaultTemplate($program))->except('program_category')->toArray();

        $issue = CertificateIssue::create([
            'user_id' => $target->id,
            'issued_by' => optional($request->user())->id,
            'program_category' => $program,
            'certificate_number' => sprintf(
                'CERT-%s-%06d',
                Carbon::now()->format('YmdHis'),
                random_int(1, 999999)
            ),
            'template_snapshot' => $snapshot,
            'issued_at' => now(),
        ]);

        UserNotification::create([
            'user_id' => $target->id,
            'type' => 'certificate_issued',
            'title' => 'Sertifikat tersedia',
            'message' => 'Sertifikat Anda sudah diterbitkan admin. Silakan unduh dari notifikasi ini.',
            'payload' => [
                'certificate_issue_id' => $issue->id,
                'download_url' => "/api/certificates/{$issue->id}/download",
                'certificate_number' => $issue->certificate_number,
                'program_category' => $program,
            ],
        ]);

        return response()->json($issue->load('user:id,name,email,program_category'), 201);
    }

    public function listIssues(Request $request)
    {
        $items = CertificateIssue::query()
            ->with(['user:id,name,email,program_category', 'issuer:id,name'])
            ->orderByDesc('id')
            ->limit(200)
            ->get();

        return response()->json($items);
    }

    public function download(Request $request, CertificateIssue $certificateIssue)
    {
        $user = $request->user();
        if (! $user) {
            abort(403);
        }

        if ($user->role === 'user' && (int) $certificateIssue->user_id !== (int) $user->id) {
            abort(403);
        }

        $certificateIssue->loadMissing('user:id,name,email');
        $tpl = $certificateIssue->template_snapshot ?? [];
        $name = e((string) optional($certificateIssue->user)->name);
        $program = strtoupper(str_replace('_', ' ', (string) $certificateIssue->program_category));
        $issuedAt = optional($certificateIssue->issued_at)->timezone('Asia/Jakarta')->format('d M Y H:i') ?? '-';
        $issuedDateLong = optional($certificateIssue->issued_at)->timezone('Asia/Jakarta')->translatedFormat('d F Y') ?? '-';
        $number = e((string) $certificateIssue->certificate_number);
        $title = e((string) ($tpl['title'] ?? 'Sertifikat'));
        $subtitle = e((string) ($tpl['subtitle'] ?? $program));
        $bodyRaw = (string) ($tpl['body_text'] ?? 'Dengan ini menyatakan bahwa peserta telah menyelesaikan program dengan baik.');
        $bodyRaw = str_replace(
            ['{name}', '{program}', '{date}'],
            [(string) optional($certificateIssue->user)->name, $program, $issuedDateLong],
            $bodyRaw
        );
        $body = nl2br(e($bodyRaw));
        $signName = e((string) ($tpl['sign_name'] ?? 'Admin Program'));
        $signPosition = e((string) ($tpl['sign_position'] ?? 'Penyelenggara'));
        $theme = e((string) ($tpl['theme_color'] ?? '#1A1A1A'));
        $orgName = 'NAMA LEMBAGA PENYELENGGARA KEGIATAN';
        $orgAddress = 'Alamat 123 Anywhere St., Any City, ST 12345';
        $orgContact = 'E-mail hello@reallygreatsite.com | Telepon +123-456-7890';

        $html = <<<HTML
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>{$title}</title>
  <style>
    @page { margin: 14mm; }
    body { font-family: DejaVu Sans, Arial, sans-serif; background: #efefef; }
    .frame { position: relative; max-width: 1080px; margin: 0 auto; background: #fff; border: 3px solid #5b4a46; padding: 8px; }
    .inner { position: relative; border: 2px solid #7b6a67; min-height: 650px; }
    .corner { position: absolute; width: 78px; height: 78px; z-index: 2; }
    .corner.tl { top: -8px; left: -8px; }
    .corner.tr { top: -8px; right: -8px; }
    .corner.bl { bottom: -8px; left: -8px; }
    .corner.br { bottom: -8px; right: -8px; }
    .top { background: #4c3d3a; color: #fff; text-align: center; padding: 22px 20px 18px; }
    .top .org { font-family: DejaVu Serif, serif; font-size: 34px; font-weight: 700; letter-spacing: 0.5px; }
    .top .addr { margin-top: 6px; font-size: 15px; opacity: .95; }
    .top .contact { margin-top: 3px; font-size: 13px; opacity: .9; }
    .content { padding: 34px 42px 24px; color: #403735; text-align: center; }
    .title { font-family: DejaVu Serif, serif; font-size: 54px; font-weight: 700; letter-spacing: 1px; margin: 0; color: {$theme}; }
    .number { margin-top: 4px; font-size: 16px; letter-spacing: 1px; }
    .given { margin-top: 26px; font-size: 18px; }
    .name { margin-top: 10px; font-family: DejaVu Serif, serif; font-size: 58px; font-weight: 700; }
    .line { border-top: 2px solid #6d5f5b; margin: 14px auto 0; width: 88%; }
    .body { margin: 22px auto 0; max-width: 86%; line-height: 1.65; font-size: 17px; }
    .ribbon { margin: 20px auto 0; display: inline-block; background: #e6ca75; color: #5d4a2e; padding: 6px 24px; font-size: 16px; font-weight: 700; border-radius: 4px; }
    .meta { margin-top: 8px; font-size: 13px; color: #6d5f5b; }
    .sign-row { margin-top: 56px; display: table; width: 100%; table-layout: fixed; }
    .sign-col { display: table-cell; width: 50%; text-align: center; vertical-align: top; }
    .sign-title { font-size: 16px; font-weight: 700; }
    .sign-line { margin: 72px auto 10px; width: 70%; border-top: 1px solid #8a7d79; }
    .sign-name { font-size: 20px; font-weight: 700; }
    .sign-role { font-size: 14px; color: #6d5f5b; margin-top: 4px; }
  </style>
</head>
<body>
  <div class="frame">
    <div class="inner">
      <svg class="corner tl" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg">
        <path d="M 0,8 L 38,8 L 38,0 L 50,0 L 50,40 Q 50,50 40,50 L 0,50 Z" fill="#4c3d3a" />
        <path d="M 4,12 L 38,12 L 38,4 L 46,4 L 46,40 Q 46,46 40,46 L 4,46 Z" fill="none" stroke="#c9b58f" stroke-width="0.6" />
      </svg>
      <svg class="corner tr" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg">
        <path d="M 60,8 L 22,8 L 22,0 L 10,0 L 10,40 Q 10,50 20,50 L 60,50 Z" fill="#4c3d3a" />
        <path d="M 56,12 L 22,12 L 22,4 L 14,4 L 14,40 Q 14,46 20,46 L 56,46 Z" fill="none" stroke="#c9b58f" stroke-width="0.6" />
      </svg>
      <svg class="corner bl" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg">
        <path d="M 0,52 L 38,52 L 38,60 L 50,60 L 50,20 Q 50,10 40,10 L 0,10 Z" fill="#4c3d3a" />
        <path d="M 4,48 L 38,48 L 38,56 L 46,56 L 46,20 Q 46,14 40,14 L 4,14 Z" fill="none" stroke="#c9b58f" stroke-width="0.6" />
      </svg>
      <svg class="corner br" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg">
        <path d="M 60,52 L 22,52 L 22,60 L 10,60 L 10,20 Q 10,10 20,10 L 60,10 Z" fill="#4c3d3a" />
        <path d="M 56,48 L 22,48 L 22,56 L 14,56 L 14,20 Q 14,14 20,14 L 56,14 Z" fill="none" stroke="#c9b58f" stroke-width="0.6" />
      </svg>
      <div class="top">
        <div class="org">{$orgName}</div>
        <div class="addr">{$orgAddress}</div>
        <div class="contact">{$orgContact}</div>
      </div>
      <div class="content">
        <h1 class="title">{$title}</h1>
        <div class="number">NOMOR : {$number}</div>
        <div class="given">Diberikan Kepada</div>
        <div class="name">{$name}</div>
        <div class="line"></div>
        <div class="body">{$body}<br/><strong>Program: {$program}</strong></div>
        <div class="ribbon">Jakarta, {$issuedDateLong}</div>
        <div class="meta">Diterbitkan: {$issuedAt}</div>

        <div class="sign-row">
          <div class="sign-col">
            <div class="sign-title">Penanggung Jawab</div>
            <div class="sign-line"></div>
            <div class="sign-name">{$signName}</div>
            <div class="sign-role">({$signPosition})</div>
          </div>
          <div class="sign-col">
            <div class="sign-title">Ketua Panitia</div>
            <div class="sign-line"></div>
            <div class="sign-name">Admin Panitia</div>
            <div class="sign-role">(Jabatan)</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
HTML;

        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $filename = 'sertifikat-'.$certificateIssue->id.'.pdf';
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4', 'landscape');

            return response()->streamDownload(
                static function () use ($pdf) {
                    echo $pdf->output();
                },
                $filename,
                ['Content-Type' => 'application/pdf']
            );
        }

        $filename = 'sertifikat-'.$certificateIssue->id.'.html';
        return response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
