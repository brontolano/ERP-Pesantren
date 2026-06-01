#!/usr/bin/env bash
set -euo pipefail

# Rebuild non-DB services while preserving master DB foundation.

STAMP="$(date +%Y%m%d-%H%M%S)"
BACKUP_ROOT="/opt/asy-syifaa/backups/$STAMP"
V2_ROOT="/opt/asy-syifaa"

mkdir -p "$BACKUP_ROOT"/{configs,env,storage,meta}

echo "==> Snapshot configs"
[ -d /etc/nginx ] && cp -a /etc/nginx "$BACKUP_ROOT/configs/nginx"
[ -d /etc/caddy ] && cp -a /etc/caddy "$BACKUP_ROOT/configs/caddy"
[ -d "$V2_ROOT/erp" ] && cp -a "$V2_ROOT/erp/docker-compose.yml" "$BACKUP_ROOT/configs/erp-compose.yml" 2>/dev/null || true
[ -d /opt/asy-syifaa-app ] && cp -a /opt/asy-syifaa-app/docker-compose.yml "$BACKUP_ROOT/configs/pwa-compose.yml" 2>/dev/null || true
pm2 list > "$BACKUP_ROOT/meta/pm2-list.txt" 2>/dev/null || true
crontab -l > "$BACKUP_ROOT/meta/crontab.txt" 2>/dev/null || true

echo "==> Backup env and dynamic storage"
[ -f "$V2_ROOT/erp/.env" ] && cp -a "$V2_ROOT/erp/.env" "$BACKUP_ROOT/env/erp.env"
[ -f "$V2_ROOT/backend/.env" ] && cp -a "$V2_ROOT/backend/.env" "$BACKUP_ROOT/env/api.env"
[ -d "$V2_ROOT/erp/storage/app/public" ] && cp -a "$V2_ROOT/erp/storage/app/public" "$BACKUP_ROOT/storage/erp-public"
[ -d "$V2_ROOT/website/src/uploads" ] && cp -a "$V2_ROOT/website/src/uploads" "$BACKUP_ROOT/storage/website-uploads"

echo "==> Backup DB metadata only (schema + grants)"
# Requires DB creds available on server env.
mysqldump --no-data --routines --triggers "$DB_DATABASE" > "$BACKUP_ROOT/meta/master-schema.sql"
mysqldump --no-data mysql user db tables_priv columns_priv procs_priv > "$BACKUP_ROOT/meta/mysql-grants-meta.sql" 2>/dev/null || true

echo "==> Stop and remove old app services (non-DB)"
docker compose -f "$V2_ROOT/erp/docker-compose.yml" down || true
docker compose -f /opt/asy-syifaa-app/docker-compose.yml down || true
pm2 delete asy-syifaa-api || true

# Preserve DB master directories and credentials; cleanup old code only.
rm -rf "$V2_ROOT/erp/src" "$V2_ROOT/backend" /opt/asy-syifaa-app/dist "$V2_ROOT/website/src"
mkdir -p "$V2_ROOT/erp/src" "$V2_ROOT/backend" /opt/asy-syifaa-app/dist "$V2_ROOT/website/src"

echo "==> Rebuild prep complete. Deploy latest source via GitHub Actions now."
echo "Backup stored at: $BACKUP_ROOT"
