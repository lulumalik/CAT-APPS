@php $f = $fields; @endphp
<h2>Surat Pernyataan Belum Pernah Menikah</h2>

<p>Yang bertanda tangan di bawah ini :</p>

<div class="label-row"><span class="label">Nama</span> : <span class="field-line">{{ $line($f['name'], 42) }}</span></div>
<div class="label-row"><span class="label">Tempat/tgl lahir</span> : <span class="field-line">{{ $line($f['birth_place_date'], 42) }}</span></div>
<div class="label-row"><span class="label">Suku</span> : <span class="field-line">{{ $line($f['ethnicity'], 42) }}</span></div>
<div class="label-row"><span class="label">Agama</span> : <span class="field-line">{{ $line($f['religion'], 42) }}</span></div>
<div class="label-row"><span class="label">Alamat</span> : <span class="field-line">{{ $line($f['address_domicile_lines'][0] ?? $f['address_domicile'], 42) }}</span></div>
<div class="field-block">{{ $line($f['address_domicile_lines'][1] ?? '', 60) }}</div>
<div class="field-block">{{ $line($f['address_domicile_lines'][2] ?? '', 60) }}</div>

<p>Dengan ini menyatakan, bahwa saya belum pernah menikah dan tidak akan menikah selama dan sesudah mengikuti kursus pelatihan Seleksi Taruna Akpol sampai dengan usia 19 tahun sesuai ketentuan Pasal 7 Ayat (1) Undang-Undang Nomor 16 Tahun 2019 tentang perubahan atas Undang-Undang Nomor 1 Tahun 1974 tentang perkawinan.</p>

<p>Surat pernyataan ini saya buat dengan sebenarnya tanpa paksaan dari siapapun dan dibuat dengan penuh kesadaran, untuk memenuhi persyaratan menjadi peserta kursus persiapan mengikuti seleksi Taruna Akpol.</p>

<p>Mengetahui {{ $line($f['document_date_city'], 14) }}, {{ $line($f['document_date_day'], 4) }} {{ $course['statement_year'] }}<br>
Orang tua / wali yang menyatakan,</p>
<div class="sign-row">
    <div class="sign-col">{{ $line($f['parent_name'], 24) }}</div>
    <div class="sign-col">{{ $line('', 24) }}</div>
</div>
<p class="materai">MATERAI<br>10.000,-</p>
