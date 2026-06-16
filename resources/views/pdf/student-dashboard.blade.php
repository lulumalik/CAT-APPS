<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Dashboard</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            line-height: 1.45;
            margin: 0;
            padding: 24px;
        }
        h1 { font-size: 20px; margin: 0 0 4px; }
        h2 { font-size: 14px; margin: 0 0 10px; color: #1a1a1a; }
        h3 { font-size: 12px; margin: 0 0 8px; }
        .muted { color: #6b7280; font-size: 10px; }
        .header {
            border-bottom: 2px solid #b8c0cc;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .section {
            border: 1.5px solid #cdd4de;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 14px;
            page-break-inside: avoid;
        }
        .card {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 8px 10px;
            margin-bottom: 8px;
            background: #fafbfc;
        }
        .card:last-child { margin-bottom: 0; }
        .tag {
            display: inline-block;
            background: #f3f4f6;
            color: #4b5563;
            font-size: 9px;
            border-radius: 999px;
            padding: 2px 8px;
            margin: 2px 4px 2px 0;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        table.data th, table.data td {
            border: 1px solid #e5e7eb;
            padding: 6px 8px;
            text-align: left;
        }
        table.data th { background: #f9fafb; }
        .weekly {
            border-color: #9db359;
            background: #f7faf2;
        }
        .two-col { width: 100%; }
        .two-col td { vertical-align: top; width: 50%; padding-right: 8px; }
    </style>
</head>
<body>
    @php
        $student = $data['student'] ?? [];
        $overview = $data['overview'] ?? [];
        $progress = $data['progress'] ?? [];
        $classes = $overview['classes'] ?? [];
        $activities = $overview['class_activities'] ?? [];
    @endphp

    <div class="header">
        <h1>Laporan Dashboard</h1>
        <div class="muted">
            {{ $student['name'] ?? 'Peserta' }} · {{ $programLabel }}
            <br>Dicetak: {{ $data['generated_at'] ?? '-' }}
        </div>
    </div>

    <table class="two-col">
        <tr>
            <td>
                <div class="section">
                    <h2>Kelas Saya</h2>
                    @forelse ($classes as $class)
                        <div class="card">
                            <strong>{{ $class['name'] ?? '-' }}</strong>
                            <div class="muted">{{ $class['class_code'] ?? '' }}</div>
                            <div class="muted">
                                Aktivitas terakhir:
                                {{ $class['latest_activity']['title'] ?? 'Belum ada aktivitas' }}
                            </div>
                        </div>
                    @empty
                        <div class="muted">Belum ada kelas yang ditambahkan.</div>
                    @endforelse
                </div>
            </td>
            <td>
                <div class="section">
                    <h2>Aktivitas Kelas</h2>
                    @forelse ($activities as $activity)
                        <div class="card">
                            <strong>{{ $activity['title'] ?? '-' }}</strong>
                            <div class="muted">
                                {{ $activity['bimble_class']['name'] ?? '' }}
                                @if (!empty($activity['creator']['name']))
                                    · {{ $activity['creator']['name'] }}
                                @endif
                                @if (!empty($activity['happened_at']))
                                    · {{ \Illuminate\Support\Carbon::parse($activity['happened_at'])->timezone('Asia/Jakarta')->format('d M Y H:i') }}
                                @endif
                            </div>
                            @if (!empty($activity['description']))
                                <div style="margin-top:4px;">{{ $activity['description'] }}</div>
                            @endif
                        </div>
                    @empty
                        <div class="muted">Belum ada aktivitas kelas.</div>
                    @endforelse
                </div>
            </td>
        </tr>
    </table>

    <div class="section">
        <h2>Perkembangan Saya</h2>

        <h3>Laporan Harian ({{ \Illuminate\Support\Carbon::parse($data['daily_date'])->format('d M Y') }})</h3>
        @forelse ($data['daily_reports'] ?? [] as $report)
            <div class="card">
                <strong>{{ $report['title'] }}</strong>
                <span class="muted"> · {{ $report['created_at'] ?? '' }}</span>
                @if (!empty($report['summary']))
                    <div style="margin-top:4px;">{{ $report['summary'] }}</div>
                @endif
                @foreach ($report['categories'] ?? [] as $key => $val)
                    <span class="tag"><strong>{{ $key }}:</strong> {{ $val }}</span>
                @endforeach
                <div class="muted" style="margin-top:4px;">
                    @if (!empty($report['class_name'])){{ $report['class_name'] }} · @endif
                    {{ $report['created_by'] ?? 'Sistem' }}
                </div>
            </div>
        @empty
            <div class="muted">Belum ada laporan harian untuk tanggal ini.</div>
        @endforelse

        <h3 style="margin-top:14px;">Ringkasan Mingguan</h3>
        @forelse ($data['weekly_reports'] ?? [] as $report)
            <div class="card weekly">
                <strong>{{ $report['title'] }}</strong>
                @if (!empty($report['summary']))
                    <div style="margin-top:4px;">{{ $report['summary'] }}</div>
                @endif
                @foreach ($report['categories'] ?? [] as $key => $val)
                    <div style="margin-top:4px;"><strong>{{ $key }}:</strong> {{ $val }}</div>
                @endforeach
            </div>
        @empty
            <div class="muted">Belum ada ringkasan mingguan.</div>
        @endforelse

        <h3 style="margin-top:14px;">Materi Kelas</h3>
        @forelse ($progress['materials'] ?? [] as $class)
            <div class="card">
                <strong>{{ $class['name'] ?? '-' }}</strong>
                <div class="muted">
                    {{ $class['materials_count'] ?? 0 }} materi ·
                    {{ $class['sessions_count'] ?? 0 }} sesi ·
                    {{ $class['activities_count'] ?? 0 }} aktivitas
                </div>
            </div>
        @empty
            <div class="muted">Belum tergabung di kelas.</div>
        @endforelse

        <h3 style="margin-top:14px;">Nilai per Mata Pelajaran</h3>
        @php $subjectSeries = $progress['academic_subject_timeline'] ?? []; @endphp
        @if (count($subjectSeries))
            <table class="data">
                <thead>
                    <tr>
                        <th>Mata Pelajaran</th>
                        <th>Tanggal</th>
                        <th>Nilai (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($subjectSeries as $series)
                        @foreach ($series['points'] ?? [] as $point)
                            <tr>
                                <td>{{ $series['label'] ?? '-' }}</td>
                                <td>{{ $point['date'] ?? '-' }}</td>
                                <td>{{ $point['percent'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="muted">Belum ada nilai akademik.</div>
        @endif

        <h3 style="margin-top:14px;">Hasil Jasmani</h3>
        @php $physicalSeries = $progress['physical_timeline'] ?? []; @endphp
        @if (count($physicalSeries))
            <table class="data">
                <thead>
                    <tr>
                        <th>Komponen</th>
                        <th>Tanggal</th>
                        <th>Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($physicalSeries as $series)
                        @foreach ($series['points'] ?? [] as $point)
                            <tr>
                                <td>{{ $series['label'] ?? '-' }}</td>
                                <td>{{ $point['date'] ?? '-' }}</td>
                                <td>{{ $point['value'] ?? '-' }} {{ $series['unit'] ?? '' }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="muted">Belum ada hasil jasmani.</div>
        @endif

        <h3 style="margin-top:14px;">Nilai Tes</h3>
        @php $academicTimeline = $progress['academic_timeline'] ?? []; @endphp
        @if (count($academicTimeline))
            <table class="data">
                <thead>
                    <tr>
                        <th>Tes</th>
                        <th>Kategori</th>
                        <th>Tanggal</th>
                        <th>Nilai (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($academicTimeline as $row)
                        <tr>
                            <td>{{ $row['label'] ?? '-' }}</td>
                            <td>{{ $row['category'] ?? '-' }}</td>
                            <td>{{ $row['date'] ?? '-' }}</td>
                            <td>{{ $row['percent'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="muted">Belum ada nilai tes.</div>
        @endif
    </div>
</body>
</html>
