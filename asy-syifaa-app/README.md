# Asy-Syifaa App — PWA Wali Santri

**URL Produksi:** `app.asy-syifaa.com`  
**Platform:** PWA (Progressive Web App) + TWA (Play Store)  
**Status:** 🚧 Dalam Perencanaan

---

## Tentang

Aplikasi mobile untuk orang tua dan wali santri Pondok Pesantren Asy-Syifaa Wal Mahmuudiyyah. Dibangun sebagai PWA dengan tampilan native Android, dan didistribusikan via Play Store menggunakan TWA.

## Prinsip Desain

> **Harus terasa seperti native Android, bukan website.**

- Bottom Navigation Bar (bukan hamburger menu)
- Skeleton loading, bukan spinner
- Bottom sheet untuk form & detail
- Animasi transisi halaman
- Pull-to-refresh
- Splash screen

## Tech Stack (Rencana)

- **Framework:** Next.js (App Router) atau Vue 3 + Vite
- **UI:** Tailwind CSS + komponen custom Material-like
- **PWA:** next-pwa / vite-plugin-pwa
- **API:** `api.asy-syifaa.com` (sudah live)
- **Auth:** Role `wali` via Sanctum token (sudah ada di ERP)

## Distribusi

1. **PWA** — install langsung dari browser (`app.asy-syifaa.com`)
2. **TWA** — publish ke Google Play Store via PWABuilder

## Struktur Folder

```
asy-syifaa-app/
├── README.md
├── docs/
│   ├── PLAYSTORE_LISTING.md   — Judul, deskripsi, metadata Play Store
│   └── WIREFRAME.md           — Rancangan layar (coming soon)
├── design/
│   └── ...                    — File desain UI/UX
└── playstore-assets/
    └── ...                    — Icon, screenshot, feature graphic
```

## Fitur Utama

| Fitur | Prioritas |
|-------|-----------|
| Dashboard santri (status, ringkasan) | Tinggi |
| Progress hafalan Al-Quran | Tinggi |
| Tagihan & upload bukti bayar | Tinggi |
| Absensi harian | Tinggi |
| Pengajuan izin online | Tinggi |
| Notifikasi push | Sedang |
| Galeri kegiatan | Sedang |
| Pengumuman pesantren | Sedang |
| Chat dengan musyrif/wali kelas | Rendah |

## API yang Digunakan

Semua data dari `api.asy-syifaa.com` dengan auth role `wali`:

```
POST /api/v1/auth/login
GET  /api/v1/wali/santri
GET  /api/v1/wali/absensi
GET  /api/v1/wali/hafalan
GET  /api/v1/wali/tagihan
POST /api/v1/wali/bayar/konfirmasi
GET  /api/v1/wali/izin
POST /api/v1/wali/izin
GET  /api/v1/posts?category=announcement
GET  /api/v1/galleries
```

*Beberapa endpoint di atas perlu ditambahkan di ERP.*
