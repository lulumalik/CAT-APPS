@php $f = $fields; @endphp
<h2>Pemeriksaan Berkas Fotocopy/Legalisir</h2>

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
            ['1', 'IJAZAH SD', 'Dilegalisir', ''],
            ['2', 'IJAZAH SMP', 'Dilegalisir', ''],
            ['3', 'IJAZAH SMA/SEDERAJAT', 'Dilegalisir', ''],
            ['4', 'AKTE KELAHIRAN', 'Dilegalisir / barcode cukup fotocopy', ''],
            ['5', 'KARTU TANDA PENDUDUK', 'Legalisir/fotocopy', ''],
            ['6', 'KARTU KELUARGA', 'Dilegalisir / barcode cukup fotocopy', ''],
            ['7', 'SURAT PERMOHONAN MENJADI PESERTA KURSUS', "Bermaterai\nHuruf balok, tinta hitam", ''],
            ['8', 'SURAT KETERANGAN BERBADAN SEHAT', 'Dilegalisir', ''],
            ['9', 'SURAT KETERANGAN CATATAN KEPOLISIAN (SKCK)', 'Dilegalisir', ''],
            ['10', 'SURAT PERSETUJUAN ORANG TUA/WALI', 'Sesuai format, diketahui Lurah/Kades', ''],
            ['11', 'SURAT PERNYATAAN BELUM PERNAH MENIKAH', 'Bermaterai, diketahui Lurah/Kades', ''],
            ['12', 'DAFTAR RIWAYAT HIDUP', 'Sesuai format, diketahui Lurah/Kades', ''],
            ['13', 'SURAT PERJANJIAN SELAMA MENGIKUTI KURSUS', 'Bermaterai, ditandatangani pihak 1 & 2', ''],
            ['14', 'SURAT PERNYATAAN TIDAK TERIKAT PERJANJIAN', 'Bermaterai, diketahui Lurah/Kades', ''],
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
