@php $f = $fields; @endphp
<h2>Surat Pernyataan</h2>

<p>Yang bertanda tangan di bawah ini:</p>
<p>1. Nama : {{ $line($f['name'], 42) }}</p>
<p>2. Tempat tanggal lahir : {{ $line($f['birth_place_date'], 42) }}</p>
<p>3. Nomor peserta : {{ $line($f['participant_number'], 42) }}</p>
<p>4. Alamat : {{ $line($f['address_domicile'], 42) }}</p>
<p>5. NIK KTP : {{ $line($f['nik'], 42) }}</p>
<p>6. Peserta kursus : Seleksi Calon Taruna/i Akpol T.A. {{ $course['selection_year'] }}</p>

<p>Dengan penuh kesadaran menyatakan bahwa saya sanggup tidak akan mengundurkan diri pada saat terpilih mengikuti Kursus seleksi Calon Taruna/i Akpol pada {{ $institution['name'] }}.</p>

<p>Demikian surat pernyataan ini saya buat dengan sungguh-sungguh dan penuh kesadaran tanpa tekanan dari pihak manapun serta apabila melanggar surat pernyataan ini maka saya bersedia untuk mendapatkan sanksi hukum sesuai ketentuan yang berlaku.</p>

<p>Mengetahui {{ $line($f['document_date_city'], 14) }}, {{ $line($f['document_date_day'], 4) }} {{ $course['statement_year'] }}<br>
Orang tua / wali &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Yang Membuat Pernyataan,</p>
<div class="sign-row">
    <div class="sign-col">{{ $line($f['parent_name'], 24) }}</div>
    <div class="sign-col">{{ $line($f['name'], 24) }}</div>
</div>
<p class="materai">MATERAI<br>10.000,-</p>
