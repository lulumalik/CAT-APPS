@php $f = $fields; @endphp
<h2>Surat Pernyataan<br>Tidak Terikat Oleh Suatu Perjanjian dengan Lembaga Kursus Lain</h2>

<p><strong>1. Yang bertanda tangan di bawah ini :</strong></p>
<p class="indent">a. Nama : {{ $line($f['name'], 42) }}</p>
<p class="indent">b. Tempat/tgl lahir : {{ $line($f['birth_place_date'], 42) }}</p>
<p class="indent">c. Agama : {{ $line($f['religion'], 42) }}</p>
<p class="indent">d. Pendidikan : {{ $line($f['education'], 42) }}</p>
<p class="indent">e. Alamat : {{ $line($f['address_domicile_lines'][0] ?? $f['address_domicile'], 42) }}</p>
<div class="field-block indent">{{ $line($f['address_domicile_lines'][1] ?? '', 55) }}</div>
<div class="field-block indent">{{ $line($f['address_domicile_lines'][2] ?? '', 55) }}</div>

<p class="bold center">MENYATAKAN</p>

<p><strong>2.</strong> Bahwa saya sampai dengan saat ini tidak/belum terikat oleh suatu perjanjian ikatan dinas dengan suatu instansi pemerintah maupun swasta.</p>
<p><strong>3.</strong> Apabila dikemudian hari ternyata bahwa pernyataan saya tersebut tidak benar, saya bersedia dikenakan sanksi/hukuman menurut ketentuan/peraturan yang berlaku di lingkungan Polri.</p>
<p><strong>4.</strong> Demikianlah surat pernyataan ini saya buat dengan sebenarnya.</p>

<p>Mengetahui {{ $line($f['document_date_city'], 14) }}, {{ $line($f['document_date_day'], 4) }} {{ $course['statement_year'] }}<br>
Lurah / Kepala Desa calon</p>
<div class="sign-row">
    <div class="sign-col">{{ $line('', 24) }}</div>
    <div class="sign-col">{{ $line($f['name'], 24) }}</div>
</div>
<p>Camat {{ $line('', 20) }}</p>
<p class="materai">MATERAI<br>10.000,-</p>
