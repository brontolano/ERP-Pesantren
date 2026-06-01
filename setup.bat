@echo off
REM Asy-Syifaa Docker Setup Script (Windows)
REM Run this once to initialize the Docker deployment

setlocal enabledelayedexpansion

echo.
echo ==========================================
echo Asy-Syifaa Pesantren — Docker Setup
echo ==========================================
echo.

REM Step 1: Create directories
echo [1/7] Creating directories...
if not exist "letsencrypt" mkdir letsencrypt
if not exist "backups" mkdir backups
if not exist "logs" mkdir logs
if not exist "storage" mkdir storage
if not exist "cache" mkdir cache
echo ✓ Directories created
echo.

REM Step 2: Copy environment files
echo [2/7] Setting up environment files...
if not exist ".env" (
    copy .env.production .env
    echo ✓ Copied .env.production to .env
) else (
    echo ⚠ .env already exists, skipping
)

if not exist "asy-syifaa-erp\.env" (
    copy "asy-syifaa-erp\.env.example" "asy-syifaa-erp\.env"
    echo ✓ Copied ERP .env
)

if not exist "asy-syifaa-api\.env" (
    copy "asy-syifaa-api\.env.example" "asy-syifaa-api\.env"
    echo ✓ Copied API .env
)
echo.

REM Step 3: Build Docker images
echo [3/7] Building Docker images (this may take 5-10 minutes)...
echo Running: docker-compose -f docker-compose.prod.yml build
docker-compose -f docker-compose.prod.yml build --pull
if errorlevel 1 (
    echo ✗ Docker build failed
    exit /b 1
)
echo ✓ Docker images built
echo.

REM Step 4: Create Laravel app key (ERP)
echo [4/7] Generating ERP Laravel key...
if not exist "asy-syifaa-erp\.env" (
    echo ⚠ ERP .env not found, skipping key generation
) else (
    REM We'll generate this after containers start
    echo ℹ Key will be generated on first docker-compose up
)
echo.

REM Step 5: Display next steps
echo [5/7] Setup complete!
echo.
echo ==========================================
echo NEXT STEPS:
echo ==========================================
echo.
echo 1. Edit your .env file with production secrets:
echo    notepad .env
echo.
echo 2. Update critical variables:
echo    - DB_PASSWORD ^(strong password^)
echo    - JWT_SECRET ^(generate secure key^)
echo    - LETSENCRYPT_EMAIL ^(valid email^)
echo    - API_KEY_* ^(generate secure keys^)
echo.
echo 3. Start the stack:
echo    docker-compose -f docker-compose.prod.yml up -d
echo.
echo 4. Wait 30 seconds, then migrate database:
echo    docker-compose -f docker-compose.prod.yml exec erp-app php artisan migrate --force
echo.
echo 5. Test endpoints:
echo    docker ps
echo    docker-compose -f docker-compose.prod.yml logs -f
echo.
echo ==========================================
echo.
pause
