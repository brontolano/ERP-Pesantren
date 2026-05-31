---
name: erp-asyifaa-iwetc
description: Kerangka kerja kontekstual I-W-E-T-C untuk eksekusi tugas ERP Asy-Syifaa secara relevan, minim perubahan, dan checklist-first. Gunakan saat menangani analisis, troubleshooting, implementasi fitur, atau keputusan teknis di repo ERP agar pemilihan skill, scope kerja, dan eskalasi selalu berbasis konteks operasional pesantren.
---

# ERP Asy-Syifaa I-W-E-T-C

Gunakan urutan ini sebelum menjalankan tugas teknis apa pun.

## I - Identify Context
Identifikasi siapa pengguna aktif, modul yang disentuh, gejala utama, urgensi, dan dampak operasional.
Klasifikasi cepat:
- Peran: `Ustadz/Ustadzah`, `Mu'alim`, `Mudir'aam`, `Abuya`, `Orang Tua`.
- Modul: keuangan, akademik, administrasi santri, presensi, portal/komunikasi.
- Severity: `URGENT`, `HIGH`, `MEDIUM`, `LOW`.

## W - World Context
Pahami batasan dunia nyata sistem:
- Ritme operasional harian pesantren, respons harus cepat dan praktis.
- Banyak pengguna non-teknis: hindari jargon rumit.
- Jalur intake `website -> ERP PPDB` dianggap sensitif dan harus dijaga.
- Deployment VPS bisa terpisah; validasi lokal tetap prioritas utama.

## E - Explain Context
Jelaskan ulang konteks kerja dalam kalimat singkat sebelum eksekusi:
1. masalah utama,
2. dampak,
3. batasan perubahan,
4. hasil akhir yang diharapkan.

Jika konteks belum lengkap, gunakan asumsi paling aman dan sebutkan asumsi tersebut secara eksplisit.

## T - Task Context
Turunkan konteks menjadi tugas konkret dan urut:
1. tentukan target file/modul,
2. tentukan perubahan minimum,
3. tentukan verifikasi lokal,
4. tentukan output yang harus terlihat user.

Pola eksekusi wajib:
- satu tugas sampai selesai,
- cek hasil,
- lanjut ke tugas berikutnya hanya jika tugas saat ini sudah stabil.

## C - Constants Context
Konstanta yang tidak boleh dilanggar:
- minimal invasive change; jangan tulis ulang alur yang sudah benar.
- jangan ubah integrasi kritikal tanpa alasan dan dampak yang jelas.
- jangan minta atau menyimpan password user.
- jangan hapus data/ubah konfigurasi sensitif tanpa approval.
- utamakan solusi yang jalan di lokal dan bisa diverifikasi.

## Skill Relevansi
Pilih skill pendamping berdasarkan tipe tugas:
- Troubleshooting ERP operasional: `erp-asyifaa-assistant`.
- Implementasi backend PHP/Laravel: `php-pro`.
- Review bug/risk: `coderabbit:code-review` bila diminta review formal.

Jika beberapa skill relevan, jalankan dengan urutan:
1. `erp-asyifaa-iwetc` (filter konteks),
2. skill domain teknis (mis. `php-pro`),
3. skill quality/review jika diminta.

## Format Output Jawaban
Saat merespons pengguna non-teknis:
1. sapaan sesuai peran,
2. identifikasi masalah singkat,
3. `Solusi cepat:` maksimal 5 langkah,
4. verifikasi hasil,
5. eskalasi jika belum selesai.
