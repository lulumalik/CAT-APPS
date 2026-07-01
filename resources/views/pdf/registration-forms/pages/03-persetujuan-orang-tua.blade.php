@php $f = $fields; @endphp
<h2>Surat Persetujuan Orang Tua / Wali</h2>

<p>Yang bertanda tangan di bawah ini, saya selaku orang tua/wali</p>

<div class="label-row"><span class="label">Nama</span> : <span class="field-line">{{ $line($f['parent_name'], 42) }}</span></div>
<div class="label-row"><span class="label">Tempat/tgl lahir</span> : <span class="field-line">{{ $line($f['parent_birth_place_date'], 42) }}</span></div>
<div class="label-row"><span class="label">Pekerjaan</span> : <span class="field-line">{{ $line($f['parent_occupation'], 42) }}</span></div>
<div class="label-row"><span class="label">Alamat</span> : <span class="field-line">{{ $line($f['parent_address_lines'][0] ?? $f['parent_address'], 42) }}</span></div>
<div class="field-block">{{ $line($f['parent_address_lines'][1] ?? '', 60) }}</div>
<div class="field-block">{{ $line($f['parent_address_lines'][2] ?? '', 60) }}</div>
<div class="label-row"><span class="label">Hubungan keluarga dengan calon peserta kursus</span> : <span class="field-line">{{ $line($f['parent_relationship'], 28) }}</span></div>

<p>Dengan ini saya menyatakan, menyetujui calon peserta tersebut di bawah ini mengikuti seluruh kegiatan kursus seleksi untuk menjadi Taruna/i Akpol sesuai dengan persyaratan dan ketentuan yang berlaku.</p>

<div class="label-row"><span class="label">Nama</span> : <span class="field-line">{{ $line($f['name'], 42) }}</span></div>
<div class="label-row"><span class="label">Tempat/tgl lahir</span> : <span class="field-line">{{ $line($f['birth_place_date'], 42) }}</span></div>
<div class="label-row"><span class="label">Alamat</span> : <span class="field-line">{{ $line($f['address_domicile_lines'][0] ?? $f['address_domicile'], 42) }}</span></div>
<div class="field-block">{{ $line($f['address_domicile_lines'][1] ?? '', 60) }}</div>
<div class="field-block">{{ $line($f['address_domicile_lines'][2] ?? '', 60) }}</div>

<p>Apabila yang bersangkutan, atas kemauan sendiri menolak atau mengundurkan diri untuk melakukan sebagian atau seluruh kegiatan kursus yang harus dilaksanakan, saya tidak akan menuntut pengembalian semua biaya yang telah dibayarkan baginya, menurut ketentuan yang berlaku.</p>

<p class="right" style="margin-top:20px;">{{ $line($f['document_date_city'], 14) }}, {{ $line($f['document_date_day'], 4) }} {{ $course['statement_year'] }}</p>
<p class="right">saya yang bertanda tangan</p>
<div class="sign-row">
    <div class="sign-col">{{ $line('', 24) }}</div>
    <div class="sign-col">{{ $line('', 24) }}</div>
</div>
