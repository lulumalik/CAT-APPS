# Deploy di Coolify (Dockerfile)

Project ini pakai **Dockerfile** (bukan docker-compose). Apache listen **port 80** di dalam container.

## Penting: `VOLUME` di Dockerfile ≠ storage Coolify otomatis

Baris ini di Dockerfile:

```dockerfile
VOLUME ["/var/www/html/storage"]
```

hanya **memberi tahu Docker** bahwa folder itu tempat data. **Coolify tidak otomatis** membuat Persistent Storage dari baris ini.

Anda **wajib** menambah volume manual di UI Coolify (langkah 2). Tanpa itu, upload ada di **layer container sementara** → API bisa jalan sesaat setelah upload, tapi **hilang saat redeploy**.

File **tidak** muncul di folder git/repo di server host — hanya di dalam container (atau di Docker volume jika sudah dikonfigurasi).

---

## 1. Buat aplikasi

1. Coolify → **New Resource** → **Application**
2. Connect repo Git
3. **Build Pack:** `Dockerfile`
4. **Port:** `80`

## 2. Persistent Storage (WAJIB)

1. Buka aplikasi → **Persistent Storage** / **Storages**
2. **Add Volume**
   - **Destination Path:** `/var/www/html/storage` ← harus persis ini
   - **Name:** `laravel-storage` (bebas)
   - Tipe: **Volume** (bukan file)
3. **Save** lalu **Redeploy**

Salah path umum yang bikin volume tidak jalan:

| Salah | Benar |
|-------|--------|
| `/app/storage` | `/var/www/html/storage` |
| `/var/www/html/storage/app/private` | `/var/www/html/storage` (mount root storage saja) |
| Hanya mengandalkan `VOLUME` di Dockerfile | Tetap harus set di Coolify UI |

### Verifikasi (Coolify → Terminal container)

```bash
php artisan app:storage-diagnostic --user=13
```

Interpretasi:

| Output | Artinya |
|--------|---------|
| `TIDAK ADA mount khusus` | Volume Coolify **belum** aktif — upload akan hilang redeploy |
| Ada baris mount + `volume-check SUDAH ADA` | Volume **OK** |
| `private registration ... files: N` dengan N > 0 | Berkas ada di `storage/app/private/registration/` |
| `public registration ... files: 0` | Normal untuk disk `local` (bukan error) |

Cek manual:

```bash
ls -la /var/www/html/storage/app/private/registration/13/
grep storage /proc/mounts
docker logs <container> 2>&1 | grep entrypoint
# Harus: "persistent mount detected" — bukan WARNING
```

**Jangan** cek `storage/` di folder clone git di VPS — itu bukan tempat runtime container (kecuali pakai bind mount ke path itu).

## 3. Environment variables

| Variable | Nilai |
|----------|--------|
| `REGISTRATION_FILESYSTEM_DISK` | `local` |
| `APP_URL` | `https://pratisthaindonesia.com` (tanpa `/` di akhir) |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `DB_*` | dari database Coolify |

Path berkas pendaftaran dengan `local`:

```
/var/www/html/storage/app/private/registration/{user_id}/nama-file.jpg
```

Bukan di `storage/app/public/` (kecuali data legacy sebelum migrasi).

## 4. Post-deployment command

```bash
php artisan migrate --force
php artisan config:cache
php artisan registration:migrate-public-files
php artisan app:storage-diagnostic
```

## 5. Akses berkas

- API (login wajib): `/api/registration-files/{user_id}/{field}`
- URL `/storage/registration/...` **sengaja diblokir** (privasi)

## 6. Redeploy aman

Setelah volume benar:

```
git push → build image baru → container baru → volume /var/www/html/storage TETAP
```

Upload **setelah** volume dikonfigurasi yang akan persisten. Upload **sebelum** volume dikonfigurasi sudah hilang dan tidak bisa dipulihkan kecuali ada backup.

## 7. Alternatif: S3 / Cloudflare R2

Jika volume Coolify sulit, simpan berkas di object storage:

```env
REGISTRATION_FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...
AWS_BUCKET=...
AWS_ENDPOINT=...   # untuk R2
AWS_URL=...        # URL publik bucket jika perlu
```

Berkas tidak bergantung pada redeploy container.

## 8. Backup volume Coolify

```bash
docker volume ls | grep -i storage
docker run --rm -v NAMA_VOLUME:/from -v /opt/backups:/to alpine \
  sh -c "cd /from && tar czf /to/storage-$(date +%F).tar.gz ."
```
