# Deploy di Coolify (Dockerfile)

Project ini pakai **Dockerfile** (bukan docker-compose). Apache listen **port 80** di dalam container.

## 1. Buat aplikasi

1. Coolify → **New Resource** → **Application**
2. Connect repo Git (GitHub / GitLab / dll.)
3. **Build Pack:** `Dockerfile`
4. **Dockerfile location:** `Dockerfile` (root repo)
5. **Port:** `80` (di pengaturan aplikasi / domain Coolify)

## 2. Persistent Storage (wajib — upload tidak hilang saat redeploy)

Tanpa ini, folder `storage/` ikut hilang setiap rebuild image.

1. Buka aplikasi → tab **Persistent Storage** (atau **Storages**)
2. Tambah **Volume** (disarankan, bukan bind mount acak):
   - **Destination Path (di container):** `/var/www/html/storage`
   - **Name:** bebas, mis. `laravel-storage`
3. Deploy / redeploy

Coolify membuat volume Docker dengan nama unik (biasanya ada UUID resource). Data upload (berkas pendaftaran, materi, dll.) tetap ada meski image diganti.

> **Path container harus** `/var/www/html/storage` — sesuai `WORKDIR` di Dockerfile, **bukan** `/app/storage` (itu untuk template Nixpacks lain).

## 3. Environment variables

Set di Coolify → **Environment Variables** (runtime):

| Variable | Contoh |
|----------|--------|
| `APP_KEY` | `base64:...` |
| `APP_URL` | `https://pratisthaindonesia.com` (tanpa slash di akhir) |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `DB_CONNECTION` | `pgsql` / `mysql` |
| `DB_HOST` | host database Coolify atau eksternal |
| `DB_DATABASE` | ... |
| `DB_USERNAME` | ... |
| `DB_PASSWORD` | ... |
| `REGISTRATION_FILESYSTEM_DISK` | `local` |
| `SESSION_DRIVER` | `database` (disarankan) |

Database bisa resource terpisah di Coolify (PostgreSQL/MySQL) — pakai hostname internal yang Coolify berikan.

## 4. Perintah setelah deploy (sekali / tiap release)

Di Coolify → **Post-deployment command** (atau jalankan manual lewat **Terminal** container):

```bash
php artisan migrate --force
php artisan config:cache
php artisan registration:migrate-public-files
```

`storage:link` dan pembuatan folder storage sudah dijalankan **entrypoint** saat container start.

## 5. Berkas pendaftaran (KTP, KK, …)

- Disimpan di volume: `storage/app/private/registration/{user_id}/`
- **Tidak** bisa dibuka tanpa login lewat URL `/storage/registration/...`
- Dibuka lewat API (session login): `/api/registration-files/{user_id}/{field}`
- Hanya **pemilik akun** atau **admin**

## 6. Backup storage dari Coolify

Volume Coolify ada di server host (Docker volume). Untuk backup manual:

```bash
# Cari volume (nama mengandung UUID aplikasi)
docker volume ls | grep -i storage

# Salin isi volume ke folder backup (sesuaikan nama volume)
docker run --rm \
  -v NAMA_VOLUME_COOLIFY:/from \
  -v /opt/backups/cat-apps:/to \
  alpine sh -c "cd /from && tar czf /to/storage-$(date +%F).tar.gz ."
```

Atau set `STORAGE_SOURCE_DIR` di `scripts/backup/backup.env` ke path bind mount jika pakai bind mount di Coolify.

## 7. Troubleshooting

| Masalah | Cek |
|---------|-----|
| Upload hilang tiap deploy | Persistent Storage belum di-set ke `/var/www/html/storage` |
| Berkas 404 | `REGISTRATION_FILESYSTEM_DISK=local`, cek log: `storage/logs/laravel.log` |
| 502 Bad Gateway | Port aplikasi di Coolify = **80** |
| File lama di `public` | `php artisan registration:migrate-public-files` |

## 8. Redeploy aman

```
Git push → Coolify build image baru → container baru
         → volume /var/www/html/storage TETAP (jika Persistent Storage sudah dikonfigurasi)
```

Jangan hapus volume di Coolify kecuali sengaja reset data upload.
