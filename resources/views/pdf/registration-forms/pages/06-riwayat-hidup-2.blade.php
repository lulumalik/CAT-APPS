@php $f = $fields; @endphp
<p>Tempat/tanggal lahir : {{ $line($f['mother_birth'], 40) }}</p>
<p>Pekerjaan : {{ $line($f['mother_occupation'], 40) }}</p>
<p>Alamat : {{ $line($f['mother_address'], 50) }}</p>

<p><strong>3. Nama ayah wali</strong></p>
<p>Tempat/tanggal lahir : {{ $line('', 40) }}</p>
<p>Pekerjaan : {{ $line('', 40) }}</p>
<p>Alamat : {{ $line('', 50) }}</p>

<p><strong>4. Nama ibu wali</strong></p>
<p>Tempat/tanggal lahir : {{ $line('', 40) }}</p>
<p>Pekerjaan : {{ $line('', 40) }}</p>
<p>Alamat : {{ $line('', 50) }}</p>

<p class="bold">5. Saudara</p>
<table class="form-table">
    <tr>
        <th>No</th><th>Nama Lengkap</th><th>P/W</th><th>Usia/Thn</th><th>Kandung/Tiri/Angkat</th><th>Pekerjaan</th><th>Alamat</th>
    </tr>
    @for ($i = 1; $i <= 4; $i++)
    <tr>
        <td>{{ $i }}</td><td></td><td></td><td></td><td></td><td></td><td></td>
    </tr>
    @endfor
</table>

<p class="bold">6. Saudara dari bapak</p>
<table class="form-table">
    <tr>
        <th>No</th><th>Nama Lengkap</th><th>P/W</th><th>Usia/Thn</th><th>Kandung/Tiri/Angkat</th><th>Pekerjaan</th><th>Alamat</th>
    </tr>
    @for ($i = 1; $i <= 3; $i++)
    <tr><td>{{ $i }}</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
    @endfor
</table>

<p class="bold">7. Saudara dari ibu</p>
<table class="form-table">
    <tr>
        <th>No</th><th>Nama Lengkap</th><th>P/W</th><th>Usia/Thn</th><th>Kandung/Tiri/Angkat</th><th>Pekerjaan</th><th>Alamat</th>
    </tr>
    @for ($i = 1; $i <= 3; $i++)
    <tr><td>{{ $i }}</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
    @endfor
</table>

<p class="bold">Lain-lain :</p>
<p>1. Organisasi yang pernah diikuti (nama organisasi, jabatan, tahun) :</p>
<p>a. {{ $line('', 60) }}</p>
<p>b. {{ $line('', 60) }}</p>
<p>c. {{ $line('', 60) }}</p>
