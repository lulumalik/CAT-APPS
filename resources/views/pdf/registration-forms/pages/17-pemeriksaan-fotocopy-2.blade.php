@php $f = $fields; @endphp
<h2>Pemeriksaan Berkas Fotocopy/Legalisir (lanjutan)</h2>

<table class="form-table">
    <tr>
        <th style="width:6%;">NO</th>
        <th style="width:28%;">BERKAS ADM YG DIPERIKSA</th>
        <th style="width:10%;">ADA</th>
        <th style="width:10%;">TIDAK</th>
        <th>KETENTUAN</th>
        <th style="width:18%;">KETERANGAN</th>
    </tr>
    @php
        $rows = [
            ['15', 'SURAT PERNYATAAN ORANG TUA/WALI', 'Bermaterai, sesuai format', ''],
            ['16', 'SURAT PERNYATAAN TIDAK MELAKUKAN KKN', 'Asli, bermaterai', ''],
            ['17', 'SURAT PERNYATAAN TIDAK MENDUKUNG ORGANISASI/PAHAM BERTENTANGAN', 'Asli, bermaterai', ''],
            ['18', 'TIDAK MELANGGAR NORMA AGAMA, KESUSILAAN, SOSIAL DAN HUKUM', 'Asli, bermaterai', ''],
            ['19', 'SURAT TIDAK AKAN MENGUNDURKAN DIRI', 'Asli, bermaterai', ''],
        ];
    @endphp
    @foreach ($rows as $row)
    <tr>
        <td class="center">{{ $row[0] }}</td>
        <td>{{ $row[1] }}</td>
        <td></td><td></td>
        <td class="small">{!! nl2br(e($row[2])) !!}</td>
        <td>{{ $row[3] }}</td>
    </tr>
    @endforeach
</table>

<p class="small center" style="margin-top:12px;">Peserta: {{ $line($f['name'], 30) }} — No. {{ $line($f['participant_number'], 12) }}</p>
