@php $f = $fields; @endphp
<h2>Permohonan Mengikuti Kursus Persiapan Menjadi Taruna/i Akpol</h2>

<p class="right">
    {{ $line($f['document_date_city'], 18) }}, {{ $line($f['document_date_day'], 4) }} {{ $line($f['document_date_month'], 12) }} {{ $course['statement_year'] }}
</p>

<p>Kepada<br>
Yth. {{ $institution['director_title'] }}<br>
di<br>
<strong>{{ $institution['city'] }}</strong></p>

<p>Dengan hormat,</p>

<p>Berdasarkan Pengumuman Nomor : {{ $course['announcement_number'] }} tanggal {{ $course['announcement_month'] }} {{ $course['statement_year'] }} tentang Penerimaan peserta kursus persiapan mengikuti Seleksi Taruna/i Akpol Tahun Anggaran {{ $course['selection_year'] }}, saya yang bertanda tangan di bawah ini :</p>

<div class="label-row"><span class="label">Nama</span> : <span class="field-line">{{ $line($f['name'], 42) }}</span></div>
<div class="label-row"><span class="label">Tempat/tanggal lahir</span> : <span class="field-line">{{ $line($f['birth_place_date'], 42) }}</span></div>
<div class="label-row"><span class="label">Agama</span> : <span class="field-line">{{ $line($f['religion'], 18) }}</span> Suku : <span class="field-line">{{ $line($f['ethnicity'], 18) }}</span></div>
<div class="label-row"><span class="label">Pendidikan</span> : <span class="field-line">{{ $line($f['education'], 42) }}</span></div>
<div class="label-row"><span class="label">Alamat/Kode Pos</span> : <span class="field-line">{{ $line($f['address_domicile_lines'][0] ?? $f['address_domicile'], 42) }}</span></div>
<div class="field-block">{{ $line($f['address_domicile_lines'][1] ?? '', 60) }}</div>
<div class="field-block">{{ $line($f['address_domicile_lines'][2] ?? ($f['postal_code'] ? 'Kode Pos: '.$f['postal_code'] : ''), 60) }}</div>
<div class="label-row"><span class="label">Pekerjaan</span> : <span class="field-line">{{ $line($f['occupation'], 42) }}</span></div>

<p>mengajukan permohonan untuk mengikuti kursus pelatihan seleksi Taruna/i Akpol T.A {{ $course['selection_year'] }}. Selanjutnya saya bersedia untuk mengikuti kegiatan dan memenuhi persyaratan serta mentaati ketentuan yang berlaku, termasuk :</p>

<ol class="list-num">
    <li>Mengikuti seluruh rangkaian kegiatan kursus baik administrasi, pengetahuan umum, teknis, kesampataan jasmani, dll.</li>
    <li>Mentaati segala peraturan yang berlaku selama mengikuti kursus di {{ $institution['name'] }}.</li>
</ol>

<p>Demikian permohonan ini saya buat atas kemauan saya sendiri.</p>

<p class="right" style="margin-top:24px;">Hormat saya,</p>
<p class="materai">MATERAI<br>10.000,-</p>
