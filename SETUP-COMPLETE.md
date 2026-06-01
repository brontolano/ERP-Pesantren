# Setup Complete ✓

## Status

All 4 services + infrastructure are **built and running**:

| Service | Image | Size | Status |
|---------|-------|------|--------|
| **ERP** (Laravel 13 + Filament) | erp-pesantren-erp-app | 308 MB | ✓ Running |
| **API** (Node.js Express) | asy-syifaa-api:latest | 207 MB | ✓ Running |
| **Website** (PHP FPM) | asy-syifaa-website:latest | ~350 MB | ✓ Running |
| **PWA App** (Vue 3 + Vite) | asy-syifaa-pwa:latest | 95.8 MB | ✓ Running |
| **PostgreSQL 16** | postgres:16-alpine | 71 MB | ✓ Healthy |
| **Redis 7** | redis:7-alpine | 44 MB | ✓ Healthy |
| **Traefik v3** | traefik:v3.0 | 146 MB | ✓ Running |

**Total Local Images:** ~1.5 GB

---

## Files Created

```
ERP-Pesantren/
├── docker-compose.prod.yml      # Main orchestration file
├── .env                          # Production environment (copy of .env.production)
├── .env.production               # Template for production
├── .dockerignore (x4)            # Build optimization for each service
├── DOCKER-DEPLOYMENT.md          # Comprehensive deployment guide
├── setup.sh / setup.bat          # Automated setup scripts
├── asy-syifaa-erp/
│   ├── .env                      # Laravel config (PostgreSQL, Redis)
│   └── .dockerignore
├── asy-syifaa-api/
│   ├── .env                      # Node.js config (Database, JWT)
│   ├── Dockerfile                # Multi-stage (dependencies)
│   └── .dockerignore
├── asy-syifaa-website/
│   ├── .env (auto-generated)
│   ├── Dockerfile                # PHP-FPM
│   ├── nginx.conf                # Nginx reverse proxy
│   └── .dockerignore
└── asy-syifaa-app/
    ├── Dockerfile                # Multi-stage Vue 3 build + Nginx
    └── .dockerignore
```

---

## Next Steps (For VPS Deployment)

### 1. Prepare Files for VPS

Create a deployment package:

```bash
# Exclude large node_modules, vendor, etc.
mkdir asy-syifaa-deployment

cp docker-compose.prod.yml asy-syifaa-deployment/
cp .env.production asy-syifaa-deployment/.env
cp DOCKER-DEPLOYMENT.md asy-syifaa-deployment/

# Copy each service
cp -r asy-syifaa-erp asy-syifaa-deployment/
cp -r asy-syifaa-api asy-syifaa-deployment/
cp -r asy-syifaa-website asy-syifaa-deployment/
cp -r asy-syifaa-app asy-syifaa-deployment/

# Remove unnecessary files (already built in Docker)
rm -rf asy-syifaa-deployment/*/node_modules
rm -rf asy-syifaa-deployment/*/vendor
rm -rf asy-syifaa-deployment/*/.git
```

### 2. Upload to VPS

```bash
# SCP or rsync to VPS
scp -r asy-syifaa-deployment/* root@your-vps-ip:/var/www/asy-syifaa/

# Or use rsync (faster)
rsync -avz --delete asy-syifaa-deployment/ root@your-vps-ip:/var/www/asy-syifaa/
```

### 3. SSH into VPS & Deploy

```bash
ssh root@your-vps-ip

cd /var/www/asy-syifaa

# Update .env secrets
nano .env
# Change:
# - DB_PASSWORD (strong)
# - JWT_SECRET (use: openssl rand -base64 32)
# - LETSENCRYPT_EMAIL
# - API_KEY_*

# Start stack
docker-compose -f docker-compose.prod.yml up -d

# Wait 30s for DB init
sleep 30

# Run migrations
docker-compose -f docker-compose.prod.yml exec erp-app php artisan key:generate --force
docker-compose -f docker-compose.prod.yml exec erp-app php artisan migrate --force

# Verify
docker-compose -f docker-compose.prod.yml ps
```

### 4. Configure DNS

Update your domain records to point to VPS:

```
A record: erp.asy-syifaa.com     → VPS_IP
A record: api.asy-syifaa.com     → VPS_IP
A record: www.asy-syifaa.com     → VPS_IP
A record: app.asy-syifaa.com     → VPS_IP
A record: asy-syifaa.com         → VPS_IP (root domain)
```

### 5. Test All Endpoints

Once DNS propagates (5-15 mins):

```bash
curl -I https://erp.asy-syifaa.com
curl -I https://api.asy-syifaa.com/api/v1/health
curl -I https://www.asy-syifaa.com
curl -I https://app.asy-syifaa.com
```

---

## Local Testing (Without DNS)

To test locally, edit your **hosts file**:

**Windows:** `C:\Windows\System32\drivers\etc\hosts`
**Mac/Linux:** `/etc/hosts`

Add:
```
127.0.0.1  erp.asy-syifaa.local
127.0.0.1  api.asy-syifaa.local
127.0.0.1  www.asy-syifaa.local
127.0.0.1  app.asy-syifaa.local
```

Then access via:
- http://erp.asy-syifaa.local
- http://api.asy-syifaa.local
- http://www.asy-syifaa.local
- http://app.asy-syifaa.local

(Traefik won't auto-generate SSL locally, but services work via Nginx reverse proxy)

---

## Useful Local Commands

```bash
# View all logs
docker-compose -f docker-compose.prod.yml logs -f

# View specific service logs
docker-compose -f docker-compose.prod.yml logs -f erp-app
docker-compose -f docker-compose.prod.yml logs -f api-app

# Stop all
docker-compose -f docker-compose.prod.yml down

# Stop & remove volumes (full reset)
docker-compose -f docker-compose.prod.yml down -v

# Rebuild a specific service
docker-compose -f docker-compose.prod.yml build --pull erp-app

# Execute command in container
docker-compose -f docker-compose.prod.yml exec erp-app php artisan tinker

# Database backup (local)
docker-compose -f docker-compose.prod.yml exec postgresql pg_dump -U pesantren erp_pesantren > backup.sql
```

---

## Troubleshooting

### Container keeps restarting
```bash
docker logs <container-name> --tail 50
# Check for errors and fix .env or Dockerfile
```

### Database connection refused
```bash
docker-compose -f docker-compose.prod.yml exec postgresql psql -U pesantren -d erp_pesantren -c "SELECT 1"
```

### Out of memory (OOM)
```bash
docker stats --no-stream
# If needed: docker system prune -a
```

### Traefik SSL not working
```bash
# Check Let's Encrypt store
ls -la letsencrypt/
# View traefik logs
docker logs traefik | grep -i "error\|tls"
```

---

## References

- **Deployment Guide:** `DOCKER-DEPLOYMENT.md` (in this folder)
- **Docker Compose Docs:** https://docs.docker.com/compose/
- **Traefik Docs:** https://doc.traefik.io/traefik/
- **Laravel in Docker:** https://docs.docker.com/samples/laravel/

---

## Questions?

Refer to `DOCKER-DEPLOYMENT.md` for detailed commands, or ask for help setting up on your VPS.

**Setup Status:** ✓ Complete & Ready for Production
