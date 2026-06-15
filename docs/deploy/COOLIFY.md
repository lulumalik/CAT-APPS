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

## 7. Upload "tersimpan" tapi folder kosong

**PostgreSQL** (path di DB) dan **volume file** adalah dua hal terpisah. Path di DB bisa ada walau file hilang.

### Langkah debug (urutan penting)

```bash
cd /var/www/html

# 1. Bisa tulis ke disk?
php artisan app:storage-diagnostic --write-test

# 2. Upload 1 berkas di browser (user 13), lalu SEGERA tanpa redeploy:
ls -la /var/www/html/storage/app/private/registration/13/

# 3. Cek DB vs file
php artisan app:storage-diagnostic --user=13
```

| Hasil | Artinya |
|-------|---------|
| `--write-test` GAGAL | Permission / disk salah — cek `REGISTRATION_FILESYSTEM_DISK=local`, `config:clear` |
| write-test OK, `ls` kosong setelah upload | Upload tidak sampai server — cek Network tab browser, `storage/logs/laravel.log` |
| `ls` ada file, hilang setelah redeploy | Coolify **ganti volume** tiap deploy — jangan hapus volume; pastikan storage tetap ter-link |
| Path di DB ada, file tidak ada | Orphan DB — upload ulang |

`php artisan` error `Could not open input file` → Anda tidak di `/var/www/html`. Selalu `cd /var/www/html` dulu.

**Tanpa artisan** (login admin di browser):

```
GET https://pratisthaindonesia.com/api/admin/storage-diagnostic?user=13
```

Response JSON: `write_test`, `volume_mounts`, `registration_private_files`, dan per-field `files` (true/false).

UI peserta: jika muncul *"Catatan lama di sistem, berkas fisik hilang"* = path masih di DB tapi file tidak ada — **pilih file lagi** untuk unggah ulang.

Path file dengan disk `local`:

```
/var/www/html/storage/app/private/registration/{user_id}/nama-file.jpg
```

Bukan di `storage/app/public/` (kecuali legacy).

## 8. Alternatif: S3 / Cloudflare R2

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
