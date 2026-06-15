#!/bin/sh
set -e

cd /var/www/html

mkdir -p \
  storage/app/private \
  storage/app/private/registration \
  storage/app/public \
  storage/app/public/registration \
  storage/framework/cache \
  storage/framework/sessions \
  storage/framework/views \
  storage/logs \
  bootstrap/cache

if [ "$(id -u)" = "0" ]; then
  chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
fi

php artisan storage:link --force >/dev/null 2>&1 || true

# Hint in container logs (Coolify / docker logs) if storage is not a separate mount
if grep -q ' /var/www/html/storage ' /proc/mounts 2>/dev/null; then
  echo "[entrypoint] storage: persistent mount detected on /var/www/html/storage"
else
  echo "[entrypoint] WARNING: no dedicated mount on /var/www/html/storage — uploads will be LOST on redeploy. Set Coolify Persistent Storage."
fi

exec "$@"
