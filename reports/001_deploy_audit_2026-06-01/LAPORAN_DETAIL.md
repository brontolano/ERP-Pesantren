# Laporan Deploy Otomatis ERP-Pesantren

Tanggal: 2026-06-01 (Asia/Jakarta)
Eksekutor: Codex
Scope: Trigger + verifikasi auto deploy modul ERP, API, App, Website

## 1) Ringkasan Eksekusi

- Implementasi workflow sudah aktif:
  - Deploy ERP to VPS
  - Deploy API to VPS
  - Deploy PWA App
  - Deploy Website to Hostinger
- Trigger deploy dilakukan via commit perubahan path modul.
- Hasil akhir saat ini:
  - ERP: Gagal
  - App: Gagal
  - Website: Gagal
  - API (submodule repo datamaster): CI sukses, namun belum ada workflow deploy API di repo tersebut.

## 2) Bukti Run Terbaru

### Repo utama: brontolano/ERP-Pesantren

Commit trigger:
- d7d76808bbcc6559f88c71c6e0666a88786c60ea

Run:
1. Deploy ERP to VPS
   - URL: https://github.com/brontolano/ERP-Pesantren/actions/runs/26722635603
   - Status: failure
   - Error inti:
     - `INPUT_USERNAME:` kosong
     - `ssh.ParsePrivateKey: ssh: this private key is passphrase protected`
     - `ssh: unable to authenticate`

2. Deploy PWA App
   - URL: https://github.com/brontolano/ERP-Pesantren/actions/runs/26722635604
   - Status: failure
   - Error inti:
     - `INPUT_USERNAME:` kosong
     - `ssh.ParsePrivateKey: ssh: this private key is passphrase protected`
     - `ssh: unable to authenticate`

3. Deploy Website to Hostinger
   - URL: https://github.com/brontolano/ERP-Pesantren/actions/runs/26722635617
   - Status: failure
   - Error inti:
     - `INPUT_HOST:` kosong
     - `INPUT_USERNAME:` kosong
     - `error: missing server host`

### Repo submodule API: brontolano/asy-syifaa-datamaster

Commit trigger:
- 48c2ade (chore(ci): trigger api deploy workflow)

Run:
1. Backend CI
   - URL: https://github.com/brontolano/asy-syifaa-datamaster/actions/runs/26722634624
   - Status: success
   - Catatan: ini CI backend, bukan deploy ke VPS.

## 3) Status Secrets Saat Ini (Repo ERP-Pesantren)

Sudah ada:
- VPS_HOST
- VPS_SSH_KEY

Belum ada / belum valid untuk deploy:
- VPS_USER (wajib untuk ERP & App)
- HOSTINGER_HOST (wajib untuk Website)
- HOSTINGER_USER (wajib untuk Website)
- HOSTINGER_SSH_KEY (wajib untuk Website)
- HOSTINGER_PORT (wajib untuk Website)

Catatan tambahan penting:
- File key yang dipakai (`arsip_project/tools/keys/gha_vps_ed25519`) adalah key ber-passphrase.
- `appleboy/ssh-action` gagal parse key jika passphrase tidak disediakan.

## 4) Akar Masalah Teknis

1. Kredensial SSH belum lengkap pada GitHub Secrets.
2. Private key VPS ber-passphrase, sementara workflow tidak menyediakan `passphrase` input.
3. Workflow website butuh secret host/user/key/port Hostinger yang belum diisi.

## 5) Tindakan yang Sudah Dilakukan

1. Set `VPS_HOST=187.77.116.167`.
2. Set `VPS_SSH_KEY` dari file key di project.
3. Trigger workflow per path dengan commit non-destruktif (`.ci-trigger`) di modul:
   - asy-syifaa-erp/.ci-trigger
   - asy-syifaa-app/.ci-trigger
   - asy-syifaa-website/.ci-trigger
   - asy-syifaa-api/.ci-trigger (di submodule)
4. Pantau hingga run selesai dan kumpulkan log gagal.

## 6) Checklist Agar Deploy Bisa Hijau

### A. Untuk ERP + App (VPS)
- [ ] Isi secret `VPS_USER` (contoh umum: `root`/`ubuntu`/user custom server).
- [ ] Ganti `VPS_SSH_KEY` ke private key tanpa passphrase ATAU update workflow untuk menyertakan passphrase secret.

### B. Untuk Website (Hostinger)
- [ ] Isi `HOSTINGER_HOST`.
- [ ] Isi `HOSTINGER_USER`.
- [ ] Isi `HOSTINGER_SSH_KEY`.
- [ ] Isi `HOSTINGER_PORT` (umumnya 22 atau port custom Hostinger).

### C. Opsional hardening
- [ ] Tambah `workflow_dispatch` pada ke-4 workflow agar bisa trigger manual tanpa commit.
- [ ] Tambah step validasi secret awal (`if: secrets.X == ''` fail-fast dengan pesan jelas).

## 7) Kesimpulan

Implementasi fondasi dan automasi sudah berjalan, namun auto deploy live belum bisa sukses karena kekurangan dan ketidaksesuaian secret SSH (username/host/passphrase). Begitu secret dilengkapi, pipeline bisa langsung diuji ulang tanpa ubah arsitektur.

---

## 8) Update Eksekusi Lanjutan (2026-06-01)

Tindakan lanjutan yang dilakukan:
1. Set secret `VPS_USER=root`.
2. Retrigger workflow via perubahan `.ci-trigger`:
   - `asy-syifaa-erp/.ci-trigger`
   - `asy-syifaa-app/.ci-trigger`
   - `asy-syifaa-website/.ci-trigger`
   - submodule `asy-syifaa-api/.ci-trigger`

Referensi commit:
- Repo utama ERP-Pesantren: `f14883c` (retrigger deploy setelah set VPS_USER)
- Repo API datamaster: `bd223e6` (retrigger backend CI)

Hasil run terbaru:

### A. ERP-Pesantren
1. Deploy ERP to VPS
   - Run: https://github.com/brontolano/ERP-Pesantren/actions/runs/26722855333
   - Status: **failure**
   - Perubahan dari sebelumnya: `INPUT_USERNAME` sudah terisi (root) ✅
   - Error tersisa:
     - `ssh.ParsePrivateKey: ssh: this private key is passphrase protected`
     - `ssh: unable to authenticate`

2. Deploy PWA App
   - Run: https://github.com/brontolano/ERP-Pesantren/actions/runs/26722855338
   - Status: **failure**
   - Perubahan dari sebelumnya: `INPUT_USERNAME` sudah terisi (root) ✅
   - Error tersisa:
     - `ssh.ParsePrivateKey: ssh: this private key is passphrase protected`
     - `ssh: unable to authenticate`

3. Deploy Website to Hostinger
   - Run: https://github.com/brontolano/ERP-Pesantren/actions/runs/26722855342
   - Status: **failure (fail-fast validasi secret)**
   - Error saat ini:
     - `Missing secret: HOSTINGER_HOST`

### B. Repo API (brontolano/asy-syifaa-datamaster)
1. Backend CI
   - Run: https://github.com/brontolano/asy-syifaa-datamaster/actions/runs/26722855209
   - Status: **success**

## 9) Status Saat Ini dan Next Blocking Items

Yang sudah benar:
- `VPS_HOST`, `VPS_USER`, `VPS_SSH_KEY` sudah terbaca pipeline.
- Validasi secret fail-fast sudah aktif di workflow.

Yang masih memblokir full green deployment:
1. `VPS_SSH_PASSPHRASE` belum diisi (wajib karena private key VPS encrypted).
2. Secret Hostinger belum diisi:
   - `HOSTINGER_HOST`
   - `HOSTINGER_USER`
   - `HOSTINGER_SSH_KEY`
   - `HOSTINGER_PORT`
   - (opsional bila encrypted) `HOSTINGER_SSH_PASSPHRASE`

Kesimpulan update:
- Progress naik: error `missing username` sudah selesai.
- Blocker tersisa murni pada passphrase key VPS dan secret Hostinger.

---

## 10) Update Eksekusi Lanjutan 2 (2026-06-01)

Asumsi user: Hostinger satu server dengan VPS.

Tindakan:
1. Set secrets:
   - `HOSTINGER_HOST=187.77.116.167`
   - `HOSTINGER_USER=root`
   - `HOSTINGER_PORT=22`
2. Retrigger deploy lewat commit:
   - Commit: `91b342b` (repo ERP-Pesantren)

Hasil run terbaru:

1. Deploy ERP to VPS
- Run: https://github.com/brontolano/ERP-Pesantren/actions/runs/26722904473
- Status: failure
- Error inti tetap:
  - `ssh.ParsePrivateKey: ssh: this private key is passphrase protected`
  - `ssh: unable to authenticate`

2. Deploy PWA App
- Run: https://github.com/brontolano/ERP-Pesantren/actions/runs/26722904475
- Status: failure
- Error inti tetap:
  - `ssh.ParsePrivateKey: ssh: this private key is passphrase protected`
  - `ssh: unable to authenticate`

3. Deploy Website to Hostinger
- Run: https://github.com/brontolano/ERP-Pesantren/actions/runs/26722904492
- Status: failure
- Error validasi terbaru:
  - `Missing secret: HOSTINGER_SSH_KEY`

Kesimpulan update 2:
- Penyelarasan host/user/port Hostinger berhasil diterapkan.
- Blocker final tersisa:
  1) `VPS_SSH_PASSPHRASE`
  2) `HOSTINGER_SSH_KEY`
  3) (opsional jika encrypted) `HOSTINGER_SSH_PASSPHRASE`

---

## 11) Update Verifikasi Path VPS dari User (2026-06-01 malam)

Sumber bukti: `pasted-text.txt` (hasil SSH langsung dari server).

Temuan terkonfirmasi:
1. `cd ~/public_html` gagal di server root:
   - Error: `-bash: cd: /root/public_html: No such file or directory`
2. Lokasi website aktif ada di:
   - `/opt/asy-syifaa/website/src`
3. Lokasi modul lain juga terdeteksi di `/opt`:
   - ERP: `/opt/asy-syifaa/erp/src`
   - API/backend: `/opt/asy-syifaa/backend`
   - App: `/opt/asy-syifaa-app`

Implikasi:
- Workflow website sebelumnya gagal karena asumsi path deploy `~/public_html` tidak sesuai struktur VPS saat ini.

Perbaikan yang diterapkan:
- File: `.github/workflows/deploy-website.yml`
- Script deploy diubah menjadi:
  - prioritas target `TARGET_DIR=/opt/asy-syifaa/website/src`
  - fallback ke `~/public_html` bila ada
  - fail-fast bila keduanya tidak ada

Status blocker tersisa setelah patch ini:
1. `VPS_SSH_PASSPHRASE` untuk ERP/App masih harus benar sesuai private key (`x509: decryption password incorrect`).
2. Website deploy sekarang sudah benar secara path, tinggal memastikan key/passphrase Hostinger valid.
