# LAPORAN PERTANGGUNGJAWABAN PEKERJAAN

## PEMBUATAN APLIKASI SOFTWARE BIMBEL
### Pratistha Cendekia Prestasi (CAT Apps)

---

**Nomor Dokumen:** LPP-CAT/001/2026  
**Tanggal:** 8 Juli 2026  
**Periode Pelaksanaan:** 4 Mei 2026 – 31 Mei 2026  
**Website Produksi:** https://pratisthaindonesia.com

---

## I. IDENTITAS PIHAK

### PIHAK PERTAMA (Developer)

| | |
|---|---|
| **Nama** | Lulu Maulana Malik |
| **Alamat** | Sukasari No. 34, Kec. Cibiru, Kel. Pasirbiru, Kota Bandung |
| **No. KTP** | 3273252604980002 |

### PIHAK KEDUA (Klien)

| | |
|---|---|
| **Nama** | BJP. (P) Drs. H. Awang Anwarudin, M.H. |
| **Alamat** | Kantor PP Polri Daerah Jabar, Komplek Pol Jl. Sukamaju No. 142, Kel. Cipadung Kulon, Kec. Panyileukan, Kota Bandung – Jabar 40614 |

Dokumen ini disusun sebagai laporan pertanggungjawaban pelaksanaan **Perjanjian Kerja Sama Pembuatan Aplikasi Software Bimbel** tertanggal **4 Mei 2026**, sebagaimana tercantum dalam Pasal 1–5 perjanjian tersebut.

---

## II. RINGKASAN EKSEKUTIF

PIHAK PERTAMA telah menyelesaikan pengembangan aplikasi **Pratistha Cendekia Prestasi (CAT Apps)** — platform web bimbingan belajar dan simulasi seleksi AKPOL — sesuai ruang lingkup pekerjaan dalam Perjanjian Kerja Sama.

Aplikasi telah di-deploy ke server produksi, dapat diakses publik, dan beroperasi dengan arsitektur modern (Laravel 12 + Vue 3) yang mendukung operasional bimbel online maupun pendukung proses offline.

### Ringkasan Status 12 Poin Ruang Lingkup Pekerjaan

| No | Ruang Lingkup (Pasal 1) | Status | Keterangan |
|----|-------------------------|--------|------------|
| 1 | Manajemen soal | Selesai | CRUD lengkap, kategori, tingkat kesulitan, upload gambar |
| 2 | Sistem ujian berbasis online | Selesai | Quiz kelas + ujian formal, timer, anti-cheat, acak urutan soal |
| 3 | Pendaftaran & monitoring | Selesai | Multi-tahap + alur khusus Kelas Online/Ujian |
| 4 | Sistem Tryout aplikasi | Selesai | Tryout gratis publik tanpa login |
| 5 | Penilaian otomatis | Selesai | Otomatis untuk PG; esai dinilai manual staff |
| 6 | Dashboard aplikasi | Selesai | Per role: admin, mentor, peserta, orang tua |
| 7 | Modul offline & online | Selesai* | Online penuh; offline operasional (tes lokasi + input jasmani) |
| 8 | Manajemen user | Selesai | CRUD, import massal, role, masa aktif |
| 9 | Sistem kelas online | Selesai | Kelas, materi, tes, aktivitas, undang peserta |
| 10 | Bank soal | Selesai* | Koleksi soal terpusat; grouping bank belum UI terpisah |
| 11 | Laporan ujian & pendaftaran | Selesai | Laporan harian/mingguan, PDF, submissions, monitoring admin |
| 12 | Email & cloud server (1 tahun) | Selesai* | Infrastruktur siap; masa aktif aplikasi sesuai program |

*\* Selesai dengan catatan teknis — dijelaskan per poin di bawah.*

---

## III. URAIAN PELAKSANAAN PER RUANG LINGKUP PEKERJAAN

### 1. Manajemen Soal
**Status: SELESAI**

Modul manajemen soal memungkinkan Admin dan Mentor untuk:

- Menambah, mengubah, dan menghapus soal melalui halaman Bank Soal
- Mengatur kategori mata pelajaran dan tingkat kesulitan (Mudah/Sedang/Sulit)
- Mendukung tipe soal pilihan ganda (A–D) dan esai
- Mengunggah gambar pendukung soal
- Mencari dan memfilter soal berdasarkan kategori/tingkat

**Halaman:** `/question-bank`  
**Endpoint API:** GET/POST/PUT/DELETE `/api/questions`

---

### 2. Sistem Ujian Berbasis Online
**Status: SELESAI**

Sistem ujian online mencakup dua jenis penilaian:

| Jenis | Akses Peserta | Fungsi |
|-------|---------------|--------|
| Quiz/Tes Kelas | `/quick-test/:id` | Tes terjadwal per kelas atau global |
| Ujian Formal | `/ujian` → `/quick-exam/:id` | Simulasi ujian gabungan lintas mata pelajaran |

**Fitur operasional:**

- Penjadwalan waktu mulai dan berakhir
- Timer durasi pengerjaan
- Satu kali submit per peserta per ujian
- Urutan soal diacak per peserta (setiap user urutan berbeda)
- Kunci jawaban disembunyikan saat ujian berlangsung
- Anti-cheat: fullscreen, blok copy-paste, deteksi pindah tab, batas pelanggaran
- Review jawaban oleh Admin/Mentor melalui halaman submissions

---

### 3. Sistem Pendaftaran dan Monitoring Pendaftaran
**Status: SELESAI**

**Alur pendaftaran peserta:**

| Program | Tahap Pendaftaran |
|---------|-------------------|
| VIP / Reguler | Administrasi → Psikologi → Kesehatan → Fisik (review admin per tahap) |
| Kelas Online | Verifikasi email → Pembayaran → Konfirmasi admin |
| Kelas Ujian | Verifikasi email → Pembayaran → Konfirmasi admin |

**Fitur:**

- Formulir pendaftaran bertahap dengan upload berkas (KTP, KK, rapor, foto)
- Berkas disimpan privat (hanya diakses via autentikasi)
- Panel admin untuk review, persetujuan, atau permintaan revisi per tahap
- Konfirmasi pembayaran khusus Kelas Online & Kelas Ujian
- Monitoring status pendaftaran seluruh peserta
- Unduh berkas PDF pendaftaran (template resmi)
- Notifikasi email dan in-app saat status berubah

**Halaman:** `/registration` (peserta), `/admin/registration` (admin)

---

### 4. Sistem Tryout Aplikasi
**Status: SELESAI**

Tryout gratis dapat diakses publik tanpa login melalui `/free-tryout`:

- Peserta mengisi data singkat (nama, jenis kelamin, kota, tanggal lahir, telepon)
- Mengerjakan soal tryout dengan timer
- Skor dihitung otomatis dan ditampilkan langsung
- Admin/Mentor dapat melihat rekap hasil tryout

**Endpoint:** `/api/free-tryout/tests` (publik)

---

### 5. Penilaian Otomatis
**Status: SELESAI**

| Tipe Soal | Penilaian |
|-----------|-----------|
| Pilihan ganda | Otomatis — skor = jumlah jawaban benar, skala 0–100 |
| Esai | Disimpan; dinilai manual oleh Admin/Mentor |

Setelah submit quiz/ujian, sistem otomatis:

- Menyimpan skor ke database
- Membuat laporan perkembangan harian
- Mengisi peringkat akademik (kategori Akademik)

Staff dapat override skor manual melalui halaman submissions bila diperlukan.

---

### 6. Dashboard Aplikasi
**Status: SELESAI**

Dashboard disesuaikan per peran pengguna:

| Role | Isi Dashboard |
|------|---------------|
| Admin | Statistik platform, daftar kelas, aktivitas terbaru, drill-down per siswa |
| Mentor | Kelas yang diampu, tes terjadwal, aktivitas kelas |
| Peserta | Kelas saya, riwayat aktivitas, grafik perkembangan nilai |
| Orang Tua | Daftar anak terhubung, laporan terbaru |

**Fitur tambahan dashboard:**

- Grafik nilai per mata pelajaran dan nilai ujian
- Progress jasmani per komponen
- Unduh PDF perkembangan siswa
- Generate laporan mingguan (admin)

**Halaman:** `/dashboard`, `/activity-history`

---

### 7. Modul Offline dan Online
**Status: SELESAI (sesuai desain operasional bimbel)**

| Aspek | Implementasi |
|-------|--------------|
| Online | Seluruh modul digital: kelas, materi, quiz, ujian, dashboard, laporan, PWA (installable) |
| Offline (operasional) | Tahap psikologi, kesehatan, dan fisik diverifikasi di lokasi tes; staff memperbarui status via admin; input nilai jasmani manual di Manajemen Nilai |
| Program Karantina (VIP) | Flag karantina untuk peserta program VIP |

*Catatan: Modul offline mengacu pada alur operasional bimbel (tes di lokasi fisik), bukan mode ujian tanpa internet di perangkat peserta.*

---

### 8. Manajemen User
**Status: SELESAI**

- CRUD pengguna dengan role: Admin, Mentor, Peserta, Orang Tua
- Penetapan program (VIP, Reguler, Kelas Online, Kelas Ujian)
- Pengaturan masa aktif aplikasi per peserta
- Import massal user via CSV/Excel
- Registrasi mandiri peserta dengan verifikasi email
- Pembatasan akses otomatis saat masa aktif habis

**Halaman:** `/users` (admin), `/profile`, `/signup`, `/login`

**Masa aktif aplikasi per program:**

| Program | Masa Aktif |
|---------|------------|
| VIP / Reguler | 1 tahun |
| Kelas Online | 6 bulan (sejak konfirmasi pembayaran) |
| Kelas Ujian | 3 bulan (sejak konfirmasi pembayaran) |

---

### 9. Sistem Kelas Online
**Status: SELESAI**

- Pembuatan kelas dengan kode, instruktur, dan periode akademik
- Menautkan materi pembelajaran (artikel/blog) ke kelas per sesi
- Menautkan quiz/tes ke kelas
- Mengundang peserta ke kelas
- Ruang kelas digital: materi, tes terjadwal, catatan aktivitas
- Kelas Ujian: peserta hanya akses ujian, tidak masuk ruang kelas

**Halaman:** `/bimble-classes` (staff), `/my-classes`, `/class/:id` (peserta)

---

### 10. Bank Soal
**Status: SELESAI (fungsi inti)**

Koleksi soal terpusat yang dapat digunakan ulang di berbagai tes dan ujian:

- Penyimpanan soal dengan kategori dan metadata
- Statistik jumlah soal per tingkat kesulitan
- Penugasan soal ke tes/ujian via Manajemen Tes

*Catatan teknis: Grouping soal ke dalam paket bank terpisah belum memiliki UI khusus; soal dikelola sebagai pool terpusat dan di-assign langsung ke definisi tes.*

---

### 11. Laporan Hasil Ujian dan Pendaftaran
**Status: SELESAI**

| Jenis Laporan | Keterangan |
|---------------|------------|
| Hasil ujian/quiz | Daftar submissions per tes, skor, review jawaban |
| Laporan harian | Manual mentor/admin atau otomatis setelah quiz/ujian |
| Ringkasan mingguan | Auto-generate dari laporan harian (Senin–Minggu WIB) |
| PDF perkembangan | Unduh oleh orang tua/admin |
| Monitoring pendaftaran | Status per tahap, konfirmasi pembayaran, berkas peserta |
| Manajemen nilai & peringkat | Peringkat akademik (otomatis) dan jasmani (input manual) — staff only |

**Halaman:** `/admin/student-reports`, `/admin/registration`, `/rankings`, submissions per tes/ujian

---

### 12. Email Server dan Cloud Server (1 Tahun)
**Status: SELESAI (infrastruktur operasional)**

| Komponen | Implementasi |
|----------|--------------|
| Email Server | Laravel Mail (SMTP) — verifikasi email, notifikasi status pendaftaran, notifikasi in-app |
| Cloud Server | Deploy Docker via Coolify; PostgreSQL produksi; storage Cloudflare R2/S3; backup terjadwal |
| Masa aktif aplikasi | VIP/Reguler: 1 tahun; Kelas Online: 6 bulan; Kelas Ujian: 3 bulan |
| Periode maintenance | Bug fixing 12 bulan hingga 31 Mei 2027 (Pasal 4) |

**Website produksi:** https://pratisthaindonesia.com

---

## IV. FITUR TAMBAHAN (DI LUAR RUANG LINGKUP MINIMAL)

Sebagai bagian dari penyempurnaan aplikasi, PIHAK PERTAMA juga menyerahkan fitur pendukung berikut:

| Fitur | Manfaat |
|-------|---------|
| Portal Orang Tua | Undangan via token; pantau perkembangan anak |
| Sertifikat Digital | Template per program; penerbitan & unduh PDF |
| Blog/Materi Publik | Halaman beranda, artikel, SEO |
| Notifikasi In-App | Pemberitahuan real-time di aplikasi |
| PWA (Progressive Web App) | Aplikasi dapat di-install di perangkat mobile |
| Multi-bahasa (ID/EN) | Toggle bahasa Indonesia/English |
| Anti-cheat ujian | Proteksi integritas pengerjaan soal |
| Kontak admin pembayaran | Profil peserta Kelas Online/Ujian |
| Pembatasan program | Kelas Ujian exam-only; alur onboarding disesederhanakan |

---

## V. SPESIFIKASI TEKNIS

| Komponen | Teknologi |
|----------|-----------|
| Backend | Laravel 12, PHP 8.2+ |
| Frontend | Vue 3, Vue Router, Pinia, Vite, Tailwind CSS 4 |
| Database | PostgreSQL (produksi), SQLite (development) |
| PDF | DomPDF |
| File Storage | Local + Cloudflare R2 (S3-compatible) |
| Deployment | Docker, Apache, Coolify |
| Keamanan | Session auth, CSRF, role middleware, verifikasi email, berkas privat |

---

## VI. KESESUAIAN WAKTU PENGERJAAN (PASAL 2)

| Milestone | Target (Perjanjian) | Keterangan |
|-----------|---------------------|------------|
| Mulai | 4 Mei 2026 | Kick-off pengembangan |
| Progress 90% | Minggu ke-3 Mei 2026 | Modul inti selesai |
| Selesai | 31 Mei 2026 | Aplikasi deploy produksi |
| Maintenance | s.d. 31 Mei 2027 | Bug fixing 12 bulan |

---

## VII. BIAYA DAN PEMBAYARAN (PASAL 3)

| Termin | Persentase | Nominal | Keterangan |
|--------|------------|---------|------------|
| DP | 30% | Rp 16.500.000 | Dibayarkan di awal |
| Termin 2 | 40% | Rp 22.000.000 | Saat progress 90% (minggu ke-3 Mei 2026) |
| Pelunasan | 30% | Rp 16.500.000 | Setelah aplikasi selesai |
| **Total** | **100%** | **Rp 55.000.000** | |

**Rekening pembayaran:** BCA 2831653011 atas nama Lulu Maulana Malik

---

## VIII. KEWAJIBAN PIHAK PERTAMA (PASAL 4)

| Kewajiban | Status |
|-----------|--------|
| Mengembangkan aplikasi sesuai kesepakatan | Telah dilaksanakan |
| Bug fixing 12 bulan (hingga 31 Mei 2027) | Aktif / berlaku |
| Menjaga kerahasiaan data | Berlaku |

---

## IX. REVISI (PASAL 5)

PIHAK KEDUA berhak mendapatkan revisi maksimal **5 (lima) kali**. Di luar jumlah tersebut, revisi dikenakan biaya tambahan sesuai kesepakatan terpisah.

---

## X. CATATAN DAN BATASAN

1. Soal esai tidak dinilai otomatis — penilaian dilakukan oleh staff Admin/Mentor.
2. Modul offline mengacu pada alur operasional tes di lokasi fisik, bukan mode ujian tanpa jaringan di perangkat peserta.
3. Bank soal berfungsi sebagai pool terpusat; grouping paket bank belum memiliki UI terpisah.
4. Revisi mengacu Pasal 5: maksimal 5 kali revisi; di luar itu biaya tambahan.

---

## XI. PENUTUP

Dengan disampaikannya laporan ini, PIHAK PERTAMA menyatakan telah menyelesaikan pekerjaan **Pembuatan Aplikasi Software Bimbel Pratistha Cendekia Prestasi (CAT Apps)** sesuai ruang lingkup Perjanjian Kerja Sama tertanggal 4 Mei 2026.

Aplikasi telah beroperasi di https://pratisthaindonesia.com dan siap digunakan PIHAK KEDUA untuk operasional bimbingan belajar.

Demikian laporan ini dibuat dengan sebenar-benarnya untuk dipergunakan sebagaimana mestinya.

---

**Bandung, 8 Juli 2026**

&nbsp;

**PIHAK PERTAMA**  
(Developer)

&nbsp;

_________________________  
**Lulu Maulana Malik**

&nbsp;

**PIHAK KEDUA**  
(Klien)

&nbsp;

_________________________  
**BJP. (P) Drs. H. Awang Anwarudin, M.H.**

---

### Lampiran

- A. Daftar halaman/modul aplikasi
- B. Panduan pengguna per role (docs/manual/)
- C. Dokumentasi deployment (docs/deploy/)
- D. Screenshot modul utama
