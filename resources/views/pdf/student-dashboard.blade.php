<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Perkembangan</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            line-height: 1.45;
            margin: 0;
            padding: 24px;
            background: #ffffff;
        }
        h1 { font-size: 20px; margin: 0 0 4px; color: #1a1a1a; }
        h2 {
            font-size: 13px;
            margin: 0 0 10px;
            color: #1a1a1a;
            font-weight: 700;
            padding-bottom: 6px;
            border-bottom: 2px solid #9db359;
        }
        h3 { font-size: 11px; margin: 0 0 8px; font-weight: 700; color: #374151; }
        .muted { color: #6b7280; font-size: 10px; }
        .header {
            border-bottom: 2px solid #cdd4de;
            padding-bottom: 14px;
            margin-bottom: 18px;
        }
        .header-name {
            font-size: 12px;
            font-weight: 700;
            color: #1a1a1a;
            margin: 10px 0 8px;
            line-height: 1.4;
        }
        .header-badge-row {
            margin-bottom: 8px;
        }
        .header-printed {
            font-size: 10px;
            line-height: 1.4;
        }
        .badge {
            display: inline-block;
            background: #f0f4e8;
            color: #5a6b2e;
            font-size: 9px;
            font-weight: 700;
            border-radius: 10px;
            padding: 4px 12px;
            border: 1px solid #c5d4a0;
            line-height: 1.2;
        }
        .section {
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            padding: 14px;
            margin-bottom: 14px;
            page-break-inside: avoid;
            background: #ffffff;
        }
        .card {
            border: 1px solid #eef0f2;
            border-radius: 8px;
            padding: 10px 12px;
            margin-bottom: 8px;
            background: #fafbfc;
        }
        .card:last-child { margin-bottom: 0; }
        .card-title { font-weight: 700; font-size: 11px; color: #1a1a1a; }
        .tag {
            display: inline-block;
            background: #f3f4f6;
            color: #4b5563;
            font-size: 9px;
            border-radius: 999px;
            padding: 2px 8px;
            margin: 2px 4px 2px 0;
        }
        .weekly {
            border: 1.5px solid #9db359;
            background: #f7faf2;
        }
        .stat-grid { width: 100%; border-collapse: separate; border-spacing: 8px 0; }
        .stat-grid td { width: 33.33%; vertical-align: top; }
        .stat-card {
            border: 1px solid #eef0f2;
            border-radius: 8px;
            padding: 10px;
            background: #fafbfc;
            text-align: center;
        }
        .stat-value { font-size: 16px; font-weight: 700; color: #9db359; }
        .stat-label { font-size: 9px; color: #6b7280; margin-top: 2px; }
        .chart-box { margin: 8px 0 4px; }
        .chart-svg { width: 100%; height: 150px; display: block; }
        .chart-empty {
            text-align: center;
            color: #9ca3af;
            font-size: 10px;
            padding: 28px 12px;
            border: 1px dashed #e5e7eb;
            border-radius: 8px;
            background: #fafbfc;
        }
        .chart-dates {
            display: table;
            width: 100%;
            margin-top: 4px;
            font-size: 9px;
            color: #9ca3af;
        }
        .chart-dates span:first-child { text-align: left; display: table-cell; }
        .chart-dates span:last-child { text-align: right; display: table-cell; }
        .chart-row { display: table; width: 100%; }
        .chart-yaxis {
            display: table-cell;
            width: 28px;
            vertical-align: middle;
            font-size: 8px;
            color: #9ca3af;
            text-align: right;
            padding-right: 6px;
        }
        .chart-yaxis span { display: block; line-height: 1.8; }
        .chart-main { display: table-cell; vertical-align: top; }
        .chart-legend { margin-top: 8px; }
        .legend-item {
            display: inline-block;
            font-size: 9px;
            color: #4b5563;
            margin: 0 10px 4px 0;
        }
        .legend-dot {
            display: inline-block;
            width: 10px;
            height: 4px;
            border-radius: 999px;
            margin-right: 4px;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    @php
        use App\Support\PdfChartRenderer;
        $student = $data['student'] ?? [];
        $progress = $data['progress'] ?? [];
    @endphp

    <div class="header">
        <h1>Laporan Perkembangan</h1>
        <div class="header-name">{{ $student['name'] ?? 'Peserta' }}</div>
        <div class="header-badge-row">
            <span class="badge">{{ $programLabel }}</span>
        </div>
        <div class="header-printed muted">Dicetak: {{ $data['generated_at'] ?? '-' }}</div>
    </div>

    <div class="section">
        <h2>Laporan Harian</h2>
        <p class="muted" style="margin:0 0 10px;">Tanggal: {{ \Illuminate\Support\Carbon::parse($data['daily_date'])->format('d M Y') }}</p>
        @forelse ($data['daily_reports'] ?? [] as $report)
            <div class="card">
                <div class="card-title">{{ $report['title'] }}</div>
                <div class="muted">{{ $report['created_at'] ?? '' }}</div>
                @if (!empty($report['summary']))
                    <div style="margin-top:6px; color:#374151;">{{ $report['summary'] }}</div>
                @endif
                @foreach ($report['categories'] ?? [] as $key => $val)
                    <span class="tag"><strong>{{ $key }}:</strong> {{ $val }}</span>
                @endforeach
                <div class="muted" style="margin-top:6px;">
                    @if (!empty($report['class_name'])){{ $report['class_name'] }} · @endif
                    {{ $report['created_by'] ?? 'Sistem' }}
                </div>
            </div>
        @empty
            <div class="chart-empty">Belum ada laporan harian untuk tanggal ini.</div>
        @endforelse
    </div>

    <div class="section">
        <h2>Ringkasan Mingguan</h2>
        @forelse ($data['weekly_reports'] ?? [] as $report)
            <div class="card weekly">
                <div class="card-title">{{ $report['title'] }}</div>
                @if (!empty($report['summary']))
                    <div style="margin-top:6px; color:#374151;">{{ $report['summary'] }}</div>
                @endif
                @foreach ($report['categories'] ?? [] as $key => $val)
                    <div style="margin-top:4px;"><strong>{{ $key }}:</strong> {{ $val }}</div>
                @endforeach
            </div>
        @empty
            <div class="chart-empty">Belum ada ringkasan mingguan.</div>
        @endforelse
    </div>

    <div class="section">
        <h2>Materi Kelas</h2>
        <p class="muted" style="margin:0 0 10px;">Jumlah materi &amp; aktivitas per kelas</p>
        @forelse ($progress['materials'] ?? [] as $class)
            <div class="card">
                <div class="card-title">{{ $class['name'] ?? '-' }}</div>
                <table class="stat-grid" style="margin-top:8px;">
                    <tr>
                        <td>
                            <div class="stat-card">
                                <div class="stat-value">{{ $class['materials_count'] ?? 0 }}</div>
                                <div class="stat-label">Materi</div>
                            </div>
                        </td>
                        <td>
                            <div class="stat-card">
                                <div class="stat-value">{{ $class['sessions_count'] ?? 0 }}</div>
                                <div class="stat-label">Sesi</div>
                            </div>
                        </td>
                        <td>
                            <div class="stat-card">
                                <div class="stat-value">{{ $class['activities_count'] ?? 0 }}</div>
                                <div class="stat-label">Aktivitas</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        @empty
            <div class="chart-empty">Belum tergabung di kelas.</div>
        @endforelse
    </div>

    <div class="section">
        <h2>Nilai per Mata Pelajaran</h2>
        <p class="muted" style="margin:0 0 8px;">Perkembangan nilai (%) tiap mata pelajaran dari waktu ke waktu</p>
        {!! PdfChartRenderer::multiLineChart(
            $progress['academic_subject_timeline'] ?? [],
            'percent',
            'Belum ada nilai akademik.'
        ) !!}
    </div>

    <div class="section">
        <h2>Hasil Jasmani</h2>
        <p class="muted" style="margin:0 0 8px;">Perkembangan nilai jasmani peserta dari waktu ke waktu</p>
        {!! PdfChartRenderer::multiLineChart(
            $progress['physical_timeline'] ?? [],
            'value',
            'Belum ada hasil jasmani.'
        ) !!}
    </div>

    <div class="section">
        <h2>Nilai Tes</h2>
        <p class="muted" style="margin:0 0 8px;">Perkembangan persentase nilai dari waktu ke waktu</p>
        {!! PdfChartRenderer::lineChart(
            $progress['academic_timeline'] ?? [],
            '#2F6BFF',
            'Belum ada nilai tes.'
        ) !!}
    </div>
</body>
</html>
