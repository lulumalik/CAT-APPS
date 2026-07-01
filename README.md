# Pratistha Cendekia Prestasi (CAT Apps)

Platform web untuk kursus persiapan AKPOL: pendaftaran peserta, kelas bimbel, tes CAT, peringkat internal, laporan perkembangan harian/mingguan, portal orang tua, dan sertifikat.

**Website produksi:** [pratisthaindonesia.com](https://pratisthaindonesia.com)

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
| `user` | Peserta — pendaftaran, kelas, tes, dashboard perkembangan |
| `parent` | Orang tua — pantau perkembangan ananda yang terhubung |
| `mentor` | Pengajar — kelola kelas, nilai, laporan harian |
| `admin` | Administrator penuh — user, tes, sertifikat, undang ortu, backup generate mingguan |

Panduan lengkap per peran: **[docs/manual/README.md](docs/manual/README.md)**

---

## Fitur utama

- **Pendaftaran multi-tahap** — administrasi, psikologi, kesehatan, fisik (berkas privat, review admin)
- **Kelas bimbel** — materi, tes, aktivitas kelas
- **Bank soal & manajemen tes** — termasuk free tryout publik
- **Peringkat internal** — nilai akademik & jasmani (staff only)
- **Laporan perkembangan** — harian + ringkasan mingguan otomatis
- **Portal orang tua** — undangan via token, lihat perkembangan & unduh PDF
- **Sertifikat** — template per program, penerbitan admin
- **Blog/materi publik** — halaman beranda & artikel
- **Notifikasi in-app** — laporan baru, nilai tes, dll.
- **PWA** — installable via Vite PWA plugin

### Laporan harian & ringkasan mingguan

| Tipe | `type` di DB | Keterangan |
|------|--------------|------------|
| Harian | `daily` | Dibuat manual mentor/admin, atau otomatis dari tes & input jasmani |
| Mingguan | `weekly_summary` | Diringkas dari laporan harian dalam satu minggu (Senin–Minggu, WIB) |

**Ringkasan mingguan ter-generate otomatis** ketika:

1. Ada **minimal 1 laporan harian** di minggu tersebut, dan
2. Terjadi salah satu trigger:
   - Laporan harian baru disimpan (`POST /api/student-reports`)
   - Tes selesai / nilai jasmani diinput (via `AutoStudentReportService`)
   - Halaman perkembangan dibuka (`GET /api/students/{id}/reports`) — backfill minggu yang belum punya ringkasan
   - Daftar laporan peserta dibuka (`GET /api/student-reports?student_id=...`)

Tidak ada cron terpisah. Jika mingguan belum muncul padahal sudah ada laporan harian, admin bisa generate manual dari dashboard siswa (`/dashboard/student/{id}`) — fitur **Generate minggu** hanya tampil untuk admin.

API terkait:

```
GET  /api/student-reports              # daftar laporan (staff)
POST /api/student-reports              # tulis laporan harian (staff)
POST /api/student-reports/weekly       # generate/regenerate mingguan (staff; UI manual admin-only)
GET  /api/students/{id}/reports        # laporan harian + mingguan untuk panel perkembangan
GET  /api/students/{id}/pdf            # unduh PDF dashboard
```

---

## Struktur penting

```
app/
  Http/Controllers/     # API & logic HTTP
  Services/             # WeeklyStudentReportService, AutoStudentReportService, dll.
resources/js/
  views/                # Halaman Vue
  components/           # StudentProgressPanel, ProgressChart, dll.
  router/index.js       # Route & guard per role
routes/api.php          # Semua endpoint REST
docs/
  manual/               # Panduan user, mentor, admin, orang tua
  deploy/COOLIFY.md     # Deploy production
scripts/
  backup/               # Backup DB + storage
  docker-run.sh         # Helper run container
```

---

## Pengembangan lokal

### Prasyarat

- PHP 8.2+ (ekstensi: pdo, mbstring, openssl, tokenizer, xml, ctype, json, fileinfo, gd)
- Composer
- Node.js 20+
- SQLite (default) atau PostgreSQL/MySQL

Di Windows, jika ekstensi PHP kurang, project menyediakan `php-additional-ini/`:

```powershell
$env:PHP_INI_SCAN_DIR = "$PWD\php-additional-ini"
composer install
```

### Setup

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
npm install
```

Jalankan (dua terminal):

```bash
npm run dev
php artisan serve
```

Buka `http://localhost:8000`.

Env lokal umum: `DB_CONNECTION=sqlite`, `REGISTRATION_FILESYSTEM_DISK=local`, `MAIL_MAILER=log`.

---

## Deploy production

### Coolify (disarankan)

Pakai **Build Pack: Dockerfile**, port **80**, dan **Persistent Storage** wajib:

| Setting | Nilai |
|---------|--------|
| Build Pack | Dockerfile |
| Port | `80` |
| Persistent Storage → Destination | `/var/www/html/storage` |

Panduan lengkap: **[docs/deploy/COOLIFY.md](docs/deploy/COOLIFY.md)**

Post-deploy (Coolify → Post-deployment command atau Terminal):

```bash
php artisan migrate --force
php artisan config:cache
php artisan registration:migrate-public-files
```

Env penting: `REGISTRATION_FILESYSTEM_DISK=local`, `APP_URL` tanpa trailing slash.

### Docker manual (tanpa Coolify)

Build image:

```bash
docker build -t cat-apps .
```

Jalankan container — **wajib** mount `storage` agar upload tidak hilang saat rebuild:

```bash
mkdir -p /data/cat-apps/storage

docker run -d \
  --name cat-apps \
  --restart unless-stopped \
  -p 80:80 \
  -v /data/cat-apps/storage:/var/www/html/storage \
  --env-file .env \
  cat-apps
```

Atau pakai helper script:

```bash
chmod +x scripts/docker-run.sh
STORAGE_DIR=/data/cat-apps/storage ENV_FILE=.env ./scripts/docker-run.sh
```

Setelah container jalan (sekali):

```bash
docker exec cat-apps php artisan migrate --force
docker exec cat-apps php artisan config:cache
docker exec cat-apps php artisan registration:migrate-public-files
```

Update deploy (rebuild image, storage tetap di host):

```bash
docker build -t cat-apps .
docker rm -f cat-apps
docker run -d \
  --name cat-apps \
  --restart unless-stopped \
  -p 80:80 \
  -v /data/cat-apps/storage:/var/www/html/storage \
  --env-file .env \
  cat-apps
```

Samakan `/data/cat-apps/storage` dengan `STORAGE_SOURCE_DIR` di `scripts/backup/backup.env`.

---

## Backup (VPS)

Script backup di `scripts/backup/backup.sh`.

Yang di-backup:

- PostgreSQL dump (`.sql.gz`) dari container database
- Folder `storage` Laravel di host (`.tar.gz`)

Langkah setup:

1. Salin template env backup:
   ```bash
   cp scripts/backup/backup.env.example scripts/backup/backup.env
   ```
2. Edit `scripts/backup/backup.env` — isi minimal `DB_CONTAINER_NAME`, `DB_PASSWORD`, `STORAGE_SOURCE_DIR`
3. Buat executable:
   ```bash
   chmod +x scripts/backup/backup.sh
   ```
4. Test manual:
   ```bash
   set -a; source scripts/backup/backup.env; set +a
   ./scripts/backup/backup.sh
   ```

Contoh cron harian jam 02:00:

```
0 2 * * * cd /path/to/CAT-APPS && set -a && source scripts/backup/backup.env && set +a && ./scripts/backup/backup.sh >> /var/log/catapps-backup-cron.log 2>&1
```

Catatan:

- Simpan backup offsite (S3/R2/B2/server lain), jangan hanya di VPS yang sama.
- Gunakan `RETENTION_DAYS` di `backup.env` untuk mengatur retensi.

---

## Testing

```bash
php artisan test
```

---

## Dokumentasi

| Dokumen | Isi |
|---------|-----|
| [docs/manual/README.md](docs/manual/README.md) | Indeks panduan & matriks hak akses |
| [docs/manual/PANDUAN-USER.md](docs/manual/PANDUAN-USER.md) | Peserta |
| [docs/manual/PANDUAN-ORANG-TUA.md](docs/manual/PANDUAN-ORANG-TUA.md) | Orang tua |
| [docs/manual/PANDUAN-MENTOR.md](docs/manual/PANDUAN-MENTOR.md) | Mentor |
| [docs/manual/PANDUAN-ADMIN.md](docs/manual/PANDUAN-ADMIN.md) | Admin |
| [docs/deploy/COOLIFY.md](docs/deploy/COOLIFY.md) | Deploy Docker/Coolify |

---

## Lisensi

MIT (Laravel framework). Hak konten & branding Pratistha Training Center Indonesia.
