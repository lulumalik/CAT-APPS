#!/usr/bin/env bash
set -Eeuo pipefail

# CAT-APPS backup script
# - Backs up PostgreSQL from a running Docker container
# - Backs up Laravel storage directory on host
# - Cleans up old backups based on retention days

timestamp="$(date +%F_%H-%M-%S)"

BACKUP_ROOT="${BACKUP_ROOT:-/opt/backups/cat-apps}"
DB_CONTAINER_NAME="${DB_CONTAINER_NAME:-}"
DB_NAME="${DB_NAME:-postgres}"
DB_USER="${DB_USER:-postgres}"
DB_PASSWORD="${DB_PASSWORD:-}"
DB_DUMP_EXTRA_ARGS="${DB_DUMP_EXTRA_ARGS:-}"
STORAGE_SOURCE_DIR="${STORAGE_SOURCE_DIR:-/var/lib/docker/volumes/cat-apps-storage/_data}"
RETENTION_DAYS="${RETENTION_DAYS:-14}"

DB_BACKUP_DIR="$BACKUP_ROOT/db"
STORAGE_BACKUP_DIR="$BACKUP_ROOT/storage"
LOG_DIR="$BACKUP_ROOT/logs"
LOG_FILE="$LOG_DIR/backup_$timestamp.log"

mkdir -p "$DB_BACKUP_DIR" "$STORAGE_BACKUP_DIR" "$LOG_DIR"

log() {
  printf '[%s] %s\n' "$(date +'%Y-%m-%d %H:%M:%S')" "$1" | tee -a "$LOG_FILE"
}

fail() {
  log "ERROR: $1"
  exit 1
}

require_command() {
  command -v "$1" >/dev/null 2>&1 || fail "Required command not found: $1"
}

validate_number() {
  [[ "$1" =~ ^[0-9]+$ ]] || fail "RETENTION_DAYS must be numeric, got: $1"
}

require_command docker
require_command gzip
require_command tar
require_command find
validate_number "$RETENTION_DAYS"

[[ -n "$DB_CONTAINER_NAME" ]] || fail "Set DB_CONTAINER_NAME (postgres container name)."
[[ -d "$STORAGE_SOURCE_DIR" ]] || fail "STORAGE_SOURCE_DIR does not exist: $STORAGE_SOURCE_DIR"

db_out="$DB_BACKUP_DIR/db_${DB_NAME}_$timestamp.sql.gz"
storage_out="$STORAGE_BACKUP_DIR/storage_$timestamp.tar.gz"

log "Starting backup process."
log "Backup root: $BACKUP_ROOT"
log "Database container: $DB_CONTAINER_NAME"
log "Storage source: $STORAGE_SOURCE_DIR"

log "Creating PostgreSQL dump..."
docker exec -e PGPASSWORD="$DB_PASSWORD" "$DB_CONTAINER_NAME" \
  pg_dump -U "$DB_USER" -d "$DB_NAME" $DB_DUMP_EXTRA_ARGS \
  | gzip -c > "$db_out"
log "Database backup created: $db_out"

log "Creating storage archive..."
tar -czf "$storage_out" -C "$STORAGE_SOURCE_DIR" .
log "Storage backup created: $storage_out"

log "Applying retention: keep $RETENTION_DAYS days"
find "$DB_BACKUP_DIR" -type f -name '*.sql.gz' -mtime +"$RETENTION_DAYS" -delete
find "$STORAGE_BACKUP_DIR" -type f -name '*.tar.gz' -mtime +"$RETENTION_DAYS" -delete
find "$LOG_DIR" -type f -name '*.log' -mtime +"$RETENTION_DAYS" -delete

log "Backup completed successfully."
