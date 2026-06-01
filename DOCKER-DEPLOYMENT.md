# Docker Deployment Guide — Asy-Syifaa Pesantren

## Overview
This docker-compose setup deploys the complete Asy-Syifaa platform:
- **ERP** (Laravel + Filament) → `erp.asy-syifaa.com`
- **API** (Node.js + Express) → `api.asy-syifaa.com`
- **Website** (PHP native) → `www.asy-syifaa.com` / `asy-syifaa.com`
- **PWA App** (Vue 3) → `app.asy-syifaa.com`

All services are auto-configured with SSL (Let's Encrypt) via Traefik reverse proxy.

---

## Prerequisites

1. **VPS Requirements:**
   - Ubuntu 22.04 LTS or CentOS 8+
   - 4+ CPU cores, 8+ GB RAM
   - 50+ GB storage
   - Ports 80/443 open

2. **Install Docker & Docker Compose:**
   ```bash
   curl -fsSL https://get.docker.com -o get-docker.sh
   sudo sh get-docker.sh
   sudo usermod -aG docker $USER
   sudo curl -fsSL "https://github.com/docker/compose/releases/download/v2.26.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
   sudo chmod +x /usr/local/bin/docker-compose
   ```

3. **DNS Records:**
   Add A records pointing to your VPS IP:
   - `erp.asy-syifaa.com` → VPS_IP
   - `api.asy-syifaa.com` → VPS_IP
   - `www.asy-syifaa.com` → VPS_IP
   - `app.asy-syifaa.com` → VPS_IP

---

## Deployment Steps

### 1. Clone & Setup
```bash
git clone https://github.com/brontolano/ERP-Pesantren.git
cd ERP-Pesantren
git submodule update --init --recursive
```

### 2. Copy & Configure Environment
```bash
cp .env.production .env
# Edit .env with your secrets:
nano .env
```

Key secrets to change:
- `DB_PASSWORD` — strong password for PostgreSQL
- `JWT_SECRET` — secure random string (use `openssl rand -base64 32`)
- `LETSENCRYPT_EMAIL` — valid email for SSL renewal
- `API_KEY_*` — API keys for each service
- AWS/Mail credentials (if applicable)

### 3. Prepare Directories
```bash
mkdir -p letsencrypt backups
chmod 700 letsencrypt
```

### 4. Build All Services
```bash
docker-compose -f docker-compose.prod.yml build --pull
```

### 5. Start the Stack
```bash
docker-compose -f docker-compose.prod.yml up -d

# Wait for database initialization (~30s)
sleep 30

# Check status
docker-compose -f docker-compose.prod.yml ps
```

### 6. Initialize ERP Database
```bash
# Run migrations
docker-compose -f docker-compose.prod.yml exec erp-app php artisan migrate --force

# Seed data (optional)
docker-compose -f docker-compose.prod.yml exec erp-app php artisan db:seed

# Import Wilayah (regions/postal codes)
docker-compose -f docker-compose.prod.yml exec erp-app \
  php artisan wilayah:import \
  path/to/wilayah.sql \
  path/to/wilayah_kodepos.sql \
  --fresh
```

### 7. Verify Deployment
```bash
# Check container logs
docker-compose -f docker-compose.prod.yml logs -f erp-app

# Test endpoints
curl -I https://erp.asy-syifaa.com
curl -I https://api.asy-syifaa.com
curl -I https://www.asy-syifaa.com
curl -I https://app.asy-syifaa.com

# Traefik dashboard (optional, if configured)
curl -I https://traefik.asy-syifaa.com
```

---

## Useful Commands

### View Logs
```bash
# All services
docker-compose -f docker-compose.prod.yml logs -f

# Specific service
docker-compose -f docker-compose.prod.yml logs -f erp-app
docker-compose -f docker-compose.prod.yml logs -f api-app
```

### Database Backup
```bash
docker-compose -f docker-compose.prod.yml exec postgresql \
  pg_dump -U pesantren erp_pesantren > backups/erp_pesantren_$(date +%Y%m%d_%H%M%S).sql
```

### Database Restore
```bash
docker-compose -f docker-compose.prod.yml exec -T postgresql \
  psql -U pesantren erp_pesantren < backups/erp_pesantren_YYYYMMDD_HHMMSS.sql
```

### Restart Service
```bash
docker-compose -f docker-compose.prod.yml restart erp-app
docker-compose -f docker-compose.prod.yml restart api-app
```

### Stop All Services
```bash
docker-compose -f docker-compose.prod.yml down
```

### Stop & Remove Volumes (Full Reset)
```bash
docker-compose -f docker-compose.prod.yml down -v
```

---

## Monitoring & Maintenance

### Container Health
```bash
docker-compose -f docker-compose.prod.yml ps
docker inspect $(docker-compose -f docker-compose.prod.yml ps -q erp-app)
```

### Disk Usage
```bash
docker system df
docker system prune -a  # Free up space (careful!)
```

### Update Images
```bash
docker-compose -f docker-compose.prod.yml pull
docker-compose -f docker-compose.prod.yml up -d
```

---

## Troubleshooting

### Traefik SSL Not Working
```bash
# Check certificate storage
ls -la letsencrypt/

# View Traefik logs
docker logs traefik
```

### Database Connection Error
```bash
docker-compose -f docker-compose.prod.yml exec postgresql \
  psql -U pesantren -d erp_pesantren -c "SELECT 1"
```

### API/App Not Responding
```bash
# Check if containers are running
docker ps | grep asy-syifaa

# View container logs
docker logs api-app
docker logs pwa
```

### Out of Memory
```bash
docker stats --no-stream
# If needed, increase VPS RAM or optimize app
```

---

## Production Checklist

- [ ] Update all `.env` secrets before deployment
- [ ] Verify DNS records are resolving
- [ ] Test all 4 endpoints (ERP, API, Website, App)
- [ ] Backup database regularly (cron job)
- [ ] Monitor disk usage & logs
- [ ] Set up alerting (e.g., Uptime Robot, Sentry)
- [ ] Enable firewall & rate limiting (optional)
- [ ] Implement automated backups to S3/external storage

---

## Support

For issues:
1. Check logs: `docker-compose -f docker-compose.prod.yml logs -f`
2. Verify DNS & firewall
3. Ensure secrets in `.env` are correct
4. Check VPS resource limits

---

**Last Updated:** 2025
**Maintained By:** Asy-Syifaa Team
