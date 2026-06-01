# Rebuild VPS Runbook (Preserve Master DB)

1. Copy `ops/rebuild-vps-preserve-master-db.sh` to VPS and run as privileged operator.
2. Ensure these env vars exist before run: `DB_DATABASE` (and proper MySQL auth in session/my.cnf).
3. Script behavior:
   - Backup config, env, dynamic storage, and DB metadata.
   - Stop and remove non-DB app services.
   - Keep master DB foundation untouched.
   - Recreate clean target directories:
     - `/opt/asy-syifaa/erp/src`
     - `/opt/asy-syifaa/backend`
     - `/opt/asy-syifaa-app/dist`
     - `/opt/asy-syifaa/website/src`
4. Trigger GitHub Actions deploy workflows after cleanup:
   - `deploy-erp.yml`
   - `deploy-api.yml`
   - `deploy-app.yml`
   - `deploy-website.yml`
5. Reverse proxy/domain mapping to enforce:
   - `erp.asy-syifaa.com` -> ERP service
   - `api.asy-syifaa.com` -> API service
   - `app.asy-syifaa.com` -> PWA service
   - `www.asy-syifaa.com` -> website service
6. Post deploy checks:
   - ERP health endpoint (`/up`)
   - API health endpoint (`/api/health`)
   - PWA root response
   - website root + key form page

Important:
- Do not run destructive cleanup against DB directories.
- Keep old backups at least 7 days for rollback.
