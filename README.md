# Asy-Syifaa Digital Platform

Sistem digital terpadu Pondok Pesantren Asy-Syifaa Wal Mahmuudiyyah, Sumedang.

## Struktur Project

```
_Agent Manager/_ERP/
├── asy-syifaa-erp/        ERP + API Backend
├── asy-syifaa-website/    Landing Page & Website Publik
├── asy-syifaa-app/        PWA Aplikasi Wali Santri
├── _branding/             Logo & Icon Assets
├── skills/                Claude Code Skills
├── arsip_project/         Arsip file lama (jangan disentuh)
└── RENAME_FOLDERS.bat     Script rename (jalankan 1x setelah tutup VS Code)
```

## Repositories

| Folder | Repo | Domain | Stack |
|--------|------|--------|-------|
| `asy-syifaa-erp` | [brontolano/asy-syifaa](https://github.com/brontolano/asy-syifaa) | erp.asy-syifaa.com | Laravel 13 + Filament 5 + PHP 8.4 |
| `asy-syifaa-website` | [brontolano/asy-syifaa-website](https://github.com/brontolano/asy-syifaa-website) | www.asy-syifaa.com | PHP native (Hostinger) |
| `asy-syifaa-app` | [brontolano/app.asy-syifaa](https://github.com/brontolano/app.asy-syifaa) | app.asy-syifaa.com | Vue 3 + Vite + PWA |

## Infrastruktur

| Komponen | Detail |
|----------|--------|
| VPS | Hostinger VPS `187.77.116.167` |
| Reverse Proxy | Traefik + auto SSL |
| Database | PostgreSQL (port 32768) |
| Website Hosting | Hostinger Shared (www.asy-syifaa.com) |
| CI/CD | GitHub Actions → VPS auto deploy |
| WhatsApp Gateway | n8n + WAHA |

## Modul Aktif

- **SPMB/PPDB** — Pendaftaran santri baru (website → API → ERP)
- **Kepesantrenan** — Data santri, kelas, akademik, hafalan
- **Keuangan** — Tagihan, pembayaran, tabungan santri
- **CMS** — Post & gallery untuk website/app
- **Wali App** — Dashboard wali santri (PWA)
- **Master Wilayah** — Database alamat Indonesia standar Kepmendagri 2025

## Quick Start (Development)

```bash
# ERP Backend
cd asy-syifaa-erp
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan wilayah:import path/to/wilayah.sql path/to/wilayah_kodepos.sql --fresh
php artisan serve

# PWA App
cd asy-syifaa-app/pwa
npm install
npm run dev
```

## API Endpoints

Base URL: `https://api.asy-syifaa.com/api/v1`

| Grup | Contoh Endpoint |
|------|-----------------|
| Auth | `POST /auth/login`, `GET /auth/me` |
| SPMB | `POST /spmb/register`, `GET /spmb/{id}/status` |
| Wali | `GET /wali/santri`, `GET /wali/santri/{id}/tagihan` |
| Wilayah | `GET /wilayah/provinces`, `GET /wilayah/cities/{code}` |
| CMS | `GET /posts`, `GET /galleries` |
