@php $f = $fields; @endphp
<h2>Daftar Riwayat Hidup</h2>

<table style="width:100%; border-collapse:collapse; font-size:10px;">
    <tr>
        <td style="width:50%; padding:4px; border:1px solid #333;">Nama Lengkap : <strong>{{ $line($f['name'], 24) }}</strong></td>
        <td style="width:50%; padding:4px; border:1px solid #333;">Agama : {{ $line($f['religion'], 20) }}</td>
    </tr>
    <tr>
        <td style="padding:4px; border:1px solid #333;">Jenis Kelamin {{ $line($f['gender_short'], 12) }} *)</td>
        <td style="padding:4px; border:1px solid #333;">Kawin/Belum kawin : {{ $line($f['marital_status'], 16) }}</td>
    </tr>
    <tr>
        <td colspan="2" style="padding:4px; border:1px solid #333;">Alamat : {{ $line($f['address_domicile_lines'][0] ?? $f['address_domicile'], 50) }}</td>
    </tr>
    <tr>
        <td style="padding:4px; border:1px solid #333;">Tempat Lahir : {{ $line($f['birth_place'], 20) }}</td>
        <td style="padding:4px; border:1px solid #333;">{{ $line($f['address_domicile_lines'][1] ?? '', 30) }}</td>
    </tr>
    <tr>
        <td style="padding:4px; border:1px solid #333;">Tanggal Lahir : {{ $line($f['birth_date_formatted'] ?: $f['birth_date'], 20) }}</td>
        <td style="padding:4px; border:1px solid #333;">{{ $line($f['address_domicile_lines'][2] ?? '', 30) }}</td>
    </tr>
    <tr>
        <td style="padding:4px; border:1px solid #333;">Suku : {{ $line($f['ethnicity'], 20) }}</td>
        <td style="padding:4px; border:1px solid #333;"></td>
    </tr>
    <tr>
        <td style="padding:4px; border:1px solid #333;">Tinggi Badan : {{ $line((string) $f['height_cm'], 6) }} cm</td>
        <td style="padding:4px; border:1px solid #333;" rowspan="5" class="small">KECAKAPAN<br>Bahasa Menulis<br><br>Rambut<br>Mata<br>Ciri lain<br>Golongan Darah</td>
    </tr>
    <tr>
        <td style="padding:4px; border:1px solid #333;">Berat badan : {{ $line((string) $f['weight_kg'], 6) }} kg</td>
    </tr>
    <tr><td style="padding:4px; border:1px solid #333;">Rambut : {{ $line($f['hair'], 20) }}</td></tr>
    <tr><td style="padding:4px; border:1px solid #333;">Mata : {{ $line($f['eyes'], 20) }}</td></tr>
    <tr><td style="padding:4px; border:1px solid #333;">Ciri lain : {{ $line($f['other_traits'], 20) }}</td></tr>
    <tr>
        <td style="padding:4px; border:1px solid #333;">Golongan Darah : {{ $line($f['blood_type'], 10) }}</td>
        <td style="padding:4px; border:1px solid #333;"></td>
    </tr>
</table>

<p class="bold" style="margin-top:10px;">Pendidikan Umum &nbsp;&nbsp;&nbsp; Kursus-kursus :</p>
<table style="width:100%; border-collapse:collapse; font-size:10px;">
    <tr><td style="padding:3px; border:1px solid #333; width:15%;">SD</td><td style="padding:3px; border:1px solid #333;">{{ $line($f['education_sd'], 40) }}</td></tr>
    <tr><td style="padding:3px; border:1px solid #333;">SMP</td><td style="padding:3px; border:1px solid #333;">{{ $line($f['education_smp'], 40) }}</td></tr>
    <tr><td style="padding:3px; border:1px solid #333;">SMA</td><td style="padding:3px; border:1px solid #333;">{{ $line($f['education_sma'] ?: $f['education'], 40) }}</td></tr>
    <tr><td style="padding:3px; border:1px solid #333;">PT/Akd</td><td style="padding:3px; border:1px solid #333;">{{ $line($f['education_pt'], 40) }}</td></tr>
</table>

<p class="bold" style="margin-top:8px;">Prestasi:</p>
<div class="field-block">{{ $line('', 70) }}</div>
<div class="field-block">{{ $line('', 70) }}</div>

<p class="bold">Pengalaman kerja :</p>
<div class="field-block">{{ $line('', 70) }}</div>
<div class="field-block">{{ $line('', 70) }}</div>

<p class="small">* ganti nama polda sesuai dengan polda pendaftar</p>

<p class="bold" style="margin-top:10px;">KELUARGA</p>
<p><strong>1. Nama ayah kandung :</strong> {{ $line($f['father_name'], 40) }}</p>
<p>Tempat/tanggal lahir : {{ $line($f['father_birth'], 40) }}</p>
<p>Pekerjaan : {{ $line($f['father_occupation'], 40) }}</p>
<p>Alamat : {{ $line($f['father_address'], 50) }}</p>

<p><strong>2. Nama ibu kandung :</strong> {{ $line($f['mother_name'], 40) }}</p>
