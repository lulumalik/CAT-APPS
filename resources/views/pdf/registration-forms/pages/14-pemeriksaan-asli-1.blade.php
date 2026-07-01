@php $f = $fields; @endphp
<h2>Pemeriksaan Berkas Asli</h2>

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
            ['1', 'IJAZAH SD', 'Asli', ''],
            ['2', 'IJAZAH SMP', 'Asli', ''],
            ['3', 'IJAZAH SMA/SEDERAJAT', 'Asli', ''],
            ['4', 'AKTE KELAHIRAN', 'Asli', ''],
            ['5', 'KARTU TANDA PENDUDUK', 'Asli', ''],
            ['6', 'KARTU KELUARGA', 'Asli', ''],
            ['7', 'SURAT PERMOHONAN MENGIKUTI KURSUS', "Asli, bermaterai\nTulisan tangan huruf balok\nTinta hitam di kertas folio bergaris", ''],
            ['8', 'SURAT KETERANGAN BERBADAN SEHAT', "Asli\nDari institusi kesehatan", ''],
            ['9', 'SURAT KETERANGAN CATATAN KEPOLISIAN (SKCK)', "Asli\nDari Polres setempat", ''],
            ['10', 'SURAT PERSETUJUAN ORANG TUA/WALI', "Asli\nSesuai format\nDiketahui Lurah/Kades", ''],
            ['11', 'SURAT PERNYATAAN BELUM PERNAH MENIKAH', "Asli, bermaterai\nSesuai format\nDiketahui Lurah/Kades", ''],
            ['12', 'DAFTAR RIWAYAT HIDUP', "Asli\nSesuai format\nDiketahui Lurah/Kades", ''],
            ['13', 'SURAT PERJANJIAN MENGIKUTI KURSUS', "Asli, bermaterai\nSesuai format", ''],
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
