# Laporan Verifikasi Root Domain `asy-syifaa.com`

Tanggal: 2026-06-02  
Status: Terverifikasi pada level DNS authoritative + HTTP responder VPS

## 1) Ringkasan

Root domain `https://asy-syifaa.com` sudah terverifikasi mengarah ke VPS dan `index` pada host tersebut sudah terbaca dengan benar dari web server aktif.

Artinya, fondasi pembacaan website pada host root sudah ada. Jika browser user masih menampilkan `404 DEPLOYMENT_NOT_FOUND`, maka sumber masalahnya bukan file `index` di VPS, melainkan jalur resolver/cache yang masih menuju target lama.

## 2) Hasil Verifikasi

### A. DNS Authoritative

Nameserver aktif domain:
- `horizon.dns-parking.com`
- `orbit.dns-parking.com`

Jawaban authoritative untuk root domain:
- `asy-syifaa.com -> 187.77.116.167`

Jawaban authoritative untuk host terkait:
- `www.asy-syifaa.com -> CNAME asy-syifaa.com`
- `api.asy-syifaa.com -> 187.77.116.167`
- `app.asy-syifaa.com -> 187.77.116.167`
- `erp.asy-syifaa.com -> 187.77.116.167`

### B. HTTP Responder VPS

Pengujian langsung ke VPS dengan `Host: asy-syifaa.com` mengembalikan:

- `HTTP/1.1 200 OK`
- `Server: nginx/1.31.1`
- `X-Powered-By: PHP/8.2.31`

Ini membuktikan bahwa request yang benar-benar sampai ke VPS sudah dilayani oleh stack web aktif, bukan oleh Vercel dan bukan oleh halaman default error.

### C. Verifikasi Isi `index`

Konten HTML root yang terambil dari responder VPS memuat identitas website resmi, antara lain:

- `<title>Ponpes Asy-Syifaa Wal Mahmuudiyyah - Website</title>`
- metadata deskripsi resmi pesantren
- asset lokal seperti `/assets/css/main.css?v=20260519`

Kesimpulan bagian ini:
- `index` untuk root domain sudah terbaca.
- root domain tidak mengarah ke placeholder kosong.
- root domain tidak mengarah ke halaman `DEPLOYMENT_NOT_FOUND`.

## 3) Implikasi Operasional

Karena `index` root domain sudah terbaca dari host yang benar, maka prinsip routing web server untuk halaman lain mengikuti fondasi yang sama:

- bila file/route halaman tersedia di document root yang sama, halaman ikut terbaca;
- bila browser masih melihat error lama, itu bukan bukti bahwa `index` VPS rusak;
- validasi awal website harus selalu dimulai dari root domain karena jika `index` terbaca dari host yang benar, maka masalah halaman lain biasanya turun ke level file path, rewrite, atau cache.

Dengan kata lain:

> `index` harus terbaca dulu. Setelah itu, pembacaan semua halaman lain mengikuti host dan document root yang sama.

## 4) Status Masalah Saat Ini

Masalah yang dilihat user di browser:
- `404: NOT_FOUND`
- `Code: DEPLOYMENT_NOT_FOUND`

Status teknis setelah verifikasi:
- **bukan** berasal dari responder VPS yang sekarang aktif;
- **bukan** akibat `index` root domain hilang;
- paling mungkin berasal dari cache browser, cache DNS lokal, atau resolver yang masih membawa user ke target lama.

## 5) Catatan Perbaikan Lanjutan

Ada satu catatan konten yang tidak menyebabkan 404 namun perlu dirapikan di source website:

- `og:url` masih menunjuk ke `https://website-pondok.com/`

Ini tidak memblokir website hidup, tetapi sebaiknya diperbarui agar metadata sosial konsisten dengan domain produksi.

## 6) Kesimpulan

Kesimpulan final verifikasi ini:

1. Root domain `asy-syifaa.com` pada jalur authoritative sudah mengarah ke VPS `187.77.116.167`.
2. VPS merespons request root domain dengan `200 OK`.
3. `index` website Asy-Syifaa sudah terbaca dari VPS.
4. Secara arsitektur, jika `index` sudah terbaca dari host yang benar, maka halaman lain mengikuti pembacaan pada host/document root yang sama.
5. Error Vercel yang masih muncul di browser user bukan representasi kondisi live responder VPS saat ini.

## 7) Rekomendasi Singkat

- Jadikan root domain sebagai checkpoint utama verifikasi live.
- Jika user masih melihat Vercel 404, lakukan flush DNS/browser cache lebih dulu sebelum menyimpulkan website belum jalan.
- Setelah root domain stabil pada sisi user, lanjut verifikasi halaman penting seperti:
  - `/`
  - `/daftar-sekarang.php`
  - `/login`
  - halaman profil/galeri utama
