# CATLab (CAT Apps)

Platform simulasi Computer Assisted Test (CAT) untuk latihan kemampuan ujian seperti JLPT/N4, TOEFL, SNMPTN, SBMPTN, JFT, dan lainnya.

---

## Tech stack

| Layer | Teknologi |
|-------|-----------|
| Backend | Laravel 12, PHP 8.2+ |
| Frontend | Vue 3, Vue Router, Pinia, Vite, Tailwind CSS 4 |
| Database | SQLite (lokal), PostgreSQL (produksi) |
| PDF | DomPDF |
| Storage | Local / S3-compatible (Cloudflare R2) |
| Deploy | Docker (Apache, port 80) |

---

## Peran pengguna

| Role | Keterangan |
|------|------------|
| `user` | Peserta — pilih minat ujian, kerjakan simulasi, tinjau hasil |
| `mentor` | Pengajar — kelola kelas, soal, dan laporan |
| `admin` | Administrator penuh — user, kategori ujian, bank soal, paket ujian |

---

## Fitur utama

- **Free tryout publik** — coba kemampuan tanpa login penuh
- **Kategori ujian** — JLPT, JFT, IELTS, TOEFL, SNMPTN, SBMPTN (dapat ditambah)
- **Bank soal & manajemen ujian** — dengan retake + tinjau jawaban
- **Google OAuth** — login tanpa verifikasi email tambahan
- **Kelas bimbel** — materi, tes, aktivitas kelas (program non–exam-only)
- **Laporan perkembangan** — harian + ringkasan mingguan
- **Sertifikat** — template per program, penerbitan admin
- **PWA** — installable via Vite PWA plugin

## Pengembangan lokal

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
npm install
npm run dev
php artisan serve
```

Env lokal umum: `DB_CONNECTION=sqlite`, `REGISTRATION_FILESYSTEM_DISK=local`, `MAIL_MAILER=log`.

## Lisensi

MIT (Laravel framework).
