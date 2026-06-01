# Laporan Implementasi Rebuild VPS (Preserve Master DB)

Tanggal: 2026-06-01 (Asia/Jakarta)
Eksekutor: Codex
Scope: Implementasi plan "Rebuild VPS: pertahankan fondasi DB master + auto-deploy GitHub"

## 1) Ringkasan Hasil

Implementasi level repository sudah selesai untuk:
- Hardening workflow auto-deploy ERP/API/PWA.
- Perbaikan trigger API agar otomatis saat ada perubahan source.
- Penambahan skrip operasional rebuild VPS non-DB (preserve DB master).
- Penambahan runbook eksekusi dan checklist pasca-deploy.

Implementasi level VPS (cleanup live, reinstall service, cutover domain) belum dieksekusi dari sesi ini karena membutuhkan eksekusi langsung di server.

## 2) Perubahan yang Diterapkan

### A. Workflow API
File: `.github/workflows/deploy-api.yml`

Perubahan utama:
- Trigger path diperbaiki dari `asy-syifaa-api` menjadi `asy-syifaa-api/**`.
- Timeout job dinaikkan ke 12 menit, `command_timeout` ke 8 menit.
- Tambah fail-fast:
  - validasi direktori `/opt/asy-syifaa/backend`
  - `set -euo pipefail`
- Migrasi Prisma dijalankan mandatory jika schema ada.
- Health check API wajib sukses (`curl -fsS http://127.0.0.1:3100/api/health`).

### B. Workflow ERP
File: `.github/workflows/deploy-erp.yml`

Perubahan utama:
- Timeout job dinaikkan ke 12 menit, `command_timeout` ke 8 menit.
- Tambah fail-fast:
  - validasi `/opt/asy-syifaa/erp`
  - validasi `/opt/asy-syifaa/erp/src/artisan`
  - `set -euo pipefail`
- `composer install`, `migrate --force`, cache config/route/view dijadikan mandatory.
- Restart container mandatory + health check ERP endpoint (`/up`).

### C. Workflow PWA
File: `.github/workflows/deploy-app.yml`

Perubahan utama:
- Timeout job dinaikkan ke 12 menit.
- Tambah verifikasi deploy artifact di VPS:
  - folder `/opt/asy-syifaa-app/dist` wajib ada
  - jumlah file wajib > 0
- Restart container + health check local PWA mandatory.

### D. Skrip Rebuild VPS Preserve DB
File: `ops/rebuild-vps-preserve-master-db.sh`

Isi utama:
- Backup snapshot konfigurasi aktif (`nginx/caddy`, compose, PM2, cron).
- Backup env dan storage dinamis (`storage/app/public`, uploads website).
- Backup metadata DB (schema + grants) tanpa drop data utama.
- Stop dan cleanup service lama non-DB (container/PM2/kode lama).
- Recreate direktori standar deploy:
  - `/opt/asy-syifaa/erp/src`
  - `/opt/asy-syifaa/backend`
  - `/opt/asy-syifaa-app/dist`
  - `/opt/asy-syifaa/website/src`

### E. Runbook Operasional
File: `ops/REBUILD_RUNBOOK.md`

Isi utama:
- Urutan eksekusi rebuild.
- Mapping domain target.
- Daftar workflow yang harus ditrigger.
- Checklist verifikasi post-deploy.

### F. Stabilization Update: Website Deploy Passphrase Optional
File: `.github/workflows/deploy-website.yml`

Perubahan utama:
- `HOSTINGER_SSH_PASSPHRASE` diubah menjadi **opsional** (tidak lagi fail-fast saat kosong).
- Log mode autentikasi diperjelas:
  - encrypted key mode (passphrase terdeteksi)
  - non-encrypted key mode (passphrase kosong)
- `passphrase` tetap dikirim ke action, default kosong jika secret tidak ada.

Dampak:
- Menghilangkan false blocker `Missing secrets: HOSTINGER_SSH_PASSPHRASE`.
- Jika deploy masih gagal, akar masalah dipersempit ke mismatch `HOSTINGER_USER` / `HOSTINGER_SSH_KEY` / `authorized_keys`.

## 3) Status terhadap Plan

- Inventaris + backup: **tersedia dalam skrip**.
- Cleanup sistem lama non-DB: **tersedia dalam skrip**.
- Path baku baru: **sudah dikunci dalam workflow + skrip**.
- Koneksi DB preserve master: **disiapkan via prinsip migrate-only (tanpa reset)**.
- Perbaikan auto-deploy GitHub: **sudah diterapkan**.
- Perbaikan reverse proxy/domain: **runbook tersedia, belum dieksekusi live di VPS dari sesi ini**.

## 4) Risiko dan Catatan Operasional

1. Skrip rebuild bersifat destruktif untuk direktori aplikasi non-DB (`rm -rf` path kode/deploy). Wajib jalankan hanya setelah backup sukses.
2. Skrip backup DB metadata mengasumsikan kredensial DB tersedia di sesi VPS (`DB_DATABASE` + auth mysql).
3. Health endpoint ERP/PWA bisa berbeda di server aktual; jika berbeda, sesuaikan URL check di workflow.
4. Website saat ini punya workflow terpisah Hostinger. Jika full pindah ke VPS tunggal, workflow website perlu diselaraskan ke `VPS_*` secrets.

## 5) Checklist Lanjutan (Eksekusi Live)

- [ ] Upload + jalankan `ops/rebuild-vps-preserve-master-db.sh` di VPS.
- [ ] Pastikan backup folder `/opt/asy-syifaa/backups/<timestamp>` terbentuk lengkap.
- [ ] Trigger manual/commit ke workflow:
  - `deploy-erp.yml`
  - `deploy-api.yml`
  - `deploy-app.yml`
  - `deploy-website.yml`
- [ ] Verifikasi GitHub Actions hijau dan log menunjuk path standar.
- [ ] Terapkan/cek reverse proxy:
  - `erp.asy-syifaa.com`
  - `api.asy-syifaa.com`
  - `app.asy-syifaa.com`
  - `www.asy-syifaa.com`
- [ ] Uji end-to-end flow website -> ERP/API.

## 6) Kesimpulan

Rencana rebuild sudah diimplementasikan pada sisi kode/CI workflow dan artefak operasional. Tahap berikutnya adalah eksekusi langsung di VPS untuk cleanup, install ulang service, cutover domain, dan verifikasi live.
