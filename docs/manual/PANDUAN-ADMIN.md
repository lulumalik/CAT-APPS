# Panduan Admin

Manual book untuk **administrator** Pratistha Cendekia Prestasi — pengelola penuh platform dari setup awal sampai operasional harian.

---

## 1. Peran Admin

Admin memiliki akses **penuh** ke seluruh fitur:

- Manajemen **user** (admin, mentor, peserta)
- **Bank soal** & **manajemen tes**
- **Kelas kursus** (semua kelas)
- **Materi** belajar
- **Admin pendaftaran** (review & approve tahap peserta)
- **Nilai & peringkat siswa**
- **Laporan peserta** & ringkasan mingguan
- **Undang orang tua**
- **Sertifikat**
- Dashboard statistik
- **Dashboard siswa** (lihat & unduh laporan PDF per peserta)

---

## 2. Login & Navigasi

1. Buka **https://pratisthaindonesia.com/login**
2. Login dengan akun admin
3. Menu sidebar:

| Menu | URL | Fungsi |
|------|-----|--------|
| Dashboard | `/dashboard` | Statistik & ringkasan |
| Kelas kursus | `/bimble-classes` | CRUD semua kelas |
| Kelola Materi | `/materials` | CRUD materi |
| Tes | `/tests` | Buat & jadwalkan tes |
| Bank Soal | `/question-bank` | Kelola soal per kategori |
| Nilai & Peringkat Siswa | `/rankings` | Input nilai & lihat peringkat |
| Undang Orang Tua | `/admin/guardians` | Undangan wali |
| Laporan Peserta | `/admin/student-reports` | Laporan harian/mingguan |
| Pengguna | `/users` | Manajemen akun |
| Sertifikat | `/admin/certificates` | Template & penerbitan |
| Admin pendaftaran | `/admin/registration` | Review pendaftaran peserta |

---

## 3. Alur Setup Awal (Dari Nol)

Urutan rekomendasi saat platform baru dipakai:

```
1. Buat akun mentor        → /users
2. Buat bank soal          → /question-bank
3. Buat tes                → /tests
4. Buat materi             → /materials
5. Buat kelas              → /bimble-classes
6. Tambah peserta ke kelas   → kelola kelas
7. Lampirkan materi & tes  → kelola kelas
8. Review pendaftaran      → /admin/registration
9. Input laporan & nilai   → /admin/student-reports, /rankings
10. Undang orang tua       → /admin/guardians
```

---

## 4. Manajemen Pengguna

Menu **Pengguna** (`/users`)

### Buat Akun Baru

1. Klik **Tambah User**
2. Isi: nama, username, email, password, **role** (admin / mentor / user)
3. Untuk peserta: pilih **program category**
4. Simpan

### Edit / Nonaktifkan

- Ubah role, program, atau reset data
- Hapus user jika diperlukan (hati-hati — data terkait ikut terpengaruh)

### Lihat Dashboard Siswa

Untuk peserta (`role: user`):

1. Di tabel **Pengguna**, klik **Dashboard Siswa** pada baris peserta
2. Anda diarahkan ke `/dashboard/student/{id}` — tampilan sama seperti dashboard peserta:
   - Kelas & aktivitas
   - Perkembangan (laporan harian/mingguan, materi, grafik nilai)
3. Klik **Download PDF** untuk menyimpan laporan lengkap (nama file memakai nama peserta)
4. Gunakan **Kembali ke Manajemen User** untuk kembali ke daftar pengguna

> **Catatan:** Nomor WhatsApp dan telepon orang tua **tidak** diisi saat signup — peserta mengisinya di tahap **Administrasi** (`/registration`).

### Role yang Tersedia

| Role | Keterangan |
|------|------------|
| `admin` | Akses penuh |
| `mentor` | Kelola kelas sendiri, laporan, nilai, undang ortu |
| `user` | Peserta — daftar, pendaftaran, kelas, tes |
| `parent` | Orang tua — dibuat via link undangan |

---

## 5. Bank Soal

Menu **Bank Soal** (`/question-bank`)

### Langkah Buat Soal

1. Klik **Tambah Soal**
2. Isi:
   - **Pertanyaan** (teks/gambar)
   - **Kategori** — harus selaras dengan kategori tes (Kewarganegaraan, Matematika, dll.)
   - **Tipe** — pilihan ganda / benar-salah
   - **Opsi jawaban** & tandai jawaban benar
   - **Tingkat kesulitan** (opsional)
3. Simpan

### Tips

- Kelompokkan soal per **kategori** sesuai config peringkat akademik
- Review soal sebelum masuk ke tes production

---

## 6. Manajemen Tes

Menu **Tes** (`/tests`)

### Buat Tes Baru

1. Klik **Buat Tes**
2. Isi:
   - Nama tes
   - **Kategori** (penting — dipakai peringkat akademik)
   - Durasi (menit)
   - **Jadwal** — tanggal & jam mulai/selesai
   - Pilih soal dari bank soal
3. Simpan

### Lampirkan ke Kelas

1. Buka **Kelas kursus** → **Kelola kelas**
2. Tab/section **Tes** → tambahkan tes yang sudah dibuat
3. Peserta hanya bisa mengerjakan saat jadwal **aktif**

### Pantau Hasil

- Nilai otomatis masuk ke **peringkat akademik** dan grafik peserta
- Lihat submission via dashboard atau modul terkait

---

## 7. Materi & Kelas

### Materi (`/materials`)

1. Buat materi dengan judul, slug, konten HTML/markdown
2. Upload cover jika perlu
3. Publish — materi siap dilampirkan ke kelas

### Kelas (`/bimble-classes`)

**Buat kelas:**
- Nama, kode, program type, periode, pengajar (mentor)

**Kelola kelas:**
- **Peserta** — tambah/hapus siswa dari kelas
- **Materi** — assign ke nomor sesi (Sesi 1, 2, 3…)
- **Tes** — lampirkan tes CBT
- **Aktivitas** — catat kegiatan (judul, tanggal, deskripsi)

---

## 8. Admin Pendaftaran

Menu **Admin pendaftaran** (`/admin/registration`)

### Review Tahap Administrasi

1. Cari peserta (nama/email)
2. Buka detail — lihat data & berkas upload:
   - KTP, KK, rapor, pas foto, full body
   - WhatsApp, telepon ortu, alamat, gender, TB/BB
3. **Periksa kelengkapan berkas secara manual** — sistem tidak memblokir peserta yang belum mengunggah semua dokumen
4. Klik tautan berkas untuk melihat (harus login sebagai admin); berkas dilayani lewat API internal
5. Putuskan:
   - **Approved** — lanjut ke tahap berikutnya (pastikan dokumen sudah memadai)
   - **Revision requested** — tulis catatan jelas (berkas/data mana yang kurang), minta perbaikan
   - **Rejected** — jika tidak memenuhi syarat

> **Slot berkas:** `id_document` (KTP), `kk`, `report_card`, `passport_photo`, `full_body_photo`. Peserta dapat mengganti berkas kapan saja sebelum disetujui — unggahan baru menimpa file lama.

### Tahap Offline (Psikologi, Kesehatan, Fisik)

1. Setelah tes offline selesai di lokasi, update status peserta:
   - Pilih tahap → **Approved** atau **Revision**
2. Urutan wajib: Administrasi → Psikologi → Kesehatan → Fisik
3. Setelah **Fisik approved** → `fully_completed = true` → dashboard peserta terbuka

### Input Nilai Fisik (Opsional)

- Nilai jasmani dari tes offline bisa diinput via **Nilai & Peringkat** atau langsung di data fisik registrasi

---

## 9. Nilai & Peringkat Siswa

Menu **Nilai & Peringkat Siswa** (`/rankings`)

### Kategori

| Grup | Sumber Nilai | Contoh Subkategori |
|------|--------------|-------------------|
| **Jasmani** | Input manual (+ data registrasi) | Sprint, Push Up, Pull Up |
| **Akademik** | Tes CAT otomatis + manual | Kewarganegaraan, Matematika, Bahasa |

### Workflow Input Jasmani

1. Pilih subkategori (mis. Sprint)
2. Pilih kelas
3. Pilih **tanggal penilaian**
4. Klik **+ Input manual** → pilih peserta → isi nilai
5. Tanggal berbeda = **entri terpisah** (riwayat perkembangan)

### Workflow Akademik

- Nilai otomatis dari tes selesai
- Override manual jika perlu koreksi

---

## 10. Laporan Peserta

Sama seperti panduan mentor — admin punya akses penuh:

1. **Laporan harian** — judul, tanggal, ringkasan, kategori
2. **Ringkasan mingguan** — generate otomatis dari laporan harian
3. Laporan tampil di dashboard peserta & orang tua (dengan pagination & filter tanggal)
4. Admin dapat membuka **Dashboard Siswa** dari menu Pengguna dan **mengunduh PDF** laporan lengkap

---

## 11. Undang Orang Tua

1. Pastikan peserta **registrasi selesai**
2. Buat undangan di `/admin/guardians`
3. Kirim link via WhatsApp
4. Tandai **Ter kirim** → pantau **Accepted**

---

## 12. Sertifikat

Menu **Sertifikat** (`/admin/certificates`)

1. **Template** — edit desain sertifikat per program
2. **Terbitkan** — pilih peserta & program → generate sertifikat
3. **Riwayat** — lihat sertifikat yang sudah diterbitkan

---

## 13. Dashboard Admin

### Dashboard utama (`/dashboard`)

Menampilkan statistik:

| Metrik | Keterangan |
|--------|------------|
| Total Soal | Jumlah soal di bank |
| Peserta Terdaftar | User role peserta |
| Peserta Diterima | Registrasi fully completed |
| Kelas Dibuat | Total kelas |
| Daftar Kelas | Ringkasan per kelas |
| Aktivitas Terbaru | Log aktivitas semua kelas |

### Dashboard siswa (`/dashboard/student/{id}`)

Akses dari menu **Pengguna** → **Dashboard Siswa**:

| Bagian | Keterangan |
|--------|------------|
| Kelas Saya | Kelas yang diikuti peserta |
| Aktivitas Kelas | Log aktivitas terbaru |
| Perkembangan Saya | Laporan harian/mingguan, materi, grafik nilai |
| Download PDF | Unduh laporan dashboard peserta sebagai file PDF |

---

## 14. Tryout Gratis (Publik)

- Halaman `/free-tryout` — tidak perlu admin setup khusus
- Admin/mentor buat **tes tryout** dengan jadwal aktif
- Pengunjung isi form singkat → kerjakan → lihat skor

---

## 15. Operasional Harian Admin

| Waktu | Tugas |
|-------|-------|
| Pagi | Review pendaftaran baru, approve/revisi berkas |
| Siang | Monitor tes berjalan, bantu mentor jika error |
| Sore | Input laporan (jika perlu), cek nilai masuk |
| Minggu | Generate ringkasan mingguan, undang ortu peserta baru |
| Bulan | Backup database, review statistik dashboard |

---

## 16. Troubleshooting Teknis

| Masalah | Solusi |
|---------|--------|
| Dropdown kelas kosong di peringkat | Pastikan migrasi DB latest; cek log Laravel |
| Email verifikasi tidak terkirim | Cek konfigurasi SMTP di `.env` |
| Upload berkas gagal | Cek permission folder `storage/` (`chmod -R ug+rw storage bootstrap/cache`), `upload_max_filesize` & `post_max_size` PHP (min. 12M per berkas), `client_max_body_size` Nginx/Apache |
| Berkas pendaftaran 404 / tidak tampil | Lihat [Berkas pendaftaran (storage)](#berkas-pendaftaran-storage) di bawah |
| Peserta dashboard terkunci | Cek `fully_completed` di admin pendaftaran |
| Download PDF gagal | Pastikan build frontend terbaru; coba refresh halaman lalu unduh lagi |
| API error 500 | Cek `storage/logs/laravel.log`, jalankan `php artisan migrate` |

### Berkas pendaftaran (storage)

Berkas administrasi disimpan di disk **privat** (`REGISTRATION_FILESYSTEM_DISK=local` → `storage/app/private/registration/`). **Tidak** bisa dibuka lewat URL `/storage/registration/...` tanpa login.

| Item | Nilai / perintah |
|------|------------------|
| Disk privat (default) | `REGISTRATION_FILESYSTEM_DISK=local` di `.env` |
| Akses berkas | `GET /api/registration-files/{user_id}/{field}` — **wajib login** (pemilik atau admin) |
| Upload | `POST /api/my-registration/administration-file` — wajib login |
| Blokir URL publik | Apache: `public/.htaccess` · Nginx: `location ^~ /storage/registration/` |

**Yang boleh melihat berkas:** peserta pemilik akun, atau admin (session login aktif). Orang lain yang menebak URL mendapat **403/404**.

**Peserta lama (berkas masih di `storage/app/public/registration/`):**

1. API tetap bisa membuka berkas legacy (dicari di disk `public` lalu `local`).
2. Saat admin/peserta **klik Lihat berkas**, file otomatis dipindah ke disk privat (jika `REGISTRATION_FILESYSTEM_DISK=local`).
3. Migrasi massal sekali jalan di server:
   ```bash
   php artisan registration:migrate-public-files --dry-run   # cek dulu
   php artisan registration:migrate-public-files             # pindahkan semua
   ```
4. Setelah migrasi, salinan di folder `public/registration/` dihapus — URL `/storage/registration/...` tidak berisi file lagi.

**Jika berkas 404:**

1. Pastikan `REGISTRATION_FILESYSTEM_DISK=local` (atau `public` hanya untuk file lama).
2. Cek file ada: `ls -la storage/app/private/registration/{id}/` (atau `storage/app/public/...` jika legacy).
3. Cek log: `grep "Registration file missing" storage/logs/laravel.log`
4. Jika folder kosong tetapi path ada di database → minta peserta **unggah ulang**.
5. Backup rutin folder `storage/` — lihat `scripts/backup/` di repo.

**API upload (referensi teknis):**

| Aksi | Method & path |
|------|----------------|
| Unggah satu berkas | `POST /api/my-registration/administration-file` |
| Kirim data administrasi | `POST /api/my-registration` |
| Buka berkas | `GET /api/registration-files/{user}/{field}` |

Field yang valid: `id_document`, `kk`, `report_card`, `passport_photo`, `full_body_photo`.

### Perintah Berguna (Server)

```bash
php artisan migrate --force
php artisan config:cache
php artisan storage:link
php artisan registration:migrate-public-files --dry-run
php artisan registration:migrate-public-files
php artisan route:cache   # setelah update route; jalankan route:clear jika route baru belum aktif
```

---

## 17. Keamanan & Best Practice

- Jangan bagikan akun admin
- Buat akun **mentor terpisah** untuk pengajar
- Backup database rutin (lihat `scripts/backup/` di repo)
- Pembayaran **hanya** ke rekening resmi BRI **1107-01-000931-56-9**
- Verifikasi identitas peserta sebelum approve pendaftaran
- Berkas administrasi (KTP, KK, dll.) **tidak publik** — hanya pemilik & admin yang login boleh membuka via API

---

## 18. Kontak & Informasi Lembaga

| Item | Detail |
|------|--------|
| Nama | PT. Pratistha Training Center Indonesia |
| Brand | Pratistha Cendekia Prestasi |
| Website | pratisthaindonesia.com |
| Email | administrator@pratisthaindonesia.com, admin.pratistha@gmail.com |
| WhatsApp | +628138964488 |
| Alamat | Jl. Sukamaju no. 142, Cipadung Kulon, Panyileukan, Bandung 40614 |
| Rekening BRI | 1107-01-000931-56-9 |

---

*Manual ini mencakup seluruh modul aktif per Juni 2026. Perbarui jika ada fitur baru.*
