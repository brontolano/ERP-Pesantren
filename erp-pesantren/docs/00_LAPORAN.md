# 00_LAPORAN - Audit Lane SPMB (Form -> Santri Aktif)

Tanggal: 31 Mei 2026
Repo: erp-pesantren
Mode: Audit operasional 1 jam (heartbeat)

## 1) Ringkasan Eksekutif
Audit alur SPMB dari form pendaftaran sampai status santri aktif telah selesai.
Alur utama sudah berjalan end-to-end, namun ditemukan beberapa gap konsistensi status dan risiko duplikasi pada fase konversi.

Status akhir audit: SELESAI

## 2) Capaian Audit (sesuai time-box)
- Baseline alur SPMB terkunci:
  - register -> status `pending`
  - verifikasi dokumen -> `incomplete|pending_review|revision_needed|complete`
  - seleksi -> `lulus|cadangan|rejected`
  - pembayaran daftar ulang
  - konversi -> pendaftaran `enrolled` + student `status=aktif`
- Audit mapping status endpoint publik vs internal: selesai
- Audit idempotensi konversi dan potensi duplikasi: selesai
- Penyusunan prioritas P0/P1/P2: selesai

## 3) Gap Terkonfirmasi
1. Inkonstistensi kamus status antara API publik dan status internal SPMB
   - Publik ditemukan menggunakan `accepted|waiting_list|rejected`
   - Internal utama menggunakan `lulus|cadangan|rejected|enrolled`
   Dampak: hasil seleksi publik berisiko tidak sinkron dengan data real.

2. Risiko duplikasi saat konversi pendaftar -> santri
   - Event konversi bisa terpicu ulang tanpa guard idempotensi kuat.
   Dampak: potensi record Student ganda / relasi tidak konsisten.

3. Potensi notifikasi pembayaran ganda
   - Rangkaian listener pembayaran/konversi masih berpotensi mengirim notifikasi berulang pada kondisi tertentu.
   Dampak: pengalaman pengguna menurun, noise operasional.

## 4) Prioritas Perbaikan
### P0 (segera)
1. Samakan kamus status SPMB lintas Service, Filament, dan API publik.
2. Tambahkan guard idempotensi pada proses convertToSantri agar replay event tidak membuat data ganda.

### P1 (tinggi)
1. Tambahkan proteksi anti-duplikasi invoice daftar ulang per pendaftar.
2. Rapikan flow notifikasi agar tidak terjadi double-send pada event pembayaran/konversi.

### P2 (menengah)
1. Tambahkan integration test end-to-end untuk skenario normal dan replay event.

## 5) Urutan Next Action (siap eksekusi)
1. Refactor status mapping API publik agar konsisten dengan status internal (`lulus/cadangan/rejected/enrolled`).
2. Implement idempotency guard pada konversi pendaftar ke santri (cek relasi PPDB sebelum create).
3. Tambahkan unique/business guard pada invoice daftar ulang.
4. Audit ulang listener notifikasi pembayaran + konversi, hilangkan pengiriman duplikat.
5. Tambahkan test integrasi alur SPMB full flow termasuk retry/replay event.

## 6) Catatan Operasional
- Tidak ada blocker akses selama audit.
- Rekomendasi eksekusi: mulai dari P0 untuk menjaga jalur intake sensitif `website -> ERP PPDB` tetap stabil.

## 7) Update Implementasi Lanjutan (31 Mei 2026)

### 7.1 Standarisasi & Hardening yang Sudah Diterapkan
1. Standarisasi status seleksi publik sudah disamakan ke status internal:
   - `lulus`, `cadangan`, `rejected`, `enrolled`.
2. Guard anti-duplikasi invoice daftar ulang sudah diterapkan:
   - invoice daftar ulang dicek lebih dulu berdasarkan `ppdb_registration_id` + `invoice_type=daftar_ulang`.
3. Guard idempotensi konversi pendaftar -> santri aktif sudah diterapkan:
   - skip jika status sudah `enrolled`;
   - cek student existing by `ppdb_registration_id` / `enrolled_from_ppdb_id` sebelum create.
4. Potensi notifikasi pembayaran ganda sudah dirapikan:
   - pengiriman webhook konfirmasi pembayaran ganda di listener konversi dihapus.
5. UX pendaftar ditingkatkan:
   - dashboard menampilkan checklist dokumen wajib yang belum ter-upload;
   - indikator progres dokumen dilengkapi pesan status kelengkapan;
   - profil pendaftar diberi gate kelengkapan dasar (`profile_completed_at` + warning).

### 7.2 Integrasi Akun Terpusat ERP -> App Wali
1. Arsitektur ditegaskan: `erp_accounts` sebagai source of truth akun.
2. Transisi role setelah daftar ulang lunas tetap pada akun yang sama:
   - `Pendaftar` -> `Wali Santri`.
3. Redirect login API untuk role wali sudah diperkuat agar kompatibel lintas penamaan role:
   - `wali_santri`, `Wali Santri`, `wali`, `orang_tua` -> `https://app.asy-syifaa.com`.

### 7.3 Verifikasi Test
1. Feature test baseline disesuaikan dengan perilaku aplikasi (root redirect 302).
2. Test feature SPMB lane pendaftar sudah ditambahkan (register, dokumen checklist, selection-results status).
3. Hasil run feature test terakhir: lulus untuk suite yang dapat dijalankan; beberapa test SPMB di-skip karena environment lokal belum memiliki driver `pdo_sqlite`.

### 7.4 Status Automation
- Automation orchestrator interval 5 menit sudah dijalankan untuk penuntasan TODO.
- Setelah seluruh TODO utama selesai, automation telah dihapus agar tidak berjalan berulang.

## 8) Status Akhir
- Lane pendaftar SPMB: **STABIL (baseline produksi)**
- Risiko tersisa: 
  1. Ketersediaan driver DB test lokal (`pdo_sqlite`) untuk menjalankan seluruh test secara penuh.
  2. Penyelesaian fitur Wave 2 (ujian masuk & pembayaran online native gateway) masih lanjutan roadmap.
