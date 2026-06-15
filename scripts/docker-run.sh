#!/usr/bin/env sh
# Build & run CAT-APPS with persistent storage (Dockerfile only, no compose).
set -e

IMAGE_NAME="${IMAGE_NAME:-cat-apps}"
CONTAINER_NAME="${CONTAINER_NAME:-cat-apps}"
STORAGE_DIR="${STORAGE_DIR:-/data/cat-apps/storage}"
ENV_FILE="${ENV_FILE:-.env}"
HOST_PORT="${HOST_PORT:-80}"

mkdir -p "$STORAGE_DIR"

docker build -t "$IMAGE_NAME" .

docker rm -f "$CONTAINER_NAME" 2>/dev/null || true

docker run -d \
  --name "$CONTAINER_NAME" \
  --restart unless-stopped \
  -p "${HOST_PORT}:80" \
  -v "${STORAGE_DIR}:/var/www/html/storage" \
  --env-file "$ENV_FILE" \
  "$IMAGE_NAME"

echo "Running: http://localhost:${HOST_PORT}"
echo "Storage: ${STORAGE_DIR} -> /var/www/html/storage"
echo ""
echo "After first deploy:"
echo "  docker exec ${CONTAINER_NAME} php artisan migrate --force"
echo "  docker exec ${CONTAINER_NAME} php artisan config:cache"
echo "  docker exec ${CONTAINER_NAME} php artisan registration:migrate-public-files"
