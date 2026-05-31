# Dokumentasi Teknis — Asy-Syifaa App (PWA)

> **Versi:** 1.3.0  
> **Terakhir diperbarui:** 29 Mei 2026  
> **Status:** Development Selesai — Siap Go-Live  

---

## 1. Gambaran Umum

**Asy-Syifaa App** adalah Progressive Web App (PWA) untuk wali santri Pondok Pesantren Asy-Syifaa Wal Mahmuudiyyah, Sumedang. Aplikasi ini didistribusikan via Google Play Store menggunakan metode **Trusted Web Activity (TWA)** agar terasa seperti aplikasi Android native.

### Stack Teknologi
| Komponen | Teknologi |
|---|---|
| Frontend Framework | Vue 3 (Composition API) |
| Build Tool | Vite 8 |
| Bahasa | TypeScript |
| Styling | Tailwind CSS v4 |
| State Management | Pinia |
| Router | Vue Router 5 |
| HTTP Client | Axios |
| Icons | Heroicons v2 |
| PWA | vite-plugin-pwa (Workbox) |
| Backend (ERP) | Laravel 10 + Filament v3 |
| Database | PostgreSQL (VPS) |
| Auth | Laravel Sanctum |

### URL Produksi
| Service | URL |
|---|---|
| PWA / App | `https://app.asy-syifaa.com` |
| ERP Admin | `https://erp.asy-syifaa.com` |
| API | `https://api.asy-syifaa.com` |
| Website | `https://asy-syifaa.com` |
| VPS | `187.77.116.167` |

---

## 2. Struktur Proyek

```
asy-syifaa-app/
├── pwa/                          ← Aplikasi PWA utama
│   ├── src/
│   │   ├── api/
│   │   │   ├── client.ts         ← Axios instance + auth interceptor
│   │   │   ├── auth.ts           ← Login, logout, me
│   │   │   ├── wali.ts           ← Semua endpoint wali (+ demo mode)
│   │   │   └── mock.ts           ← Data dummy untuk demo/testing
│   │   ├── composables/
│   │   │   └── useSholatTime.ts  ← Jadwal sholat + countdown real-time
│   │   ├── stores/
│   │   │   ├── auth.ts           ← Auth state + demo auto-login
│   │   │   └── santri.ts         ← Daftar & santri aktif
│   │   ├── components/
│   │   │   └── layout/
│   │   │       └── NavItem.vue   ← Bottom nav item
│   │   ├── router/
│   │   │   └── index.ts          ← Semua routes + auth guard
│   │   └── views/
│   │       ├── auth/
│   │       │   └── LoginView.vue
│   │       ├── dashboard/
│   │       │   ├── DashboardView.vue   ← Beranda utama
│   │       │   ├── QuickAction.vue     ← Tombol akses cepat
│   │       │   └── StatusCard.vue      ← Kartu kehadiran/kesehatan
│   │       ├── profil/
│   │       │   └── ProfilView.vue      ← Akun, ganti HP, ganti password
│   │       ├── santri/
│   │       │   └── SantriDetailView.vue ← Biodata lengkap santri
│   │       ├── marketplace/
│   │       │   └── MarketplaceView.vue  ← Coming soon
│   │       ├── keuangan/
│   │       │   ├── KeuanganView.vue    ← Hub keuangan
│   │       │   ├── TagihanView.vue     ← Invoice + upload bukti bayar
│   │       │   ├── TabunganView.vue    ← Saldo + limit jajan
│   │       │   ├── TransaksiView.vue   ← Riwayat transaksi
│   │       │   └── PembayaranView.vue  ← QRIS, VA, e-wallet
│   │       ├── belajar/
│   │       │   ├── BelajarView.vue
│   │       │   ├── AbsensiView.vue
│   │       │   ├── JadwalView.vue
│   │       │   ├── PrestasiView.vue
│   │       │   └── AkademikView.vue
│   │       ├── hafalan/
│   │       │   └── HafalanView.vue
│   │       ├── kegiatan/
│   │       │   ├── KegiatanView.vue
│   │       │   ├── IzinView.vue (alias: izin/)
│   │       │   ├── KesehatanView.vue
│   │       │   ├── KunjunganView.vue
│   │       │   ├── KonselingView.vue
│   │       │   └── PresensiView.vue
│   │       ├── info/
│   │       │   └── InfoView.vue
│   │       └── AppLayout.vue     ← Bottom nav + page transitions
│   ├── .env.development          ← VITE_DEMO_MODE=true (dev)
│   ├── .env.production           ← Harus dibuat sebelum go-live
│   └── vite.config.ts
└── docs/
    ├── PWA_DOKUMENTASI.md        ← File ini
    ├── GOLIVE_CHECKLIST.md       ← Checklist go-live
    └── PLAYSTORE_LISTING.md      ← Metadata Play Store
```

---

## 3. Semua Halaman (Routes)

| Route | Komponen | Deskripsi |
|---|---|---|
| `/login` | LoginView | Form login wali |
| `/dashboard` | DashboardView | Beranda: santri card, sholat, status hari ini, akses cepat |
| `/profil` | ProfilView | Pengaturan akun, ganti HP, ganti password, logout |
| `/santri/detail` | SantriDetailView | Biodata lengkap santri |
| `/marketplace` | MarketplaceView | Coming soon — disabled |
| `/keuangan` | KeuanganView | Hub keuangan |
| `/keuangan/tagihan` | TagihanView | Invoice + upload bukti transfer |
| `/keuangan/tabungan` | TabunganView | Saldo tabungan + limit jajan harian |
| `/keuangan/transaksi` | TransaksiView | Riwayat transaksi tabungan |
| `/keuangan/bayar` | PembayaranView | QRIS, Virtual Account, E-Wallet |
| `/belajar` | BelajarView | Hub belajar |
| `/belajar/hafalan` | HafalanView | Progress hafalan Al-Qur'an & Hadist |
| `/belajar/jadwal` | JadwalView | Jadwal pelajaran per hari |
| `/belajar/absensi` | AbsensiView | Kalender absensi bulanan |
| `/belajar/akademik` | AkademikView | Nilai per mata pelajaran |
| `/belajar/prestasi` | PrestasiView | Penghargaan & pencapaian |
| `/kegiatan` | KegiatanView | Hub kegiatan |
| `/kegiatan/izin` | IzinView | Daftar & pengajuan izin |
| `/kegiatan/kesehatan` | KesehatanView | Riwayat kunjungan UKS |
| `/kegiatan/kunjungan` | KunjunganView | Jadwal kunjungan wali |
| `/kegiatan/konseling` | KonselingView | Sesi konseling santri |
| `/kegiatan/presensi` | PresensiView | Presensi kegiatan pondok |
| `/info` | InfoView | Pengumuman & informasi pesantren |

**Total: 23 halaman**

---

## 4. API Endpoints (Backend ERP)

### Auth
| Method | Endpoint | Fungsi |
|---|---|---|
| POST | `/api/v1/auth/login` | Login, return Sanctum token |
| GET | `/api/v1/auth/me` | Data user aktif |
| POST | `/api/v1/auth/logout` | Logout, revoke token |

### Wali Santri
| Method | Endpoint | Fungsi |
|---|---|---|
| GET | `/api/v1/wali/santri` | List santri yang ditautkan |
| GET | `/api/v1/wali/santri/{id}/status-harian` | Kehadiran & kesehatan hari ini |
| GET | `/api/v1/wali/santri/{id}/hafalan` | Progress hafalan + riwayat setoran |
| GET | `/api/v1/wali/santri/{id}/tagihan` | Invoice + total tunggakan |
| POST | `/api/v1/wali/santri/{id}/tagihan/{invId}/bukti-bayar` | Upload bukti transfer |
| GET | `/api/v1/wali/santri/{id}/tabungan` | Saldo + limit jajan |
| POST | `/api/v1/wali/santri/{id}/tabungan/limit` | Set limit jajan harian |
| POST | `/api/v1/wali/santri/{id}/tabungan/freeze` | Bekukan/aktifkan tabungan |
| GET | `/api/v1/wali/santri/{id}/transaksi` | Riwayat transaksi |
| GET | `/api/v1/wali/santri/{id}/jadwal` | Jadwal pelajaran |
| GET | `/api/v1/wali/santri/{id}/absensi` | Data absensi bulanan |
| GET | `/api/v1/wali/santri/{id}/akademik` | Nilai akademik per semester |
| GET | `/api/v1/wali/santri/{id}/prestasi` | Daftar prestasi |
| GET | `/api/v1/wali/santri/{id}/kesehatan` | Riwayat kesehatan |
| GET | `/api/v1/wali/santri/{id}/izin` | Daftar izin |
| POST | `/api/v1/wali/santri/{id}/izin` | Ajukan izin baru |
| GET | `/api/v1/wali/santri/{id}/kunjungan` | Daftar kunjungan |
| POST | `/api/v1/wali/santri/{id}/kunjungan` | Ajukan kunjungan |
| GET | `/api/v1/wali/santri/{id}/konseling` | Daftar sesi konseling |
| POST | `/api/v1/wali/santri/{id}/konseling` | Ajukan sesi konseling |
| GET | `/api/v1/wali/santri/{id}/presensi` | Presensi kegiatan pondok |

### External API (3rd Party)
| API | Fungsi | URL |
|---|---|---|
| aladhan.com | Jadwal sholat berdasarkan koordinat GPS | `api.aladhan.com/v1/timings` |

---

## 5. Environment Variables

### `.env.development` (lokal)
```env
VITE_API_BASE=http://localhost:8000
VITE_DEMO_MODE=true
```

### `.env.production` (wajib dibuat sebelum deploy)
```env
VITE_API_BASE=https://api.asy-syifaa.com
VITE_DEMO_MODE=false
```

---

## 6. Demo Mode

Saat `VITE_DEMO_MODE=true`, aplikasi berjalan **tanpa backend** menggunakan data dummy realistis dari `src/api/mock.ts`.

**Data demo tersedia:**
- Wali: Bapak Hasan Maulana
- Santri: Muhammad Farhan Maulana (VIII-A Tsanawiyah, NIS 2023.001)
- Jadwal sholat: Sumedang (hardcoded)
- Tagihan: SPP Mei 2026 belum bayar, April–Maret lunas
- Tabungan: Rp 247.500, limit jajan Rp 30.000/hari
- Hafalan: 12/15 juz (80%)
- Jadwal: Senin–Sabtu lengkap
- Absensi: 22 hadir, 1 sakit, 1 izin
- Prestasi: Juara 2 MTQ, wisuda 10 juz
- Akademik: 12 mapel, rata-rata 87.4, rank 3/28

---

## 7. Fitur Khusus

### Jadwal Sholat + Countdown
- Composable: `src/composables/useSholatTime.ts`
- Real mode: Geolocation → `aladhan.com` API → waktu sholat akurat
- Demo mode: Waktu hardcoded Sumedang
- Countdown real-time (update tiap 1 detik via `setInterval`)
- Highlight sholat berikutnya dengan dot kuning

### Kegiatan Santri Sekarang
- Fungsi: `getKegiatanSekarang()` di `src/api/mock.ts`
- Membaca jadwal hari ini berdasarkan `new Date().getDay()`
- Mencocokkan jam sekarang dengan slot jadwal
- Menampilkan badge `● BERLANGSUNG` atau `ISTIRAHAT`

### Marketplace (Disabled)
- Route: `/marketplace`
- Di dashboard: overlay abu-abu + label "BELUM TERSEDIA"
- Halaman detail: progress bar 65%, preview fitur, tombol notifikasi
- Diaktifkan cukup dengan menghapus overlay di `DashboardView.vue`

---

## 8. Design System

### Warna Brand
```css
--brand-50:  #f0fdf4
--brand-100: #dcfce7
--brand-500: #22c55e
--brand-600: #16a34a
--brand-700: #15803d   /* Utama */
--brand-800: #166534
--brand-900: #14532d
```

### Pola UI Konsisten
- **Header**: `bg-gradient-to-br from-brand-700 to-brand-800`, `pb-24/28`
- **Float Card**: `relative z-10 px-4 -mt-{n}` (konten mengapung di atas header)
- **Bottom Sheet**: `fixed bottom-0 z-50 bg-white rounded-t-3xl` + Transition `sheet-up`
- **Skeleton Loading**: `class="skeleton h-x w-x rounded"` (animasi shimmer)
- **Status Badge**: `bg-emerald-50 text-emerald-700` / `bg-red-50 text-red-700` / `bg-amber-50 text-amber-700`

---

## 9. Catatan Penting untuk Go-Live

1. **Hapus `VITE_DEMO_MODE=true`** dari env production
2. **Buat `.env.production`** dengan `VITE_API_BASE=https://api.asy-syifaa.com`
3. **ERP harus sudah online** dan semua 21 endpoint wali harus aktif
4. **CORS** di ERP harus allow `https://app.asy-syifaa.com`
5. **Sanctum** `SESSION_DOMAIN` harus include `.asy-syifaa.com`
6. **SSL** wajib aktif di semua subdomain (app, api, erp)
7. **Jadwal sholat** akan otomatis akurat berdasarkan GPS user
8. **Marketplace** masih disabled — aktifkan setelah backend siap

---

*Dokumen ini dibuat otomatis berdasarkan state kode pada 29 Mei 2026.*
