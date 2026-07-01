@php $f = $fields; @endphp
<h2>Surat Pernyataan</h2>

<p>Yang bertanda tangan di bawah ini saya:</p>
<div class="label-row"><span class="label">Nama</span> : <span class="field-line">{{ $line($f['name'], 42) }}</span></div>
<div class="label-row"><span class="label">Alamat</span> : <span class="field-line">{{ $line($f['address_domicile_lines'][0] ?? $f['address_domicile'], 42) }}</span></div>
<div class="field-block">{{ $line($f['address_domicile_lines'][1] ?? '', 60) }}</div>
<div class="field-block">{{ $line($f['address_domicile_lines'][2] ?? '', 60) }}</div>
<div class="label-row"><span class="label">Kursus</span> : Seleksi Calon Taruna/i Akpol T.A. {{ $course['selection_year'] }}</div>
<div class="label-row"><span class="label">Nomor Peserta</span> : <span class="field-line">{{ $line($f['participant_number'], 24) }}</span></div>

<p>Sebagai peserta kursus seleksi penerimaan Taruna Akpol, dengan penuh kesadaran dan kesungguhan menyatakan :</p>
<ol class="list-num">
    <li>Akan mengikuti seluruh kursus seleksi penerimaan Taruna Akpol dengan mengutamakan kejujuran sesuai dengan kemampuan yang saya miliki.</li>
    <li>Tidak akan melakukan tindakan kolusi dan nepotisme maupun tindakan lain yang menyimpang dari ketentuan, baik dengan panitia seleksi ataupun pihak lain (menyuap ataupun menjanjikan sesuatu) yang dapat mempengaruhi obyektifitas penilaian karena dapat merugikan diri saya sendiri, orang lain dan {{ $institution['name'] }}.</li>
</ol>

<p>Saya menyadari bahwa untuk menjadi peserta kursus harus diawali dengan mengutamakan kejujuran, maka apabila saya tidak melaksanakan pernyataan ini maka saya bersedia untuk dikenakan sanksi administrasi dan teguran keras dari Pihak Lembaga Kursus.</p>
<p>Demikian surat pernyataan ini saya buat dengan sebenarnya.</p>

<p>{{ $line($f['document_date_city'], 12) }}, {{ $line($f['document_date_day'], 4) }} {{ $course['statement_year'] }}</p>
<div class="sign-row">
    <div class="sign-col">
        <p>Saya yang menyatakan<br>(Tanda Tangan)<br><br>{{ $line($f['name'], 22) }}</p>
        <p class="materai">Materai<br>Rp 10.000,-</p>
    </div>
    <div class="sign-col">
        <p>mengetahui<br>ORANG TUA / WALI<br>(Tanda Tangan)<br><br>{{ $line($f['parent_name'], 22) }}</p>
    </div>
</div>
