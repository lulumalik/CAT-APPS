@php $f = $fields; @endphp
<p>2. Cabang olah raga yang aktif diikuti :</p>
<p>a. {{ $line('', 60) }}</p>
<p>b. {{ $line('', 60) }}</p>
<p>c. {{ $line('', 60) }}</p>
<p>d. {{ $line('', 60) }}</p>

<p>3. Cabang kesenian yang aktif diikuti :</p>
<p>a. {{ $line('', 60) }}</p>
<p>b. {{ $line('', 60) }}</p>
<p>c. {{ $line('', 60) }}</p>
<p>d. {{ $line('', 60) }}</p>

<p>4. Keterangan lain yang perlu dikemukakan :</p>
<p>a. Tidak pernah dipidana karena melakukan suatu kejahatan</p>
<p>b. Tidak terkait perjanjian ikatan dinas dengan instansi lain</p>

<p>Daftar riwayat hidup ini saya buat dengan sebenar-benarnya.<br>
Dan apabila terdapat keterangan yang dipalsukan, saya bersedia dituntut secara hukum.</p>

<div style="margin-top:16px;">
    <div class="photo-box">Pas foto<br>4 x 6<br>(warna)</div>
    <p>Mengetahui &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Tanda tangan</p>
    <p>Lurah/Kepala Desa</p>
    <p>{{ $line('', 28) }} &nbsp;&nbsp;&nbsp; {{ $line($f['name'], 28) }}</p>
</div>
