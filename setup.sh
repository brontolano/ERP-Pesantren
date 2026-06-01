#!/bin/bash
# Asy-Syifaa Docker Setup Script
# Run this once to initialize the Docker deployment

set -e

echo "=========================================="
echo "Asy-Syifaa Pesantren — Docker Setup"
echo "=========================================="
echo ""

# Step 1: Create necessary directories
echo "[1/7] Creating directories..."
mkdir -p letsencrypt backups logs storage cache
chmod 700 letsencrypt
echo "✓ Directories created"
echo ""

# Step 2: Copy environment files
echo "[2/7] Setting up environment files..."
if [ ! -f .env ]; then
    cp .env.production .env
    echo "✓ Copied .env.production → .env"
else
    echo "⚠ .env already exists, skipping"
fi

if [ ! -f ./asy-syifaa-erp/.env ]; then
    cp ./asy-syifaa-erp/.env.example ./asy-syifaa-erp/.env
    echo "✓ Copied ERP .env"
fi

if [ ! -f ./asy-syifaa-api/.env ]; then
    cp ./asy-syifaa-api/.env.example ./asy-syifaa-api/.env
    echo "✓ Copied API .env"
fi

echo ""

# Step 3: Install PHP dependencies (ERP)
echo "[3/7] Installing ERP dependencies..."
if [ -f ./asy-syifaa-erp/composer.json ]; then
    echo "Running: docker run --rm -v $(pwd)/asy-syifaa-erp:/app composer install --ignore-platform-reqs"
    docker run --rm -v "$(pwd)"/asy-syifaa-erp:/app composer install --ignore-platform-reqs 2>&1 | tail -5
    echo "✓ ERP dependencies installed"
else
    echo "⚠ composer.json not found"
fi
echo ""

# Step 4: Install Node dependencies (API)
echo "[4/7] Installing API dependencies..."
if [ -d ./asy-syifaa-api ]; then
    cd ./asy-syifaa-api
    npm install
    cd ..
    echo "✓ API dependencies installed"
fi
echo ""

# Step 5: Install Node dependencies (PWA)
echo "[5/7] Installing PWA dependencies..."
if [ -d ./asy-syifaa-app/pwa ]; then
    cd ./asy-syifaa-app/pwa
    npm install
    cd ../../..
    echo "✓ PWA dependencies installed"
fi
echo ""

# Step 6: Build Docker images
echo "[6/7] Building Docker images..."
docker-compose -f docker-compose.prod.yml build --pull 2>&1 | tail -10
echo "✓ Docker images built"
echo ""

# Step 7: Display next steps
echo "[7/7] Setup complete!"
echo ""
echo "=========================================="
echo "NEXT STEPS:"
echo "=========================================="
echo ""
echo "1. Edit your .env file with production secrets:"
echo "   nano .env"
echo ""
echo "2. Update critical variables:"
echo "   - DB_PASSWORD (strong password)"
echo "   - JWT_SECRET (run: openssl rand -base64 32)"
echo "   - LETSENCRYPT_EMAIL (valid email)"
echo "   - API_KEY_* (generate secure keys)"
echo ""
echo "3. Start the stack:"
echo "   docker-compose -f docker-compose.prod.yml up -d"
echo ""
echo "4. Wait 30 seconds for services to start, then migrate database:"
echo "   docker-compose -f docker-compose.prod.yml exec erp-app php artisan migrate --force"
echo ""
echo "5. Test endpoints:"
echo "   curl -I http://localhost"
echo ""
echo "=========================================="
echo ""
