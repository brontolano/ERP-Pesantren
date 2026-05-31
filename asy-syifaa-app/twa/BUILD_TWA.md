# Build TWA → Play Store — Asy-Syifaa App

Panduan konversi PWA `app.asy-syifaa.com` menjadi aplikasi Android (AAB) untuk Google Play Store via **Bubblewrap (Trusted Web Activity)**.

| Item | Nilai |
|------|-------|
| Package ID | `com.asysyifaa.app` |
| Nama app | Asy-Syifaa — Pantau Santri Anda |
| Launcher | Asy-Syifaa |
| Host PWA | `https://app.asy-syifaa.com` |
| Manifest | `https://app.asy-syifaa.com/manifest.webmanifest` |
| Versi awal | 1.0.0 (versionCode 1) |

---

## Prasyarat (WAJIB sebelum build)

1. **PWA harus LIVE di HTTPS** di `https://app.asy-syifaa.com`
   - Saat ini host belum bisa diakses. Deploy dulu via `docker-compose up -d` di VPS (lihat `../docker-compose.yml`).
   - Verifikasi: `curl -I https://app.asy-syifaa.com/manifest.webmanifest` → harus 200.
2. **JDK 17** terpasang (`java -version`).
3. **Android SDK** (build-tools + platform). Bubblewrap bisa mengunduh otomatis saat `bubblewrap build` pertama.
4. **Bubblewrap CLI**: `npm install -g @bubblewrap/cli`.

---

## Langkah Build

Jalankan dari folder ini (`asy-syifaa-app/twa/`).

### 1. (Opsi A) Re-init dari manifest live — setelah PWA online
```bash
bubblewrap init --manifest https://app.asy-syifaa.com/manifest.webmanifest
```
Konfigurasi akan ditarik otomatis. Cocokkan dengan `twa-manifest.json` yang sudah disiapkan
(packageId `com.asysyifaa.app`, warna `#1f6b43`, dst).

### 1. (Opsi B) Pakai twa-manifest.json yang sudah ada
`twa-manifest.json` di folder ini sudah dikonfigurasi lengkap. Langsung lanjut ke build.

### 2. Buat keystore rilis (SEKALI saja — SIMPAN AMAN!)
> ⚠️ Password keystore dipilih & disimpan oleh pemilik. Jangan commit keystore ke git.
```bash
keytool -genkeypair -v \
  -keystore android.keystore \
  -alias asysyifaa \
  -keyalg RSA -keysize 2048 -validity 9125 \
  -dname "CN=Asy-Syifaa Wal Mahmuudiyyah, OU=IT, O=Ponpes Asy-Syifaa, L=Sumedang, ST=Jawa Barat, C=ID"
```

### 3. Ambil SHA-256 fingerprint untuk assetlinks
```bash
keytool -list -v -keystore android.keystore -alias asysyifaa | grep SHA256
```
Salin nilai (format `AA:BB:CC:...`) ke:
`../pwa/public/.well-known/assetlinks.json` → ganti `REPLACE_WITH_KEYSTORE_SHA256_FINGERPRINT`.

### 4. Build AAB + APK
```bash
bubblewrap build
```
Output:
- `app-release-bundle.aab` → diupload ke Play Console
- `app-release-signed.apk` → untuk uji coba di device

### 5. Play App Signing (penting!)
Setelah upload AAB pertama, Play Console akan **men-generate kunci signing-nya sendiri**.
Ambil SHA-256 dari Play Console → **Setup → App Integrity → App signing key certificate**,
lalu tambahkan ke `assetlinks.json` (slot kedua `REPLACE_WITH_PLAY_APP_SIGNING_SHA256_FINGERPRINT`).
Re-deploy PWA agar assetlinks ter-update. Tanpa ini, address bar TWA tidak hilang.

---

## assetlinks.json

Sudah disiapkan di `../pwa/public/.well-known/assetlinks.json`.
Karena ada di `pwa/public/`, file otomatis ikut ter-deploy ke
`https://app.asy-syifaa.com/.well-known/assetlinks.json` (nginx sudah dikonfigurasi).

Verifikasi setelah deploy:
```bash
curl https://app.asy-syifaa.com/.well-known/assetlinks.json
```

---

## Checklist Play Console (lihat juga docs/PLAYSTORE_LISTING.md)

- [ ] Hi-res icon 512×512 → `playstore-assets/playstore-icon-512.png` ✅
- [ ] Feature graphic 1024×500 → `playstore-assets/feature-graphic-1024x500.png` ✅
- [ ] Screenshot portrait min 2 (Dashboard, Hafalan, Tagihan, Izin) — TODO
- [ ] Privacy Policy URL (wajib) — TODO, host di asy-syifaa.com/privacy
- [ ] Target SDK 33+ (Bubblewrap default sudah ≥33)
- [ ] Upload AAB (bukan APK) ke track Internal/Closed dulu

---

## Status Saat Ini

| Tahap | Status |
|-------|--------|
| Ikon & branding (PWA/ERP/Web) | ✅ Selesai |
| Manifest PWA (icons + maskable) | ✅ Selesai |
| nginx serve `.well-known/assetlinks.json` | ✅ Dikonfigurasi |
| twa-manifest.json | ✅ Disiapkan |
| Keystore + SHA-256 fingerprint | ✅ Dibuat (lihat KEYSTORE_INFO.txt) |
| assetlinks.json (upload key) | ✅ Diisi fingerprint upload key |
| **Build signed AAB + APK** | ✅ **SELESAI** → `playstore-assets/asysyifaa-v1.0.0-release.aab` |
| Deploy PWA live HTTPS | ⏳ **PERLU AKSI** — app.asy-syifaa.com belum online |
| Tambah SHA-256 Play App Signing ke assetlinks | ⏳ Setelah upload AAB pertama |

## Catatan Build (30 Mei 2026)

- AAB di-build via Gradle langsung (JDK 21 Android Studio JBR + Android SDK lokal),
  bukan `bubblewrap build`, karena Bubblewrap mensyaratkan JDK 17 sedangkan yang
  terpasang JDK 21. Project Gradle sudah di-generate Bubblewrap dan signing config
  ditambahkan manual di `app/build.gradle`.
- Build dijalankan di path tanpa spasi (`C:\bw\twa`) untuk menghindari isu aapt2.
- Untuk re-build update: naikkan `versionCode`/`versionName` di `app/build.gradle`,
  lalu `./gradlew bundleRelease` (set JAVA_HOME ke JBR, sdk.dir di local.properties).
- Verifikasi AAB: `package=com.asysyifaa.app v1.0.0, minSdk 21, targetSdk 35`, ditandatangani
  CN=Asy-Syifaa Wal Mahmuudiyyah.
