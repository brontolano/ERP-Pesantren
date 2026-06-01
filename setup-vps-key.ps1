##############################################
# Setup SSH Key untuk deploy ke Hostinger VPS
# VPS: 187.77.116.167 | User: root | Port: 22
##############################################

$VPS_HOST = "187.77.116.167"
$VPS_USER = "root"
$VPS_PORT = "22"
$KeyPath = "$env:USERPROFILE\.ssh\hostinger_deploy"

Write-Host "`n=== VPS Deploy Key Setup ===" -ForegroundColor Cyan
Write-Host "  Host : $VPS_HOST"
Write-Host "  User : $VPS_USER"
Write-Host "  Port : $VPS_PORT"
Write-Host ""

# Step 1: Generate key
Write-Host "=== Step 1: Generate SSH Key (tanpa passphrase) ===" -ForegroundColor Cyan

if (Test-Path $KeyPath) {
    $overwrite = Read-Host "Key sudah ada di $KeyPath. Overwrite? (y/n)"
    if ($overwrite -ne 'y') {
        Write-Host "Pakai key existing." -ForegroundColor Yellow
    } else {
        Remove-Item "$KeyPath*" -Force
        ssh-keygen -t ed25519 -f $KeyPath -N '""' -C "github-actions-deploy"
    }
} else {
    ssh-keygen -t ed25519 -f $KeyPath -N '""' -C "github-actions-deploy"
}

if (-not (Test-Path "$KeyPath.pub")) {
    Write-Host "GAGAL generate key!" -ForegroundColor Red
    exit 1
}

Write-Host "`nKey generated:" -ForegroundColor Green
Write-Host "  Private: $KeyPath"
Write-Host "  Public : $KeyPath.pub"

# Step 2: Upload public key ke VPS
Write-Host "`n=== Step 2: Pasang public key ke VPS ===" -ForegroundColor Cyan
Write-Host "Masukkan PASSWORD VPS saat diminta..." -ForegroundColor Yellow
Write-Host ""

$PubKey = (Get-Content "$KeyPath.pub" -Raw).Trim()

ssh -p $VPS_PORT "$VPS_USER@$VPS_HOST" "mkdir -p ~/.ssh && chmod 700 ~/.ssh && echo '$PubKey' >> ~/.ssh/authorized_keys && sort -u -o ~/.ssh/authorized_keys ~/.ssh/authorized_keys && chmod 600 ~/.ssh/authorized_keys && echo 'Public key berhasil ditambahkan!'"

if ($LASTEXITCODE -ne 0) {
    Write-Host "`nGAGAL upload key. Pastikan password VPS benar." -ForegroundColor Red
    Write-Host "Coba manual: ssh -p $VPS_PORT $VPS_USER@$VPS_HOST" -ForegroundColor Yellow
    exit 1
}

# Step 3: Test key login
Write-Host "`n=== Step 3: Test SSH key login ===" -ForegroundColor Cyan
ssh -p $VPS_PORT -i $KeyPath -o PasswordAuthentication=no "$VPS_USER@$VPS_HOST" "echo 'SSH key auth OK!' && php -v 2>/dev/null | head -1 && nginx -v 2>&1 | head -1 && ls -la /opt/asy-syifaa/website/src/ 2>/dev/null | head -5 || echo '/opt/asy-syifaa/website/src/ belum ada'"

if ($LASTEXITCODE -ne 0) {
    Write-Host "`nSSH key test GAGAL." -ForegroundColor Red
    Write-Host "Kemungkinan PubkeyAuthentication disabled di VPS." -ForegroundColor Yellow
    Write-Host "SSH manual ke VPS, lalu jalankan:" -ForegroundColor Yellow
    Write-Host "  sed -i 's/^#*PubkeyAuthentication.*/PubkeyAuthentication yes/' /etc/ssh/sshd_config"
    Write-Host "  systemctl restart sshd"
    exit 1
}

# Step 4: Tampilkan instruksi update GitHub Secrets
Write-Host "`n`n========================================" -ForegroundColor Green
Write-Host "  BERHASIL! Sekarang update GitHub Secrets" -ForegroundColor Green
Write-Host "========================================`n" -ForegroundColor Green

Write-Host "Buka: https://github.com/brontolano/ERP-Pesantren/settings/secrets/actions" -ForegroundColor Cyan
Write-Host ""

$PrivKey = Get-Content $KeyPath -Raw
Write-Host "1. Update secret HOSTINGER_SSH_KEY:" -ForegroundColor Yellow
Write-Host "   (copy SEMUA termasuk BEGIN/END line)`n"
Write-Host "--- MULAI COPY ---" -ForegroundColor DarkGray
Write-Host $PrivKey
Write-Host "--- AKHIR COPY ---`n" -ForegroundColor DarkGray

Write-Host "2. Update secret VPS_SSH_KEY dengan isi yang SAMA di atas`n" -ForegroundColor Yellow

Write-Host "3. Set/update secrets ini juga:" -ForegroundColor Yellow
Write-Host "   HOSTINGER_HOST        = $VPS_HOST"
Write-Host "   HOSTINGER_USER        = $VPS_USER"
Write-Host "   HOSTINGER_PORT        = $VPS_PORT"
Write-Host "   HOSTINGER_DEPLOY_PATH = /opt/asy-syifaa/website/src"
Write-Host ""
Write-Host "4. HAPUS atau kosongkan:" -ForegroundColor Yellow
Write-Host "   HOSTINGER_SSH_PASSPHRASE  (key baru tanpa passphrase)"
Write-Host "   VPS_SSH_PASSPHRASE        (key baru tanpa passphrase)"
Write-Host ""

Write-Host "5. Setelah semua secrets di-update:" -ForegroundColor Yellow
Write-Host "   Buka GitHub Actions -> 'Reinstall Website on VPS' -> Run workflow"
Write-Host ""

# Copy private key to clipboard
$PrivKey | Set-Clipboard
Write-Host "[Private key sudah di-copy ke clipboard!]" -ForegroundColor Green
