# Panduan Mentor / Pengajar

Manual book untuk **mentor** yang mengelola kelas, nilai, laporan, dan undangan orang tua di Pratistha Cendekia Prestasi.

---

## 1. Apa yang Bisa Anda Lakukan?

Sebagai mentor, Anda dapat:

- Melihat **dashboard** kelas yang Anda ampu
- **Mengelola kelas kursus** (hanya kelas Anda sendiri)
- **Menulis laporan harian** & **ringkasan mingguan** peserta
- **Input nilai manual** & melihat peringkat (jasmani & akademik)
- **Mengundang orang tua** peserta
- Melihat **tes terjadwal** dan aktivitas kelas

> Mentor **tidak** dapat: mengelola bank soal, manajemen user, sertifikat, atau admin pendaftaran (khusus admin).

---

## 2. Login & Navigasi

1. Buka **https://pratisthaindonesia.com/login**
2. Masukkan username & password yang diberikan admin
3. Setelah masuk, menu sidebar kiri menampilkan:

| Menu | Fungsi |
|------|--------|
| Dashboard | Ringkasan kelas & tes mendatang |
| Kelas kursus | Kelola kelas yang Anda ampu |
| Kelola Materi | Upload/edit materi belajar |
| Tes | Lihat daftar tes (admin buat) |
| Bank Soal | *(Admin only — mentor tidak akses)* |
| Nilai & Peringkat Siswa | Input & lihat peringkat per kelas |
| Undang Orang Tua | Buat link undangan untuk wali |
| Laporan Peserta | Tulis laporan harian & mingguan |

---

## 3. Langkah 1 — Kenali Dashboard Mentor

Dashboard menampilkan:

- **Kelas yang Diusung** — nama, kode, jumlah peserta, aktivitas terakhir
- **Test Akan Berlangsung** — tes terjadwal di kelas Anda
- **Aktivitas Kelas Terbaru** — log kegiatan

Klik **Refresh** jika data perlu dimuat ulang.

---

## 4. Langkah 2 — Kelola Kelas Kursus

1. Buka menu **Kelas kursus**
2. Anda hanya melihat kelas yang **Anda buat** atau **Anda ampu sebagai pengajar**
3. Untuk setiap kelas:

### Buat Kelas Baru

1. Klik **Buat Kelas**
2. Isi:
   - Nama kelas
   - Kode kelas (opsional — otomatis jika kosong)
   - Jenis program (VIP/Karantina, Reguler, Online, Ujian)
   - Periode (tanggal mulai & selesai)
   - Pengajar (mentor: otomatis diri sendiri)
3. Simpan

### Kelola Isi Kelas

1. Klik **Kelola kelas** pada kartu kelas
2. Di halaman kelola:
   - **Tambah/hapus peserta** (siswa)
   - **Lampirkan materi** ke sesi tertentu
   - **Lampirkan tes** ke kelas
   - **Catat aktivitas kelas** (judul, deskripsi, tanggal)

### Buka Ruang Kelas (Preview)

- Klik **Buka ruang kelas** untuk melihat tampilan yang sama seperti peserta

---

## 5. Langkah 3 — Kelola Materi

1. Buka menu **Kelola Materi**
2. Buat materi baru:
   - Judul, slug, konten (rich text)
   - Kategori, cover image
3. Materi yang sudah dibuat bisa **dilampirkan** ke kelas via halaman Kelola Kelas

---

## 6. Langkah 4 — Laporan Peserta

Menu **Laporan Peserta** (`/admin/student-reports`)

### Tulis Laporan Harian

1. **Cari peserta** — ketik nama/email/username
2. Pilih peserta dari daftar
3. Isi:
   - **Judul** (mis. "Latihan Fisik Pagi")
   - **Tanggal** (default hari ini)
   - **Ringkasan** singkat
   - Kategori: akademik, jasmani, kedisiplinan, dll.
4. Klik **Simpan Laporan Harian**
5. Laporan langsung terlihat di dashboard peserta & orang tua

### Buat Ringkasan Mingguan

1. Pilih peserta yang sama
2. Atur **mulai minggu** (opsional)
3. Klik **Buat Ringkasan**
4. Sistem menggabungkan laporan harian pekan tersebut menjadi satu ringkasan

### Lihat & Hapus Laporan

- Panel kanan menampilkan laporan tersimpan per peserta
- Klik **Muat ulang** untuk refresh
- Hapus laporan jika perlu diperbaiki (lalu buat ulang)

---

## 7. Langkah 5 — Nilai & Peringkat Siswa

Menu **Nilai & Peringkat Siswa** (`/rankings`)

### Setup

1. Pilih **kategori** di panel kiri (Jasmani / Akademik)
2. Pilih **subkategori** (mis. Sprint, Push Up, Kewarganegaraan)
3. Pilih **kelas** dari dropdown

### Lihat Peringkat

- Tabel menampilkan peringkat peserta di kelas terpilih
- Untuk **jasmani**: pilih **tanggal penilaian** di header — setiap tanggal punya peringkat sendiri
- Untuk **akademik**: peringkat dari tes CAT otomatis + input manual

### Input Nilai Manual

1. Klik **+ Input manual**
2. Cari & pilih peserta
3. Isi **nilai** dan **satuan** (mis. 10 detik)
4. Untuk jasmani: pilih **tanggal penilaian**
   - **Tanggal berbeda = entri baru** (tidak menimpa nilai tanggal lain)
5. Klik **Simpan peringkat**

### Edit / Hapus

- Entri bertanda **MANUAL** bisa di-**Ubah** atau **Hapus**
- Nilai otomatis dari tes tidak bisa dihapus dari sini — gunakan override manual jika perlu

---

## 8. Langkah 6 — Undang Orang Tua

Menu **Undang Orang Tua** (`/admin/guardians`)

### Buat Undangan

1. **Cari peserta** — hanya peserta dengan **registrasi selesai** yang muncul
2. Isi:
   - Nama orang tua/wali
   - Hubungan (Ayah / Ibu / Wali)
   - No. WhatsApp
   - Email (opsional)
3. Klik **Buat Undangan**
4. Sistem menghasilkan **link undangan**

### Kirim ke Orang Tua

1. Klik **Salin Pesan WA** — pesan siap kirim
2. Atau klik **Buka WhatsApp** langsung
3. Setelah terkirim, klik **Tandai Terkirim**

### Pantau Status

| Status | Arti |
|--------|------|
| Pending | Undangan dibuat, belum diterima |
| Sent | Sudah dikirim ke orang tua |
| Accepted | Orang tua sudah buka link & hubungkan akun |

---

## 9. Alur Kerja Harian (Rekomendasi)

```
Pagi   → Input laporan harian peserta setelah latihan
       → Input nilai jasmani (dengan tanggal hari ini)

Sore   → Cek tes terjadwal di dashboard
       → Pantau peserta mengerjakan tes di ruang kelas

Minggu → Buat ringkasan mingguan per peserta
       → Review grafik perkembangan di dashboard peserta

Bulan  → Undang orang tua peserta baru yang registrasinya selesai
```

---

## 10. FAQ — Pertanyaan Umum

**Q: Saya tidak bisa buat tes baru?**  
A: Pembuatan tes hanya oleh **admin**. Lampirkan tes yang sudah ada ke kelas Anda.

**Q: Peserta tidak muncul saat undang orang tua?**  
A: Peserta harus **menyelesaikan seluruh tahap pendaftaran** dulu.

**Q: Nilai jasmani menimpa nilai kemarin?**  
A: Tidak, jika tanggal berbeda. Pastikan pilih **tanggal penilaian** yang benar saat input.

**Q: Materi tidak muncul di ruang kelas peserta?**  
A: Pastikan materi sudah **dilampirkan** ke kelas dengan nomor sesi yang benar.

---

## 11. Kontak Admin

Jika butuh akses tambahan, pembuatan tes, atau bantuan teknis:

| Kanal | Informasi |
|-------|-----------|
| WhatsApp | +628138964488 |
| Email | administrator@pratisthaindonesia.com, admin.pratistha@gmail.com |

---

*Terima kasih telah membimbing calon taruna Pratistha Cendekia Prestasi.*
