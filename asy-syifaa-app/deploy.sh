#!/bin/bash
# ============================================================
# Deploy Script — Asy-Syifaa PWA ke VPS
# Jalankan di VPS: bash deploy.sh
# ============================================================

set -e  # exit on error

APP_DIR="/opt/asy-syifaa-app"
REPO_URL="https://github.com/brontolano/app.asy-syifaa.git"
BRANCH="main"

echo ""
echo "╔══════════════════════════════════════════════╗"
echo "║   Deploy Asy-Syifaa PWA — app.asy-syifaa.com ║"
echo "╚══════════════════════════════════════════════╝"
echo ""

# ── 1. Clone atau Pull repo ──────────────────────────────────
if [ -d "$APP_DIR" ]; then
  echo "📦 Repo sudah ada, pull latest..."
  cd "$APP_DIR"
  git pull origin "$BRANCH"
else
  echo "📦 Clone repo..."
  git clone "$REPO_URL" "$APP_DIR"
  cd "$APP_DIR"
fi

# ── 2. Buat .env jika belum ada ─────────────────────────────
if [ ! -f "$APP_DIR/.env.production" ]; then
  echo "⚙️  Membuat .env.production..."
  cat > "$APP_DIR/.env.production" << 'EOF'
VITE_API_BASE=https://api.asy-syifaa.com
VITE_DEMO_MODE=false
EOF
fi

# Copy .env ke dalam pwa/ untuk Vite build
cp "$APP_DIR/.env.production" "$APP_DIR/pwa/.env.production"

# ── 3. Pastikan network traefik-public ada ──────────────────
if ! docker network ls | grep -q "traefik-public"; then
  echo "🌐 Membuat Docker network traefik-public..."
  docker network create traefik-public
fi

# ── 4. Build & start container ──────────────────────────────
echo ""
echo "🔨 Build Docker image PWA..."
cd "$APP_DIR"

docker compose build --no-cache pwa

echo ""
echo "🚀 Starting container..."
docker compose up -d pwa

# ── 5. Verifikasi ────────────────────────────────────────────
echo ""
echo "✅ Status container:"
docker compose ps pwa

echo ""
echo "📋 Log (10 baris terakhir):"
docker compose logs --tail=10 pwa

echo ""
echo "══════════════════════════════════════════════"
echo "  Deploy selesai!"
echo "  URL: https://app.asy-syifaa.com"
echo "  (DNS propagation ~15 menit jika baru)"
echo "══════════════════════════════════════════════"
