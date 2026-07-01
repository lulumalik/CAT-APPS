@php $f = $fields; @endphp
<h2>Surat Pernyataan<br>Tidak Mendukung atau Ikut Serta dalam Organisasi atau Paham yang Bertentangan<br>dengan Pancasila, Undang-Undang Dasar 1945, NKRI dan Bhinneka Tunggal Ika</h2>

<p>Yang bertandatangan di bawah ini :</p>
<div class="label-row"><span class="label">Nama lengkap</span> : <span class="field-line">{{ $line($f['name'], 42) }}</span></div>
<div class="label-row"><span class="label">Tempat/Tanggal lahir</span> : <span class="field-line">{{ $line($f['birth_place_date'], 42) }}</span></div>
<div class="label-row"><span class="label">Agama</span> : <span class="field-line">{{ $line($f['religion'], 42) }}</span></div>
<div class="label-row"><span class="label">Jenis kelamin</span> : <span class="field-line">{{ $line($f['gender_label'], 42) }}</span></div>
<div class="label-row"><span class="label">Pendidikan terakhir</span> : <span class="field-line">{{ $line($f['education'], 42) }}</span></div>
<div class="label-row"><span class="label">Alamat lengkap rumah</span> : <span class="field-line">{{ $line($f['address_domicile'], 42) }}</span></div>
<div class="label-row"><span class="label">Nomor telepon</span> : <span class="field-line">{{ $line($f['whatsapp'] ?: $f['phone'], 42) }}</span></div>

<p>Menyatakan dengan sebenarnya bahwa saya <strong>Tidak Mendukung Atau Ikut Serta Dalam Organisasi Atau Paham Yang Bertentangan Dengan Pancasila, Undang-Undang Dasar 1945, NKRI Dan Bhinneka Tunggal Ika</strong>.</p>

<p>Apabila pernyataan saya ini ternyata di kemudian hari terbukti tidak benar dan/atau ternyata saya melanggar pernyataan tersebut, maka saya bersedia mendapatkan sanksi hukum sesuai ketentuan yang berlaku.</p>

<p>Demikian surat pernyataan ini saya buat dengan sebenarnya untuk dapat digunakan sebagai bukti pemenuhan syarat untuk mengikuti Kursus Seleksi Taruna Akpol.</p>

<p>{{ $line($f['document_date_city'], 10) }}, {{ $line($f['document_date_day'], 4) }} {{ $course['statement_year'] }}<br>
Yang menyatakan,</p>
<p class="materai">MATERAI<br>10.000,-</p>
