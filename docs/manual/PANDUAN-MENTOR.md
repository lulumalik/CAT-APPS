# Panduan Mentor / Pengajar

Manual book untuk **mentor** yang mengelola kelas, nilai, laporan, dan undangan orang tua di Pratistha Cendekia Prestasi.

---

## 1. Apa yang Bisa Anda Lakukan?

Sebagai mentor, Anda dapat:

- Melihat **dashboard** kelas yang Anda ampu
- **Mengelola kelas kursus** (hanya kelas Anda sendiri)
- **Menulis laporan harian** & **ringkasan mingguan** peserta
- **Input nilai jasmani manual** & melihat peringkat
- **Mengundang orang tua** peserta (login berbasis **username**)
- Membuat & menjadwalkan **quiz/test** dan **ujian**
- Melihat aktivitas kelas & quiz terjadwal

> Mentor **tidak** dapat: manajemen user, review pendaftaran (khusus admin).

---

## 2. Login & Navigasi

1. Buka **https://pratisthaindonesia.com/login**
2. Masukkan username & password yang diberikan admin
3. Menu sidebar kiri:

| Menu | URL | Fungsi |
|------|-----|--------|
| Dashboard | `/dashboard` | Ringkasan kelas & tes mendatang |
| Ujian | `/exams` | Buat & kelola ujian lintas mapel |
| Kelas kursus | `/bimble-classes` | Kelola kelas yang Anda ampu |
| Kelola Materi | `/materials` | Upload/edit materi belajar |
| Quiz/Test | `/tests` | Buat quiz per mata pelajaran untuk kelas |
| Bank Soal | `/question-bank` | Kelola soal |
| Nilai & Peringkat Siswa | `/rankings` | Input nilai jasmani & lihat peringkat |
| Undang Orang Tua | `/admin/guardians` | Buat link undangan untuk wali |
| Laporan Peserta | `/admin/student-reports` | Tulis laporan harian & mingguan |

---

## 3. Langkah 1 — Kenali Dashboard Mentor

Dashboard menampilkan:

- **Kelas yang Diusung** — nama, kode, jumlah peserta, aktivitas terakhir
- **Test Akan Berlangsung** — quiz/ujian terjadwal
- **Aktivitas Kelas Terbaru** — log kegiatan

Klik **Refresh** jika data perlu dimuat ulang.

---

## 4. Langkah 2 — Kelola Kelas Kursus

1. Buka menu **Kelas kursus**
2. Anda hanya melihat kelas yang **Anda buat** atau **Anda ampu sebagai pengajar**

### Buat Kelas Baru

1. Klik **Buat Kelas**
2. Isi: nama, kode (opsional), jenis program, periode, pengajar
3. Simpan

### Kelola Isi Kelas

Klik **Kelola kelas** — modal terstruktur dalam 3 bagian:

| Bagian | Fungsi |
|--------|--------|
| **1) Tambah Peserta** | Cari & tambah siswa ke kelas |
| **2) Assign Materi** | Lampirkan materi ke sesi tertentu |
| **3) Assign Quiz** | Lampirkan quiz dari `/tests` (jenis: **Quiz**); quiz kedaluwarsa/sudah ditaut tidak muncul di dropdown |

> **Penting:** Peserta yang **belum selesai pendaftaran** dan **masa aktif sudah kedaluwarsa** tidak bisa ditambahkan ke kelas.

### Buka Ruang Kelas (Preview)

- Klik **Buka ruang kelas** untuk melihat tampilan yang sama seperti peserta
- Tab **Quiz kelas** menampilkan quiz yang dilampirkan (bukan ujian lintas mapel)

---

## 5. Langkah 3 — Quiz/Test vs Ujian

### Quiz/Test (`/tests`)

- Untuk tes **per mata pelajaran**
- Dilampirkan ke **kelas** sebagai quiz
- Wajib pilih **kategori** (Math, English, dll.)
- Bisa dijadikan **Tryout Gratis** untuk halaman publik

**Alur:** Buat quiz → Atur soal → Lampirkan ke kelas via **Assign Quiz** → Peserta submit → nilai otomatis masuk perkembangan

- Setelah peserta menyelesaikan quiz: **laporan harian otomatis** + grafik **Nilai per Mata Pelajaran** (mapel sesuai kategori quiz)
- Lihat hasil di **Submisi** (`/tests/{id}/submissions`) — nilai skala 1–100

### Ujian (`/exams`)

- Untuk tes **gabungan lintas mata pelajaran**
- Peserta mengakses lewat menu **Ujian** (`/ujian`), bukan dari kelas
- Tanpa field kategori & tanpa opsi tryout
- Fitur **Duplikat Ujian** untuk membuat pretest/posttest dengan soal sama

**Alur:** Buat ujian → Atur soal (dari berbagai kategori) → Peserta kerjakan di menu Ujian → nilai masuk **Nilai Ujian**

- Lihat submisi di `/exams/{id}/submissions`

### Tips Umum

- Tes **kedaluwarsa** ditandai merah dan tidak bisa dibuka
- Daftar quiz di `/tests` memakai pagination (5 per halaman)

---

## 6. Langkah 4 — Kelola Materi

1. Buka menu **Kelola Materi**
2. Buat materi baru: judul, slug, konten, kategori, cover
3. Lampirkan ke kelas via **Assign Materi** di halaman Kelola Kelas

---

## 7. Langkah 5 — Laporan Peserta

Menu **Laporan Peserta** (`/admin/student-reports`)

### Tulis Laporan Harian

1. **Cari peserta** — ketik nama/email/username
2. Pilih peserta
3. Isi judul, tanggal, ringkasan, kategori (akademik, jasmani, kedisiplinan, dll.)
4. Klik **Simpan Laporan Harian**

> Selain laporan manual, sistem otomatis membuat laporan harian saat peserta **menyelesaikan quiz kelas** atau **ujian** (nilai skala 1–100).

### Buat Ringkasan Mingguan

1. Pilih peserta yang sama
2. Atur **mulai minggu** (opsional) atau gunakan **Generate minggu** di dashboard peserta (admin)
3. Klik **Buat Ringkasan** / **Generate ulang** jika perlu perbarui narasi pekan

### Lihat & Hapus Laporan

- Panel kanan menampilkan laporan tersimpan per peserta
- Hapus laporan jika perlu diperbaiki (lalu buat ulang)

---

## 8. Langkah 6 — Nilai & Peringkat Siswa

Menu **Nilai & Peringkat Siswa** (`/rankings`)

### Kategori Aktif

Halaman ini menampilkan kategori **Jasmani** (Sprint, Push Up, Pull Up, Sit Up, Shuttle Run, Renang).

> Nilai akademik dari quiz kelas & ujian otomatis masuk grafik perkembangan peserta (**Nilai per Mata Pelajaran** — grafik terpisah per mapel + tabel detail; **Nilai Ujian**). Skala tampilan: **1–100**, bukan persentase.

### Input Nilai Manual

1. Pilih subkategori (mis. Sprint)
2. Pilih **kelas**
3. Pilih **tanggal penilaian**
4. Klik **+ Input manual** → pilih peserta
5. Isi nilai **1–100** (bukan persentase) dan satuan
6. Klik **Simpan peringkat**
7. **Tanggal berbeda = entri baru** (tidak menimpa nilai tanggal lain)

### Edit / Hapus

- Entri bertanda **MANUAL** bisa di-**Ubah** atau **Hapus**

---

## 9. Langkah 7 — Undang Orang Tua

Menu **Undang Orang Tua** (`/admin/guardians`)

### Buat Undangan

1. **Cari peserta** — hanya peserta dengan **registrasi selesai** yang muncul
2. Isi:
   - Nama orang tua/wali
   - Hubungan (Ayah / Ibu / Wali)
   - No. WhatsApp
3. Klik **Buat Undangan** — **tidak perlu email**
4. Sistem menghasilkan **link undangan**

### Kirim ke Orang Tua

1. Klik **Salin Pesan WA** — pesan siap kirim
2. Atau klik **Buka WhatsApp** langsung
3. Setelah terkirim, klik **Tandai Terkirim**

### Yang Dilakukan Orang Tua

1. Buka link undangan
2. Isi **nama**, **username** (untuk login), dan **kata sandi**
3. Akun orang tua terhubung ke ananda

### Pantau Status

| Status | Arti |
|--------|------|
| Pending | Undangan dibuat, belum diterima |
| Sent | Sudah dikirim ke orang tua |
| Accepted | Orang tua sudah buka link & hubungkan akun |

---

## 10. Alur Kerja Harian (Rekomendasi)

```
Pagi   → Input laporan harian peserta setelah latihan
       → Input nilai jasmani (dengan tanggal hari ini)

Sore   → Cek quiz/ujian terjadwal di dashboard
       → Pantau peserta mengerjakan quiz di ruang kelas

Minggu → Buat ringkasan mingguan per peserta
       → Review grafik perkembangan di dashboard peserta

Bulan  → Undang orang tua peserta baru yang registrasinya selesai
```

---

## 11. FAQ — Pertanyaan Umum

**Q: Apa bedanya quiz di kelas dan ujian?**  
A: **Quiz** = per mata pelajaran, diakses dari **ruang kelas**. **Ujian** = gabungan lintas mapel, diakses dari menu **Ujian** peserta.

**Q: Peserta tidak muncul saat undang orang tua?**  
A: Peserta harus **menyelesaikan seluruh tahap pendaftaran** dulu.

**Q: Peserta tidak bisa ditambahkan ke kelas?**  
A: Cek apakah pendaftaran sudah selesai dan masa aktif belum kedaluwarsa.

**Q: Nilai jasmani menimpa nilai kemarin?**  
A: Tidak, jika tanggal berbeda. Pastikan pilih **tanggal penilaian** yang benar.

**Q: Quiz tidak muncul di dropdown Assign Quiz?**  
A: Quiz **kedaluwarsa** atau **sudah ditaut** ke kelas tidak ditampilkan. Buat quiz baru atau perpanjang jadwal.

**Q: Nilai quiz tidak masuk grafik perkembangan?**  
A: Pastikan quiz sudah **di-assign ke kelas** dan kategori mapel sesuai (Math, Kewarganegaraan, dll.). Tryout gratis tidak masuk grafik perkembangan.

**Q: Quiz tidak muncul di ruang kelas peserta?**  
A: Pastikan quiz sudah **di-assign** lewat **Assign Quiz** di kelola kelas.

**Q: Quiz kedaluwarsa masih bisa dikerjakan?**  
A: Tidak. Quiz kedaluwarsa ditandai merah dan tombol mulai dinonaktifkan.

---

## 12. Kontak Admin

Jika butuh akses tambahan atau bantuan teknis:

| Kanal | Informasi |
|-------|-----------|
| WhatsApp | +628138964488 |
| Email | administrator@pratisthaindonesia.com, admin.pratistha@gmail.com |

---

*Terima kasih telah membimbing calon taruna Pratistha Cendekia Prestasi.*
