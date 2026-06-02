## Install Apps
$env:PHP_INI_SCAN_DIR = "d:\Project\CAT-APPS\php-additional-ini"; composer install

cd $env:USERPROFILE\scoop\apps\mariadb\current\bin
net start MariaDB

# Pastikan MariaDB berjalan di host dan Nginx (jika ada) dikonfigurasi untuk proxy ke port 9000.

jalanin apps dev (fast untuk local Windows)
1) jalanin backend Laravel di container:
```
docker compose -f docker-compose.local.yml up -d --build app
```
2) jalanin Vite di host (bukan container) agar HMR cepat:
```
npm install
npm run dev
```

opsional (lebih lambat, hanya jika perlu): Vite di container
```
docker compose -f docker-compose.local.yml --profile docker-vite up -d vite
```

jalanin apps prod
- Build aset sekali:
```
docker compose -f docker-compose.prod.yml up -d --force-recreate app nginx
```
- Jalankan app:
```
docker compose --profile prod up -d nginx app
```

## Backup Rutin (Dockerfile / non-compose)

Project ini menyediakan script backup di `scripts/backup/backup.sh`.

Yang di-backup:
- PostgreSQL dump (`.sql.gz`) dari container database
- Folder `storage` Laravel di host (`.tar.gz`)

Langkah setup di VPS:
1. Salin template env backup:
   ```
   cp scripts/backup/backup.env.example scripts/backup/backup.env
   ```
2. Edit `scripts/backup/backup.env` dan isi minimal:
   - `DB_CONTAINER_NAME` (nama container postgres)
   - `DB_PASSWORD`
   - `STORAGE_SOURCE_DIR` (path host yang di-mount ke `/var/www/html/storage`)
3. Buat executable:
   ```
   chmod +x scripts/backup/backup.sh
   ```
4. Test manual:
   ```
   set -a; source scripts/backup/backup.env; set +a
   ./scripts/backup/backup.sh
   ```

Contoh cron harian jam 2 pagi:
```
0 2 * * * cd /path/to/CAT-APPS && set -a && source scripts/backup/backup.env && set +a && ./scripts/backup/backup.sh >> /var/log/catapps-backup-cron.log 2>&1
```

Catatan:
- Simpan backup offsite (S3/R2/B2/server lain), jangan hanya di VPS yang sama.
- Gunakan `RETENTION_DAYS` di `backup.env` untuk mengatur retensi.


## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
