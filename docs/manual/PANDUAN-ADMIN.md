# Panduan Admin

Manual book untuk **administrator** Pratistha Cendekia Prestasi — pengelola penuh platform dari setup awal sampai operasional harian.

---

## 1. Peran Admin

Admin memiliki akses **penuh** ke seluruh fitur:

- Manajemen **pengguna** (admin, mentor, peserta, orang tua) — dengan **pagination** & **filter peran**
- **Bank soal**
- **Quiz/Test** (tes per mata pelajaran untuk kelas)
- **Ujian** (tes gabungan lintas mata pelajaran — modul terpisah)
- **Kelas kursus** (semua kelas)
- **Materi** belajar
- **Admin pendaftaran** (review & approve per tahap)
- **Nilai & peringkat siswa** (kategori jasmani)
- **Laporan peserta** & ringkasan mingguan
- **Undang orang tua** (berbasis **username**)
- Dashboard statistik
- **Dashboard siswa** (lihat perkembangan peserta di web)

> Menu **Sertifikat** sementara dinonaktifkan di navigasi.

---

## 2. Login & Navigasi

1. Buka **https://pratisthaindonesia.com/login**
2. Login dengan akun admin
3. Menu sidebar:

| Menu | URL | Fungsi |
|------|-----|--------|
| Dashboard | `/dashboard` | Statistik & ringkasan |
| Ujian | `/exams` | Buat & jadwalkan ujian lintas mapel |
| Kelas kursus | `/bimble-classes` | CRUD semua kelas |
| Kelola Materi | `/materials` | CRUD materi |
| Quiz/Test | `/tests` | Buat & jadwalkan quiz per mata pelajaran |
| Bank Soal | `/question-bank` | Kelola soal per kategori |
| Nilai & Peringkat Siswa | `/rankings` | Input nilai jasmani & lihat peringkat |
| Undang Orang Tua | `/admin/guardians` | Undangan wali |
| Laporan Peserta | `/admin/student-reports` | Laporan harian/mingguan |
| Pengguna | `/users` | Manajemen akun + filter peran |
| Admin pendaftaran | `/admin/registration` | Review pendaftaran peserta |

---

## 3. Alur Setup Awal (Dari Nol)

Urutan rekomendasi saat platform baru dipakai:

```
1. Buat akun mentor              → /users
2. Buat bank soal                → /question-bank
3. Buat quiz/test per mapel     → /tests
4. Buat ujian lintas mapel      → /exams (opsional, untuk pretest/posttest)
5. Buat materi                   → /materials
6. Buat kelas                    → /bimble-classes
7. Tambah peserta ke kelas       → kelola kelas
8. Lampirkan materi & quiz       → kelola kelas (jenis: Quiz)
9. Review pendaftaran            → /admin/registration
10. Input laporan & nilai jasmani → /admin/student-reports, /rankings
11. Undang orang tua             → /admin/guardians
```

---

## 4. Manajemen Pengguna

Menu **Pengguna** (`/users`)

### Fitur Daftar

- **Pencarian** nama, email, atau username
- **Filter peran**: Semua / Admin / Peserta / Mentor / Orang tua
- **Pagination** — 10 pengguna per halaman, dengan info halaman & total
- **Masa aktif** — untuk peserta (`role: user`), atur tanggal kedaluwarsa akses aplikasi

### Buat Akun Baru

1. Klik **Tambah pengguna**
2. Isi: nama, username, email, password, **peran** (admin / mentor / user / parent)
3. Untuk peserta: pilih **program category**
4. Simpan

### Edit / Hapus

- Ubah peran, program, atau masa aktif
- Hapus user jika diperlukan (hati-hati — data terkait ikut terpengaruh)

### Lihat Dashboard Siswa

Untuk peserta (`role: user`):

1. Di tabel **Pengguna**, klik **Dashboard Siswa** pada baris peserta
2. Anda diarahkan ke `/dashboard/student/{id}` — tampilan sama seperti dashboard peserta (kelas, aktivitas, perkembangan)
3. Gunakan **Kembali ke Manajemen User** untuk kembali ke daftar

> **Unduhan PDF** perkembangan dilakukan oleh **orang tua** dari halaman `/child/{id}`. Admin memantau lewat tampilan web di Dashboard Siswa.

> **Catatan:** Nomor WhatsApp dan telepon orang tua **tidak** diisi saat signup — peserta mengisinya di tahap **Administrasi** (`/registration`).

### Pembatasan Undang ke Kelas

Peserta yang **belum menyelesaikan pendaftaran** dan **masa aktif sudah kedaluwarsa** **tidak bisa** diundang ke kelas. Pastikan pendaftaran selesai atau perpanjang masa aktif sebelum menambahkan peserta.

### Peran yang Tersedia

| Peran | Keterangan |
|-------|------------|
| `admin` | Akses penuh |
| `mentor` | Kelola kelas sendiri, laporan, nilai, undang ortu |
| `user` | Peserta — daftar, pendaftaran, kelas, quiz, ujian |
| `parent` | Orang tua — dibuat via link undangan (login **username**) |

---

## 5. Bank Soal

Menu **Bank Soal** (`/question-bank`)

### Langkah Buat Soal

1. Klik **Tambah Soal**
2. Isi:
   - **Pertanyaan** (teks/gambar)
   - **Kategori** — selaras dengan kategori quiz/test (Kewarganegaraan, Math, English, dll.)
   - **Tipe** — pilihan ganda / esai
   - **Opsi jawaban** & tandai jawaban benar
   - **Tingkat kesulitian** (opsional)
3. Simpan

Soal yang sama bisa dipakai di **quiz kelas** maupun **ujian lintas mapel**.

---

## 6. Manajemen Quiz/Test

Menu **Quiz/Test** (`/tests`)

Digunakan untuk tes **per mata pelajaran** yang dilampirkan ke **kelas kursus** sebagai quiz.

### Buat Quiz/Test Baru

1. Klik **Buat tes**
2. Isi:
   - Nama tes
   - **Kategori** (wajib — mis. Math, English)
   - Durasi (menit)
   - **Jadwal** — tanggal & jam mulai/selesai
   - Opsional: centang **Tryout Gratis** untuk halaman publik `/free-tryout`
3. Simpan → **Atur soal** dari bank soal

### Fitur Tambahan

- Tes **kedaluwarsa** ditandai merah dan tidak bisa diklik
- Daftar diurutkan: tes aktif di atas, kedaluwarsa di bawah
- **Pagination** — 5 item per halaman
- **Atur soal** — kelola soal dari bank soal
- **Submisi** — halaman terpisah (`/tests/{id}/submissions`) menampilkan daftar peserta, nilai (skala 1–100), dan waktu submit

### Lampirkan ke Kelas (sebagai Quiz)

1. Buka **Kelas kursus** → **Kelola kelas**
2. Section **Assign Quiz** → pilih quiz yang sudah dibuat (jenis tetap **Quiz**)
3. Quiz yang **sudah kedaluwarsa** atau **sudah ditautkan** tidak muncul di dropdown
4. Peserta mengerjakan dari tab **Quiz kelas** di ruang kelas

---

## 7. Manajemen Ujian

Menu **Ujian** (`/exams`)

Modul **terpisah** dari Quiz/Test — untuk ujian **gabungan lintas mata pelajaran**.

### Buat Ujian Baru

1. Klik **Buat Ujian**
2. Isi:
   - Nama ujian
   - Deskripsi
   - Durasi (menit)
   - **Jadwal** — tanggal & jam mulai/selesai
3. Simpan → **Atur soal** (bisa dari berbagai kategori mapel)

> Modal ujian **tidak** memiliki field kategori maupun opsi tryout gratis.

### Duplikat Ujian

- Klik **Duplikat Ujian** pada kartu ujian
- Sistem menyalin soal yang sama dengan nama "(Duplikat)" — berguna untuk pretest/posttest dengan deskripsi berbeda

### Akses Peserta

- Peserta melihat ujian di menu **Ujian** (`/ujian`)
- Ujian hanya bisa dikerjakan sesuai jadwal aktif
- Setelah submit: nilai (1–100) masuk **Nilai Ujian** + laporan harian otomatis
- **Submisi** ujian: halaman terpisah (`/exams/{id}/submissions`)

---

## 8. Materi & Kelas

### Materi (`/materials`)

1. Buat materi dengan judul, slug, konten HTML/markdown
2. Upload cover jika perlu
3. Publish — materi siap dilampirkan ke kelas

### Kelas (`/bimble-classes`)

**Buat kelas:**
- Nama, kode, program type, periode, pengajar (mentor)

**Kelola kelas** (modal terstruktur):

1. **Tambah Peserta** — cari & tambah siswa (perhatikan aturan masa aktif/pendaftaran)
2. **Assign Materi** — lampirkan ke nomor sesi (Sesi 1, 2, 3…)
3. **Assign Quiz** — lampirkan quiz dari `/tests` (bukan ujian `/exams`)
4. **Aktivitas** — catat kegiatan (judul, tanggal, deskripsi)

---

## 9. Admin Pendaftaran

Menu **Admin pendaftaran** (`/admin/registration`)

### Cara Review

1. Cari peserta (nama/email) di tabel utama
2. Klik **Tinjau** — modal menampilkan kartu per tahap:
   - **Administrasi** (dengan data & berkas)
   - **Psikologi**
   - **Kesehatan**
   - **Fisik**

### UI Persetujuan per Tahap

Setiap kartu berisi:

| Bagian | Fungsi |
|--------|--------|
| Badge status | Disetujui / Perlu perbaikan / Menunggu |
| **Tindakan** | Tombol **Setujui** atau **Minta perbaikan** |
| **Catatan untuk peserta** | Opsional, maks. 500 karakter |
| **Simpan** | Kirim keputusan untuk tahap tersebut |

### Tahap Administrasi

- Lihat data teks & tautan berkas (hanya jika benar-benar sudah diunggah)
- Berkas: KTP, KK, rapor, pas foto, full body
- Setujui jika lengkap → peserta lanjut ke tahap Psikologi

### Tahap Offline (Psikologi, Kesehatan, Fisik)

1. Setelah tes offline selesai di lokasi, buka kartu tahap terkait
2. Pilih **Setujui** atau **Minta perbaikan** + catatan
3. Klik **Simpan**
4. Urutan wajib: Administrasi → Psikologi → Kesehatan → Fisik
5. Setelah **Fisik disetujui** → `fully_completed = true` → dashboard peserta terbuka

> **Slot berkas:** `id_document`, `kk`, `report_card`, `passport_photo`, `full_body_photo`. Unggahan baru menimpa file lama.

---

## 10. Nilai & Peringkat Siswa

Menu **Nilai & Peringkat Siswa** (`/rankings`)

### Kategori yang Aktif di UI

Saat ini halaman peringkat menampilkan kategori **Jasmani** saja:

| Subkategori | Satuan | Contoh |
|-------------|--------|--------|
| Sprint | detik | 10.5 |
| Push Up | reps | 30 |
| Pull Up | reps | 15 |
| Sit Up | reps | 40 |
| Shuttle Run | detik | — |
| Renang | detik | — |

> Nilai akademik dari quiz kelas & ujian **otomatis** masuk ke grafik perkembangan peserta (Nilai per Mata Pelajaran & Nilai Ujian) — tidak perlu input manual di halaman peringkat. Skala tampilan: **1–100**.

### Mata pelajaran yang dikenali sistem

| Mapel | Kategori soal/quiz |
|-------|-------------------|
| Kewarganegaraan | Kewarganegaraan, Citizenship, Law, Hukum |
| Matematika | Math, Mathematics, Matematika |
| Bahasa Inggris | English, Bahasa Inggris |
| Interpersonal Skill | Interpersonal Skill, Interpersonal |

### Workflow Input Jasmani

1. Pilih subkategori (mis. Sprint)
2. Pilih kelas
3. Pilih **tanggal penilaian**
4. Klik **+ Input manual** → pilih peserta → isi nilai **1–100** (bukan persentase)
5. Tanggal berbeda = **entri terpisah** (riwayat perkembangan)

---

## 11. Laporan Peserta

Sama seperti panduan mentor — admin punya akses penuh:

1. **Laporan harian manual** — judul, tanggal, ringkasan, kategori
2. **Laporan harian otomatis** — dibuat sistem saat peserta menyelesaikan quiz (kelas) atau ujian; format nilai **1–100**
3. **Ringkasan mingguan** — generate otomatis dari laporan harian; admin/mentor bisa **generate ulang**
4. Laporan tampil di dashboard peserta & orang tua (pagination & filter tanggal)
5. Admin memantau lewat **Dashboard Siswa**; orang tua **mengunduh PDF** dari halaman perkembangan ananda

---

## 12. Undang Orang Tua

1. Pastikan peserta **registrasi selesai**
2. Buka `/admin/guardians`
3. Cari peserta → isi:
   - Nama orang tua/wali
   - Hubungan (Ayah / Ibu / Wali)
   - No. WhatsApp
4. Klik **Buat Undangan** — **tidak perlu email**
5. Kirim link via WhatsApp
6. Orang tua buka link → buat akun dengan **username** & kata sandi
7. Tandai **Terkirim** → pantau **Accepted**

---

## 13. Dashboard Admin

### Dashboard utama (`/dashboard`)

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
| Perkembangan Saya | Laporan harian/mingguan, materi, grafik nilai (per mapel terpisah + tabel quiz, nilai ujian) |

> Unduhan file PDF dilakukan oleh orang tua di `/child/{id}`, bukan dari halaman admin ini.

---

## 14. Tryout Gratis (Publik)

- Halaman `/free-tryout` — tidak perlu akun
- Admin/mentor buat **quiz/test** dengan centang **Tryout Gratis** dan jadwal aktif
- Pengunjung isi form singkat → kerjakan → lihat skor

---

## 15. Operasional Harian Admin

| Waktu | Tugas |
|-------|-------|
| Pagi | Review pendaftaran baru — setujui/revisi per tahap |
| Siang | Monitor quiz & ujian berjalan, bantu mentor jika error |
| Sore | Input laporan (jika perlu), cek nilai jasmani masuk |
| Minggu | Generate ringkasan mingguan, undang ortu peserta baru |
| Bulan | Backup database, review statistik dashboard |

---

## 16. Troubleshooting Teknis

| Masalah | Solusi |
|---------|--------|
| Dropdown kelas kosong di peringkat | Pastikan migrasi DB latest; cek log Laravel |
| Email verifikasi tidak terkirim | Cek konfigurasi SMTP di `.env` |
| Upload berkas gagal | Cek permission `storage/`, `upload_max_filesize` PHP (min. 12M) |
| Berkas pendaftaran 404 | Lihat [Berkas pendaftaran (storage)](#berkas-pendaftaran-storage) |
| Peserta dashboard terkunci | Cek `fully_completed` di admin pendaftaran |
| Peserta tidak bisa diundang ke kelas | Cek masa aktif & status pendaftaran |
| Download PDF gagal (orang tua) | Pastikan From/To valid; samakan tanggal untuk 1 hari; coba browser lain |
| API error 500 | Cek `storage/logs/laravel.log`, jalankan `php artisan migrate` |

### Berkas pendaftaran (storage)

Berkas administrasi disimpan di disk **privat** (`REGISTRATION_FILESYSTEM_DISK=local` → `storage/app/private/registration/`). **Tidak** bisa dibuka lewat URL `/storage/registration/...` tanpa login.

| Item | Nilai / perintah |
|------|------------------|
| Disk privat (default) | `REGISTRATION_FILESYSTEM_DISK=local` di `.env` |
| Akses berkas | `GET /api/registration-files/{user_id}/{field}` — **wajib login** |
| Upload | `POST /api/my-registration/administration-file` — wajib login |

Field yang valid: `id_document`, `kk`, `report_card`, `passport_photo`, `full_body_photo`.

**Docker / Coolify — berkas hilang tiap redeploy:** mount volume ke `/var/www/html/storage`. Detail: [docs/deploy/COOLIFY.md](../deploy/COOLIFY.md)

### Perintah Berguna (Server)

```bash
php artisan migrate --force
php artisan config:cache
php artisan storage:link
php artisan registration:migrate-public-files --dry-run
php artisan registration:migrate-public-files
```

---

## 17. Keamanan & Best Practice

- Jangan bagikan akun admin
- Buat akun **mentor terpisah** untuk pengajar
- Backup database rutin (lihat `scripts/backup/` di repo)
- Pembayaran **hanya** ke rekening resmi BRI **1107-01-000931-56-9**
- Verifikasi identitas peserta sebelum approve pendaftaran
- Berkas administrasi **tidak publik** — hanya pemilik & admin yang login

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

*Manual ini mencakup seluruh modul aktif per Juli 2026. Perbarui jika ada fitur baru.*
