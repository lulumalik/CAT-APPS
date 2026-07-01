@php $f = $fields; @endphp
<h1>Formulir dan Persyaratan Menjadi Peserta Kursus</h1>
<h2>Persiapan Seleksi Menjadi Taruna/Taruni Akpol pada Lembaga<br>{{ $institution['name'] }}</h2>

<ol class="list-num">
    <li>Mengisi berkas persyaratan di bawah ini kemudian cetak dan diserahkan ke sekretariat dengan jumlah 4 rangkap.</li>
    <li>Surat permohonan mengikuti kursus:
        <ol type="a" class="list-num">
            <li>Ditulis dengan tinta warna hitam di atas kertas folio bergaris bermaterai Rp. 10.000,-</li>
            <li>Ditulis sendiri oleh pelamar</li>
            <li>Menggunakan huruf balok tanpa coretan/dihapus</li>
        </ol>
    </li>
    <li>Jika belum jelas harap menghubungi sekretariat.</li>
    <li>Berkas yang dibawa pada saat pendaftaran:
        <ol type="a" class="list-num">
            <li>Surat permohonan mengikuti kursus</li>
            <li>Fotocopy + legalisir akta kelahiran/surat kenal lahir (jika sudah ada barcode tidak perlu dilegalisir, cukup fotocopy)</li>
            <li>Fotocopy + legalisir ijazah/STTB SD, SMP, SMA</li>
            <li>Surat keterangan berbadan sehat dari institusi kesehatan resmi milik pemerintah (di luar kesehatan Polri)</li>
            <li>Surat keterangan catatan kepolisian (SKCK)</li>
            <li>Fotocopy + legalisir KTP dan KK (jika sudah ada barcode tidak perlu dilegalisir, cukup fotocopy)</li>
            <li>Daftar riwayat hidup</li>
            <li>Surat persetujuan orang tua/wali</li>
            <li>Surat pernyataan belum pernah menikah</li>
            <li>Surat pernyataan tidak terikat oleh suatu lembaga kursus sejenis</li>
            <li>Surat pernyataan orang tua/wali</li>
            <li>Surat pernyataan tidak melakukan KKN</li>
            <li>Surat pernyataan tidak mendukung atau ikut serta dalam organisasi atau paham yang bertentangan dengan Pancasila, UUD 1945, NKRI dan Bhinneka Tunggal Ika</li>
            <li>Tidak melakukan perbuatan yang melanggar norma agama, norma kesusilaan, norma sosial dan norma hukum</li>
            <li>Surat tidak akan mengundurkan diri</li>
        </ol>
    </li>
</ol>

<p class="small center" style="margin-top:20px;">Dicetak otomatis dari data pendaftaran — {{ $printed_at }}</p>
