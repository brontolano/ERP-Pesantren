# Go-Live Checklist — Asy-Syifaa App (PWA)

> **Target Go-Live:** Semester 1 - 2026  
> **Versi PWA:** 1.3.0  
> **Status:** Pre-Launch Preparation

Gunakan checklist ini sebelum dan sesudah deploy ke produksi. Centang satu per satu.

---

## 1. Environment & Build

- [ ] Buat file `pwa/.env.production` dengan isi:
  ```env
  VITE_API_BASE=https://api.asy-syifaa.com
  VITE_DEMO_MODE=false
  ```
- [ ] Pastikan `VITE_DEMO_MODE=true` **TIDAK ADA** di `.env.production`
- [ ] Test build production lokal: `cd pwa && npm run build`
- [ ] Verifikasi folder `pwa/dist/` terbentuk tanpa error
- [ ] Cek tidak ada `console.log` atau debug code tertinggal

---

## 2. ERP Backend — API Readiness

### Auth Endpoints
- [ ] `POST /api/v1/auth/login` — return Sanctum token
- [ ] `GET /api/v1/auth/me` — return user aktif
- [ ] `POST /api/v1/auth/logout` — revoke token

### Wali Endpoints (21 endpoints)
- [ ] `GET /api/v1/wali/santri`
- [ ] `GET /api/v1/wali/santri/{id}/status-harian`
- [ ] `GET /api/v1/wali/santri/{id}/hafalan`
- [ ] `GET /api/v1/wali/santri/{id}/tagihan`
- [ ] `POST /api/v1/wali/santri/{id}/tagihan/{invId}/bukti-bayar`
- [ ] `GET /api/v1/wali/santri/{id}/tabungan`
- [ ] `POST /api/v1/wali/santri/{id}/tabungan/limit`
- [ ] `POST /api/v1/wali/santri/{id}/tabungan/freeze`
- [ ] `GET /api/v1/wali/santri/{id}/transaksi`
- [ ] `GET /api/v1/wali/santri/{id}/jadwal`
- [ ] `GET /api/v1/wali/santri/{id}/absensi`
- [ ] `GET /api/v1/wali/santri/{id}/akademik`
- [ ] `GET /api/v1/wali/santri/{id}/prestasi`
- [ ] `GET /api/v1/wali/santri/{id}/kesehatan`
- [ ] `GET /api/v1/wali/santri/{id}/izin`
- [ ] `POST /api/v1/wali/santri/{id}/izin`
- [ ] `GET /api/v1/wali/santri/{id}/kunjungan`
- [ ] `POST /api/v1/wali/santri/{id}/kunjungan`
- [ ] `GET /api/v1/wali/santri/{id}/konseling`
- [ ] `POST /api/v1/wali/santri/{id}/konseling`
- [ ] `GET /api/v1/wali/santri/{id}/presensi`

---

## 3. ERP Konfigurasi

### CORS
- [ ] Buka `config/cors.php` di ERP Laravel
- [ ] Tambahkan ke `allowed_origins`:
  ```php
  'https://app.asy-syifaa.com',
  'https://api.asy-syifaa.com',
  ```
- [ ] Pastikan `allowed_methods` include `['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS']`
- [ ] Pastikan `allowed_headers` include `['Authorization', 'Content-Type', 'Accept']`
- [ ] `supports_credentials` → `true`

### Laravel Sanctum
- [ ] Buka `config/sanctum.php`
- [ ] Pastikan `stateful` include:
  ```php
  'app.asy-syifaa.com',
  '*.asy-syifaa.com',
  ```
- [ ] Di `.env` ERP, set: `SANCTUM_STATEFUL_DOMAINS=app.asy-syifaa.com`
- [ ] Di `.env` ERP, set: `SESSION_DOMAIN=.asy-syifaa.com`

### Upload Bukti Bayar
- [ ] Pastikan `storage/app/public` writeable di container
- [ ] `php artisan storage:link` sudah dijalankan
- [ ] Max upload size (php.ini `upload_max_filesize`) minimal 5MB

---

## 4. DNS & SSL

- [ ] DNS A record `app` → `187.77.116.167` aktif
- [ ] DNS A record `api` → `187.77.116.167` aktif
- [ ] DNS A record `erp` → `187.77.116.167` aktif
- [ ] SSL certificate aktif untuk `app.asy-syifaa.com`
- [ ] SSL certificate aktif untuk `api.asy-syifaa.com`
- [ ] SSL certificate aktif untuk `erp.asy-syifaa.com`
- [ ] Semua HTTP redirect ke HTTPS
- [ ] Test: `curl -I https://app.asy-syifaa.com` → 200 OK
- [ ] Test: `curl -I https://api.asy-syifaa.com/api/v1/health` → 200 OK

---

## 5. VPS — Deploy PWA

```bash
# Di VPS, dalam folder project
cd /path/to/asy-syifaa-app

# Pull latest
git pull origin main

# Install dependencies & build
cd pwa
npm install
npm run build

# Copy dist ke web server (Nginx/Apache)
cp -r dist/* /var/www/app.asy-syifaa.com/
# atau jika pakai Docker:
docker build -t asy-syifaa-pwa:latest .
docker-compose up -d --build pwa
```

- [ ] PWA build berhasil (`npm run build` tanpa error)
- [ ] File `dist/` ter-deploy ke server
- [ ] `manifest.webmanifest` accessible di `https://app.asy-syifaa.com/manifest.webmanifest`
- [ ] Service Worker aktif (`sw.js` ter-load)
- [ ] Buka browser → DevTools → Application → Service Worker → Status: activated

---

## 6. VPS — Deploy ERP

```bash
# Pull & update ERP
cd /path/to/erp-pesantren
git pull origin main

# Install/update composer
composer install --no-dev --optimize-autoloader

# Run migrations
php artisan migrate --force

# Clear & rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart queue worker (jika pakai)
php artisan queue:restart
```

- [ ] Semua migration berjalan tanpa error
- [ ] `php artisan config:cache` sukses
- [ ] `php artisan route:cache` sukses
- [ ] Container restart sukses: `docker-compose restart erp`

---

## 7. Traefik — Routing

- [ ] `app.asy-syifaa.com` → container PWA (Nginx serve static)
- [ ] `api.asy-syifaa.com` → container ERP Laravel (port 80)
- [ ] `erp.asy-syifaa.com` → container ERP Laravel (port 80)
- [ ] Buka `docker-compose.yml` ERP — pastikan Traefik labels:
  ```yaml
  labels:
    - "traefik.http.routers.erp.rule=Host(`erp.asy-syifaa.com`) || Host(`api.asy-syifaa.com`)"
    - "traefik.http.routers.erp.tls=true"
    - "traefik.http.routers.erp.tls.certresolver=letsencrypt"
  ```

---

## 8. Pengujian Smoke Test (Post Deploy)

### Login & Auth
- [ ] Buka `https://app.asy-syifaa.com/login`
- [ ] Login dengan akun wali santri yang ada di DB
- [ ] Redirect ke `/dashboard` berhasil
- [ ] Token tersimpan di localStorage (`asf_token`)

### Dashboard
- [ ] Nama wali tampil di header
- [ ] Jadwal sholat muncul (countdown berjalan)
- [ ] Status hari ini: kehadiran + kesehatan tampil
- [ ] 15 tombol Akses Cepat tampil dalam 3 grup
- [ ] Marketplace card tampil abu-abu/disabled

### Halaman Fitur
- [ ] `/keuangan` → Hub keuangan load
- [ ] `/keuangan/tagihan` → Invoice list tampil (atau empty state)
- [ ] `/keuangan/tabungan` → Saldo tampil
- [ ] `/belajar/hafalan` → Progress hafalan tampil
- [ ] `/belajar/jadwal` → Jadwal pelajaran tampil
- [ ] `/belajar/absensi` → Kalender absensi tampil
- [ ] `/kegiatan/izin` → List izin tampil
- [ ] `/profil` → Data akun tampil, logout berfungsi

### Fitur Khusus
- [ ] Ganti nomor HP → simpan → berhasil
- [ ] Ubah password → simpan → berhasil (atau test dengan akun dummy)
- [ ] Upload bukti bayar tagihan → file ter-upload ke server
- [ ] Set limit jajan → API dipanggil, nilai berubah

### PWA Features
- [ ] "Add to Home Screen" prompt muncul di Android Chrome
- [ ] Setelah install, buka dari home screen → fullscreen (no browser UI)
- [ ] Offline indicator muncul saat koneksi terputus

---

## 9. TWA & Play Store

> Lakukan ini setelah PWA live dan berfungsi penuh di `https://app.asy-syifaa.com`

- [ ] Pastikan `assetlinks.json` tersedia:
  ```
  https://app.asy-syifaa.com/.well-known/assetlinks.json
  ```
- [ ] `assetlinks.json` berisi SHA-256 fingerprint APK/keystore yang benar
- [ ] Build TWA APK menggunakan [Bubblewrap CLI](https://github.com/GoogleChromeLabs/bubblewrap):
  ```bash
  npx @bubblewrap/cli init --manifest=https://app.asy-syifaa.com/manifest.webmanifest
  npx @bubblewrap/cli build
  ```
- [ ] Test APK di device Android → buka tanpa browser chrome bar
- [ ] Upload ke Play Console (Internal Testing dulu)
- [ ] Jalankan Internal Test dengan 3-5 akun wali santri real
- [ ] Setelah lolos test → Publish ke Production

---

## 10. Marketplace — Aktivasi (Setelah Backend Siap)

> Lakukan HANYA setelah fitur marketplace backend selesai

- [ ] Backend endpoint marketplace sudah siap
- [ ] Di `DashboardView.vue`: hapus overlay abu-abu di section Marketplace
- [ ] Di `DashboardView.vue`: hapus `filter grayscale opacity-50` dari card Marketplace
- [ ] Tambahkan route baru ke `router/index.ts` jika ada sub-halaman
- [ ] Test full flow: lihat produk → tambah ke keranjang → checkout via tabungan santri
- [ ] Re-build & re-deploy

---

## 11. Monitoring Post-Launch

- [ ] Aktifkan error tracking (Sentry atau Laravel Telescope)
- [ ] Monitor Nginx access logs: `docker logs nginx -f`
- [ ] Monitor ERP Laravel logs: `docker exec erp php artisan log:tail` (atau buka `storage/logs/`)
- [ ] Pantau usage selama 48 jam pertama

---

## Kontak Darurat / Rollback

Jika ada critical bug setelah go-live:

```bash
# Rollback PWA ke versi sebelumnya
git checkout <previous-commit>
npm run build
# re-deploy dist/

# Rollback ERP migration (jika perlu)
php artisan migrate:rollback --step=1
```

- VPS IP: `187.77.116.167`
- ERP URL: `https://erp.asy-syifaa.com`
- API URL: `https://api.asy-syifaa.com`
- App URL: `https://app.asy-syifaa.com`

---

*Checklist ini harus diselesaikan 100% sebelum mengumumkan app ke wali santri.*
