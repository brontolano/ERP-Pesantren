/**
 * DEMO MODE — data dummy realistis untuk testing PWA tanpa backend
 * Aktifkan dengan VITE_DEMO_MODE=true di .env.development
 */

// ── Delay simulasi network ─────────────────────────────────────
const delay = (ms = 400) => new Promise(r => setTimeout(r, ms))

// ══════════════════════════════════════════════════════════════
// MASTER DATA DEMO
// ══════════════════════════════════════════════════════════════

export const DEMO_USER = {
  id: 1,
  username: 'wali_demo',
  full_name: 'Bapak Hasan Maulana',
  phone: '08122334455',
  email: 'hasan.maulana@gmail.com',
  roles: ['Wali Santri'],
}

export const DEMO_SANTRI = [
  {
    id: 101,
    nis: '2023.001',
    nama: 'Muhammad Farhan Maulana',
    kelas: 'VIII-A',
    jenjang: 'Tsanawiyah',
    rombel: 'Ibnu Sina',
    status: 'aktif',
    tahun_masuk: '2023',
    gender: 'L',
    tanggal_lahir: '2010-04-15',
    tempat_lahir: 'Sumedang',
    jalur_masuk: 'Reguler',
    tunggakan_bulan: 1,
    spp_amount: 650000,
    alamat: 'Jl. Raya Paseh No. 12, RT 03/RW 05',
    desa_kelurahan: 'Cimalaka',
    kecamatan: 'Cimalaka',
    kab_kota: 'Kabupaten Sumedang',
    provinsi: 'Jawa Barat',
    kode_pos: '45353',
    nama_ayah: 'Hasan Maulana',
    nama_ibu: 'Siti Rahmawati',
    no_hp_ayah: '08122334455',
    no_hp_ibu: '08577889900',
    golongan_darah: 'O',
    tinggi_badan: 158,
    berat_badan: 47,
    riwayat_penyakit: 'Tidak ada',
    alergi: 'Tidak ada',
  },
]

// ── Status Harian ──────────────────────────────────────────────
export const DEMO_STATUS_HARIAN = {
  hari_ini: [
    {
      id: 1,
      tanggal: new Date().toISOString().split('T')[0],
      sesi: 'Shubuh & Pagi',
      status_kehadiran: 'hadir',
      status_label: 'Hadir',
      status_kesehatan: 'sehat',
      kesehatan_label: 'Sehat',
      keterangan: null,
    },
  ],
  riwayat: [
    { id: 2, tanggal: '2026-05-28', sesi: 'Shubuh & Pagi', status_kehadiran: 'hadir', status_label: 'Hadir', status_kesehatan: 'sehat', kesehatan_label: 'Sehat', keterangan: null },
    { id: 3, tanggal: '2026-05-27', sesi: 'Shubuh & Pagi', status_kehadiran: 'hadir', status_label: 'Hadir', status_kesehatan: 'sehat', kesehatan_label: 'Sehat', keterangan: null },
    { id: 4, tanggal: '2026-05-26', sesi: 'Shubuh & Pagi', status_kehadiran: 'sakit', status_label: 'Sakit', status_kesehatan: 'sakit', kesehatan_label: 'Sakit Ringan', keterangan: 'Flu' },
    { id: 5, tanggal: '2026-05-25', sesi: 'Shubuh & Pagi', status_kehadiran: 'hadir', status_label: 'Hadir', status_kesehatan: 'sehat', kesehatan_label: 'Sehat', keterangan: null },
  ],
  rekap_bulan: { hadir: 22, sakit: 1, izin: 0, alfa: 0 },
}

// ── Hafalan ────────────────────────────────────────────────────
export const DEMO_HAFALAN = {
  progress: {
    total_juz_quran: 12,
    target_juz_quran: 15,
    persen_quran: 80,
    total_hadist: 45,
    target_hadist: 100,
    update_terakhir: '2026-05-27',
    catatan: 'Alhamdulillah konsisten. Perlu peningkatan di tajwid mad.',
  },
  riwayat_setor: [
    { id: 1, tanggal: '2026-05-27', kategori: 'Al-Quran', pencapaian: 'Surah An-Naba (Juz 30)', jenis: 'setoran', jenis_label: 'Setoran Baru', nilai: 'A', nilai_label: 'Mumtaz', catatan: 'Lancar dan tartil', ustadz: 'Ust. Ahmad Fauzi' },
    { id: 2, tanggal: '2026-05-25', kategori: 'Al-Quran', pencapaian: 'Juz 12 (muraja\'ah)', jenis: 'murajaah', jenis_label: 'Muraja\'ah', nilai: 'B', nilai_label: 'Jayyid', catatan: 'Ada beberapa tempat yang perlu diperbaiki', ustadz: 'Ust. Ahmad Fauzi' },
    { id: 3, tanggal: '2026-05-22', kategori: 'Hadist', pencapaian: 'Hadist Arbain No. 43-45', jenis: 'setoran', jenis_label: 'Setoran Baru', nilai: 'A', nilai_label: 'Mumtaz', catatan: 'Hafalan dan makna sangat baik', ustadz: 'Ust. Ridwan Hakim' },
    { id: 4, tanggal: '2026-05-20', kategori: 'Al-Quran', pencapaian: 'Juz 11 (muraja\'ah)', jenis: 'murajaah', jenis_label: 'Muraja\'ah', nilai: 'A', nilai_label: 'Mumtaz', catatan: 'Sangat baik', ustadz: 'Ust. Ahmad Fauzi' },
    { id: 5, tanggal: '2026-05-18', kategori: 'Doa & Dzikir', pencapaian: "Doa Qunut & Doa Setelah Sholat", jenis: 'setoran', jenis_label: 'Setoran Baru', nilai: 'A', nilai_label: 'Mumtaz', catatan: 'Hafalan sempurna', ustadz: 'Ust. Ridwan Hakim' },
  ],
}

// ── Tagihan ────────────────────────────────────────────────────
export const DEMO_TAGIHAN = {
  total_tunggakan: 650000,
  invoices: [
    {
      id: 1,
      keterangan: 'SPP Mei 2026',
      status: 'unpaid',
      due_date: '2026-05-15',
      total: 650000,
      terbayar: 0,
      items: [
        { id: 1, nama_item: 'SPP Bulanan', nominal: 500000 },
        { id: 2, nama_item: 'Iuran Kegiatan', nominal: 100000 },
        { id: 3, nama_item: 'Iuran Kebersihan', nominal: 50000 },
      ],
    },
    {
      id: 2,
      keterangan: 'SPP April 2026',
      status: 'paid',
      due_date: '2026-04-15',
      total: 650000,
      terbayar: 650000,
      items: [
        { id: 4, nama_item: 'SPP Bulanan', nominal: 500000 },
        { id: 5, nama_item: 'Iuran Kegiatan', nominal: 100000 },
        { id: 6, nama_item: 'Iuran Kebersihan', nominal: 50000 },
      ],
    },
    {
      id: 3,
      keterangan: 'SPP Maret 2026',
      status: 'paid',
      due_date: '2026-03-15',
      total: 650000,
      terbayar: 650000,
      items: [
        { id: 7, nama_item: 'SPP Bulanan', nominal: 500000 },
        { id: 8, nama_item: 'Iuran Kegiatan', nominal: 100000 },
        { id: 9, nama_item: 'Iuran Kebersihan', nominal: 50000 },
      ],
    },
    {
      id: 4,
      keterangan: 'Biaya Seragam 2026',
      status: 'partial',
      due_date: '2026-02-28',
      total: 450000,
      terbayar: 200000,
      items: [
        { id: 10, nama_item: 'Seragam Putih', nominal: 150000 },
        { id: 11, nama_item: 'Seragam Batik', nominal: 175000 },
        { id: 12, nama_item: 'Seragam Olahraga', nominal: 125000 },
      ],
    },
  ],
}

// ── Tabungan ───────────────────────────────────────────────────
export const DEMO_TABUNGAN = {
  saldo: 247500,
  limit_harian: 30000,
  is_frozen: false,
  transaksi_hari_ini: 12500,
  saldo_dapat_dipakai: 17500,
  last_transaction_at: '2026-05-29',
  tunggakan_prioritas: 0,
  topup_jajan_locked: false,
}

// ── Transaksi ──────────────────────────────────────────────────
export const DEMO_TRANSAKSI = [
  { id: 1, tanggal: '2026-05-29', keterangan: 'Jajan Kantin', tipe: 'keluar', nominal: 8500, saldo_akhir: 247500 },
  { id: 2, tanggal: '2026-05-29', keterangan: 'Jajan Kantin - Sarapan', tipe: 'keluar', nominal: 4000, saldo_akhir: 256000 },
  { id: 3, tanggal: '2026-05-28', keterangan: 'Setoran Wali', tipe: 'masuk', nominal: 100000, saldo_akhir: 260000 },
  { id: 4, tanggal: '2026-05-28', keterangan: 'Jajan Kantin', tipe: 'keluar', nominal: 12000, saldo_akhir: 160000 },
  { id: 5, tanggal: '2026-05-27', keterangan: 'Jajan Kantin', tipe: 'keluar', nominal: 7500, saldo_akhir: 172000 },
  { id: 6, tanggal: '2026-05-26', keterangan: 'Jajan Kantin', tipe: 'keluar', nominal: 5000, saldo_akhir: 179500 },
  { id: 7, tanggal: '2026-05-25', keterangan: 'Setoran Wali', tipe: 'masuk', nominal: 200000, saldo_akhir: 184500 },
  { id: 8, tanggal: '2026-05-25', keterangan: 'Jajan Kantin', tipe: 'keluar', nominal: 9000, saldo_akhir: 184500 },
  { id: 9, tanggal: '2026-05-24', keterangan: 'Jajan Kantin', tipe: 'keluar', nominal: 11000, saldo_akhir: 193500 },
  { id: 10, tanggal: '2026-05-23', keterangan: 'Jajan Kantin', tipe: 'keluar', nominal: 6000, saldo_akhir: 204500 },
]

// ── Jadwal ─────────────────────────────────────────────────────
export const DEMO_JADWAL = {
  0: [], // Minggu
  1: [ // Senin
    { id: 1, jam_mulai: '07.30', jam_selesai: '08.30', mata_pelajaran: 'Matematika', guru: 'Ust. Andi Susanto', ruang: 'Kelas VIII-A' },
    { id: 2, jam_mulai: '08.30', jam_selesai: '09.30', mata_pelajaran: 'Bahasa Arab', guru: 'Ust. Fathurrahman', ruang: 'Kelas VIII-A' },
    { id: 3, jam_mulai: '09.45', jam_selesai: '10.45', mata_pelajaran: 'Al-Quran & Tajwid', guru: 'Ust. Ahmad Fauzi', ruang: 'Musholla' },
    { id: 4, jam_mulai: '10.45', jam_selesai: '11.45', mata_pelajaran: 'Fiqih', guru: 'Ust. Maman Abdurrahman', ruang: 'Kelas VIII-A' },
    { id: 5, jam_mulai: '13.30', jam_selesai: '14.30', mata_pelajaran: 'Bahasa Indonesia', guru: 'Bu Dewi Rahayu', ruang: 'Kelas VIII-A' },
    { id: 6, jam_mulai: '14.30', jam_selesai: '15.30', mata_pelajaran: 'IPA Terpadu', guru: 'Bu Sari Indah', ruang: 'Kelas VIII-A' },
  ],
  2: [ // Selasa
    { id: 7, jam_mulai: '07.30', jam_selesai: '08.30', mata_pelajaran: 'Bahasa Inggris', guru: 'Bu Nita Sanjaya', ruang: 'Kelas VIII-A' },
    { id: 8, jam_mulai: '08.30', jam_selesai: '09.30', mata_pelajaran: 'IPS Terpadu', guru: 'Pak Dedi Kurniawan', ruang: 'Kelas VIII-A' },
    { id: 9, jam_mulai: '09.45', jam_selesai: '10.45', mata_pelajaran: 'Hadist', guru: 'Ust. Ridwan Hakim', ruang: 'Kelas VIII-A' },
    { id: 10, jam_mulai: '10.45', jam_selesai: '11.45', mata_pelajaran: 'Aqidah Akhlaq', guru: 'Ust. Syamsul Bahri', ruang: 'Kelas VIII-A' },
    { id: 11, jam_mulai: '13.30', jam_selesai: '15.30', mata_pelajaran: 'Prakarya & Kewirausahaan', guru: 'Pak Hendra', ruang: 'Lab Ketrampilan' },
  ],
  3: [ // Rabu
    { id: 12, jam_mulai: '07.30', jam_selesai: '09.30', mata_pelajaran: 'Matematika', guru: 'Ust. Andi Susanto', ruang: 'Kelas VIII-A' },
    { id: 13, jam_mulai: '09.45', jam_selesai: '10.45', mata_pelajaran: 'Bahasa Arab', guru: 'Ust. Fathurrahman', ruang: 'Kelas VIII-A' },
    { id: 14, jam_mulai: '10.45', jam_selesai: '11.45', mata_pelajaran: 'Shorof & Nahwu', guru: 'Ust. Fathurrahman', ruang: 'Kelas VIII-A' },
    { id: 15, jam_mulai: '13.30', jam_selesai: '14.30', mata_pelajaran: 'PKn', guru: 'Bu Hani Lestari', ruang: 'Kelas VIII-A' },
    { id: 16, jam_mulai: '14.30', jam_selesai: '15.30', mata_pelajaran: 'Seni Budaya', guru: 'Pak Rudi', ruang: 'Kelas VIII-A' },
  ],
  4: [ // Kamis
    { id: 17, jam_mulai: '07.30', jam_selesai: '08.30', mata_pelajaran: 'Al-Quran & Tajwid', guru: 'Ust. Ahmad Fauzi', ruang: 'Musholla' },
    { id: 18, jam_mulai: '08.30', jam_selesai: '09.30', mata_pelajaran: 'Bahasa Inggris', guru: 'Bu Nita Sanjaya', ruang: 'Lab Bahasa' },
    { id: 19, jam_mulai: '09.45', jam_selesai: '10.45', mata_pelajaran: 'IPA Terpadu', guru: 'Bu Sari Indah', ruang: 'Lab IPA' },
    { id: 20, jam_mulai: '10.45', jam_selesai: '11.45', mata_pelajaran: 'Fiqih', guru: 'Ust. Maman Abdurrahman', ruang: 'Kelas VIII-A' },
    { id: 21, jam_mulai: '13.30', jam_selesai: '14.30', mata_pelajaran: 'Bahasa Indonesia', guru: 'Bu Dewi Rahayu', ruang: 'Kelas VIII-A' },
  ],
  5: [ // Jumat
    { id: 22, jam_mulai: '07.30', jam_selesai: '08.30', mata_pelajaran: 'Olahraga & Kesehatan', guru: 'Pak Budi Santoso', ruang: 'Lapangan' },
    { id: 23, jam_mulai: '08.30', jam_selesai: '09.30', mata_pelajaran: 'Muhadharoh', guru: 'Ust. Syamsul Bahri', ruang: 'Aula' },
    { id: 24, jam_mulai: '09.45', jam_selesai: '10.45', mata_pelajaran: 'Pramuka / Ekskul', guru: 'Pembina Pramuka', ruang: 'Lapangan' },
  ],
  6: [ // Sabtu
    { id: 25, jam_mulai: '07.30', jam_selesai: '09.30', mata_pelajaran: 'Bersih-bersih (Ro\'an)', guru: 'Pengurus', ruang: 'Seluruh Area Pesantren' },
    { id: 26, jam_mulai: '09.45', jam_selesai: '11.00', mata_pelajaran: 'Mudzakarah / Belajar Mandiri', guru: '-', ruang: 'Kelas VIII-A' },
  ],
}

// ── Absensi ────────────────────────────────────────────────────
function generateAbsensi(year: number, month: number) {
  const days: any[] = []
  const now = new Date()
  const daysInMonth = new Date(year, month, 0).getDate()
  const statuses: Record<number, string> = { 26: 'sakit', 14: 'izin' }
  for (let d = 1; d <= daysInMonth; d++) {
    const date = new Date(year, month - 1, d)
    if (date > now) break
    if (date.getDay() === 0) continue // Skip Minggu
    const st = statuses[d] ?? 'hadir'
    days.push({ tanggal: `${year}-${String(month).padStart(2,'0')}-${String(d).padStart(2,'0')}`, status: st })
  }
  return days
}

export const DEMO_ABSENSI = {
  bulan: 5,
  tahun: 2026,
  hari: generateAbsensi(2026, 5),
  rekap: { hadir: 22, sakit: 1, izin: 1, alfa: 0 },
}

// ── Prestasi ───────────────────────────────────────────────────
export const DEMO_PRESTASI = {
  nilai_ekskul: 88,
  nilai_khidmah: 92,
  nilai_disiplin: 85,
  total_poin: 2450,
  list: [
    { id: 1, kategori: 'akademik', nama: 'Juara 2 MTQ Tingkat Kecamatan', tanggal: '2026-04-10', tingkat: 'Kecamatan', medali: 'Perak', keterangan: 'Cabang tilawah' },
    { id: 2, kategori: 'hafalan', nama: 'Wisuda Hafidz 10 Juz', tanggal: '2026-03-15', tingkat: 'Internal', medali: 'Emas', keterangan: 'Penghargaan dari pesantren' },
    { id: 3, kategori: 'akademik', nama: 'Siswa Teladan Semester I', tanggal: '2026-01-20', tingkat: 'Internal', medali: 'Emas', keterangan: 'Nilai akademik tertinggi di kelas' },
    { id: 4, kategori: 'ekskul', nama: 'Juara 1 Futsal Antar Pesantren', tanggal: '2025-11-05', tingkat: 'Kabupaten', medali: 'Emas', keterangan: 'Turnamen futsal se-Sumedang' },
    { id: 5, kategori: 'ekskul', nama: 'Peserta Lomba Pidato Bahasa Arab', tanggal: '2025-09-20', tingkat: 'Provinsi', medali: null, keterangan: 'Mewakili pesantren di tingkat Jabar' },
  ],
}

// ── Akademik ───────────────────────────────────────────────────
export const DEMO_AKADEMIK = {
  semester_aktif: '2025/2026 Genap',
  ipk: 87.4,
  rank_kelas: 3,
  total_siswa: 28,
  mata_pelajaran: [
    { nama: 'Al-Quran & Tajwid', nilai: 95, guru: 'Ust. Ahmad Fauzi', grade: 'A' },
    { nama: 'Hadist', nilai: 92, guru: 'Ust. Ridwan Hakim', grade: 'A' },
    { nama: 'Fiqih', nilai: 88, guru: 'Ust. Maman Abdurrahman', grade: 'A' },
    { nama: 'Aqidah Akhlaq', nilai: 90, guru: 'Ust. Syamsul Bahri', grade: 'A' },
    { nama: 'Bahasa Arab', nilai: 85, guru: 'Ust. Fathurrahman', grade: 'B' },
    { nama: 'Shorof & Nahwu', nilai: 82, guru: 'Ust. Fathurrahman', grade: 'B' },
    { nama: 'Matematika', nilai: 78, guru: 'Ust. Andi Susanto', grade: 'B' },
    { nama: 'Bahasa Indonesia', nilai: 84, guru: 'Bu Dewi Rahayu', grade: 'B' },
    { nama: 'Bahasa Inggris', nilai: 80, guru: 'Bu Nita Sanjaya', grade: 'B' },
    { nama: 'IPA Terpadu', nilai: 83, guru: 'Bu Sari Indah', grade: 'B' },
    { nama: 'IPS Terpadu', nilai: 86, guru: 'Pak Dedi Kurniawan', grade: 'B' },
    { nama: 'PKn', nilai: 89, guru: 'Bu Hani Lestari', grade: 'A' },
  ],
}

// ── Kesehatan ──────────────────────────────────────────────────
export const DEMO_KESEHATAN = [
  { id: 1, tanggal: '2026-05-26', keluhan: 'Flu dan pilek', tindakan: 'Istirahat di UKS, diberikan obat flu', dokter: 'Bidan Rini', status: 'sembuh', tingkat: 'ringan' },
  { id: 2, tanggal: '2026-04-03', keluhan: 'Sakit kepala', tindakan: 'Istirahat, diberikan paracetamol', dokter: 'Bidan Rini', status: 'sembuh', tingkat: 'ringan' },
  { id: 3, tanggal: '2026-02-18', keluhan: 'Nyeri perut', tindakan: 'Dirujuk ke klinik luar untuk pemeriksaan lebih lanjut', dokter: 'dr. Hendra Sp.PD', status: 'sembuh', tingkat: 'sedang' },
  { id: 4, tanggal: '2025-11-12', keluhan: 'Demam tinggi (39.5°C)', tindakan: 'Opname 2 hari di UKS, diberikan infus dan antibiotik', dokter: 'dr. Hendra Sp.PD', status: 'sembuh', tingkat: 'berat' },
]

// ── Izin ───────────────────────────────────────────────────────
export const DEMO_IZIN = [
  { id: 1, jenis_izin: 'Pulang', tanggal_mulai: '2026-04-10', tanggal_selesai: '2026-04-12', alasan: 'Menghadiri acara keluarga (pernikahan adik)', status: 'disetujui' },
  { id: 2, jenis_izin: 'Sakit', tanggal_mulai: '2026-05-26', tanggal_selesai: '2026-05-26', alasan: 'Sakit flu, tidak dapat mengikuti kegiatan', status: 'disetujui' },
  { id: 3, jenis_izin: 'Kegiatan Luar', tanggal_mulai: '2026-06-05', tanggal_selesai: '2026-06-05', alasan: 'Lomba MTQ tingkat kabupaten', status: 'menunggu' },
]

// ── Kunjungan ──────────────────────────────────────────────────
export const DEMO_KUNJUNGAN = [
  { id: 1, nama_pengunjung: 'Hasan Maulana & Siti Rahmawati', hubungan: 'Orang Tua', tanggal_kunjungan: '2026-05-04', jam_mulai: '08.00', jam_selesai: '14.00', status: 'disetujui' },
  { id: 2, nama_pengunjung: 'Hasan Maulana', hubungan: 'Orang Tua', tanggal_kunjungan: '2026-04-06', jam_mulai: '08.00', jam_selesai: '12.00', status: 'disetujui' },
  { id: 3, nama_pengunjung: 'Siti Rahmawati & Adik-adik', hubungan: 'Orang Tua', tanggal_kunjungan: '2026-06-01', jam_mulai: '08.00', jam_selesai: '16.00', status: 'menunggu' },
]

// ── Konseling ──────────────────────────────────────────────────
export const DEMO_KONSELING = [
  { id: 1, topik: 'Perkembangan hafalan dan motivasi', catatan: 'Farhan membutuhkan dukungan lebih dari keluarga dalam hal semangat menghafal. Disarankan untuk sering berkomunikasi dan memberikan apresiasi.', tanggal: '2026-04-15', konselor: 'Ust. Syamsul Bahri', status: 'selesai' },
]

// ── Presensi Kegiatan ──────────────────────────────────────────
export const DEMO_PRESENSI = [
  { id: 1, kegiatan: 'Sholat Shubuh Berjamaah', sesi: 'Shubuh', tanggal: '2026-05-29', hadir: true },
  { id: 2, kegiatan: 'Sholat Dhuhur Berjamaah', sesi: 'Dhuhur', tanggal: '2026-05-29', hadir: true },
  { id: 3, kegiatan: 'Sholat Ashr Berjamaah', sesi: 'Ashr', tanggal: '2026-05-29', hadir: true },
  { id: 4, kegiatan: 'Sholat Maghrib Berjamaah', sesi: 'Maghrib', tanggal: '2026-05-28', hadir: true },
  { id: 5, kegiatan: 'Sholat Isya Berjamaah', sesi: 'Isya', tanggal: '2026-05-28', hadir: true },
  { id: 6, kegiatan: 'Muhadharoh (Latihan Pidato)', sesi: 'Jumat Pagi', tanggal: '2026-05-29', hadir: true },
  { id: 7, kegiatan: 'Sholat Shubuh Berjamaah', sesi: 'Shubuh', tanggal: '2026-05-27', hadir: false },
  { id: 8, kegiatan: 'Pengajian Kitab Kuning', sesi: 'Ba\'da Isya', tanggal: '2026-05-27', hadir: true },
  { id: 9, kegiatan: 'Ro\'an (Bersih-bersih)', sesi: 'Sabtu Pagi', tanggal: '2026-05-24', hadir: true },
  { id: 10, kegiatan: 'Sholat Tahajud Berjamaah', sesi: 'Tengah Malam', tanggal: '2026-05-23', hadir: false },
  { id: 11, kegiatan: 'Pengajian Kitab Kuning', sesi: 'Ba\'da Isya', tanggal: '2026-05-22', hadir: true },
  { id: 12, kegiatan: 'Olahraga Pagi', sesi: 'Jumat Pagi', tanggal: '2026-05-22', hadir: true },
]

// ══════════════════════════════════════════════════════════════
// MOCK API — dipanggil saat VITE_DEMO_MODE=true
// ══════════════════════════════════════════════════════════════

export const mockApi = {
  getSantri: async () => { await delay(); return DEMO_SANTRI },

  getStatusHarian: async (_id: number) => { await delay(); return DEMO_STATUS_HARIAN },

  getHafalan: async (_id: number) => { await delay(); return DEMO_HAFALAN },

  tagihan: async (_id: number) => { await delay(); return DEMO_TAGIHAN },

  getTagihan: async (_id: number) => { await delay(); return DEMO_TAGIHAN },

  tabungan: async (_id: number) => { await delay(); return DEMO_TABUNGAN },

  transaksi: async (_id: number) => { await delay(); return DEMO_TRANSAKSI },

  setLimitJajan: async (_id: number, limit: number) => { await delay(200); DEMO_TABUNGAN.limit_harian = limit; DEMO_TABUNGAN.saldo_dapat_dipakai = DEMO_TABUNGAN.is_frozen ? 0 : Math.max(0, limit - DEMO_TABUNGAN.transaksi_hari_ini); return { ok: true } },

  freezeTabungan: async (_id: number, frozen: boolean) => { await delay(200); DEMO_TABUNGAN.is_frozen = frozen; DEMO_TABUNGAN.saldo_dapat_dipakai = frozen ? 0 : Math.max(0, DEMO_TABUNGAN.limit_harian - DEMO_TABUNGAN.transaksi_hari_ini); return { ok: true } },

  jadwal: async (_id: number) => { await delay(); return DEMO_JADWAL },

  absensi: async (_id: number) => { await delay(); return DEMO_ABSENSI },

  prestasi: async (_id: number) => { await delay(); return DEMO_PRESTASI },

  akademik: async (_id: number) => { await delay(); return DEMO_AKADEMIK },

  kesehatan: async (_id: number) => { await delay(); return DEMO_KESEHATAN },

  daftarIzin: async (_id: number) => { await delay(); return DEMO_IZIN },

  getIzin: async (_id: number) => { await delay(); return DEMO_IZIN },

  ajukanIzin: async (_id: number, payload: any) => {
    await delay(600)
    const newIzin = { id: Date.now(), ...payload, status: 'menunggu' }
    DEMO_IZIN.unshift(newIzin)
    return { ok: true, data: newIzin }
  },

  daftarKunjungan: async (_id: number) => { await delay(); return DEMO_KUNJUNGAN },

  ajukanKunjungan: async (_id: number, payload: any) => {
    await delay(600)
    const newK = { id: Date.now(), ...payload, jam_mulai: '08.00', jam_selesai: '16.00', status: 'menunggu' }
    DEMO_KUNJUNGAN.unshift(newK)
    return { ok: true }
  },

  daftarKonseling: async (_id: number) => { await delay(); return DEMO_KONSELING },

  ajukanKonseling: async (_id: number, payload: any) => {
    await delay(600)
    const newK = { id: Date.now(), ...payload, tanggal: new Date().toISOString().split('T')[0], konselor: 'Ust. Syamsul Bahri', status: 'menunggu' }
    DEMO_KONSELING.unshift(newK)
    return { ok: true }
  },

  presensi: async (_id: number) => { await delay(); return DEMO_PRESENSI },

  uploadBuktiBayar: async () => { await delay(800); return { ok: true, message: 'Demo: bukti berhasil dikirim (simulasi)' } },
}

// ══════════════════════════════════════════════════════════════
// HELPER: Kegiatan Santri Sekarang (dari jadwal hari ini)
// ══════════════════════════════════════════════════════════════

export interface KegiatanSekarang {
  mata_pelajaran: string
  guru: string
  ruang: string
  jam_mulai: string
  jam_selesai: string
  status: 'berlangsung' | 'istirahat' | 'selesai'
}

export function getKegiatanSekarang(): KegiatanSekarang | null {
  const now   = new Date()
  const day   = now.getDay() // 0=Ahad...6=Sabtu
  const jadwal = (DEMO_JADWAL as Record<number, any[]>)[day] ?? []
  if (!jadwal.length) return null

  // Konversi jam ke menit: "07.30" → 450
  const toMin = (t: string) => {
    const [h, m] = t.split('.').map(Number)
    return h * 60 + (m || 0)
  }
  const nowMin = now.getHours() * 60 + now.getMinutes()

  // Cari pelajaran yang sedang berlangsung
  const current = jadwal.find(j => toMin(j.jam_mulai) <= nowMin && nowMin < toMin(j.jam_selesai))
  if (current) {
    return {
      mata_pelajaran: current.mata_pelajaran,
      guru:           current.guru,
      ruang:          current.ruang,
      jam_mulai:      current.jam_mulai,
      jam_selesai:    current.jam_selesai,
      status:         'berlangsung',
    }
  }

  // Cek apakah sedang istirahat (antara dua pelajaran)
  const next = jadwal.find(j => toMin(j.jam_mulai) > nowMin)
  const prev = [...jadwal].reverse().find(j => toMin(j.jam_selesai) <= nowMin)
  if (prev && next && toMin(next.jam_mulai) - nowMin <= 20) {
    return {
      mata_pelajaran: `Istirahat (menjelang ${next.mata_pelajaran})`,
      guru:           '',
      ruang:          '',
      jam_mulai:      prev.jam_selesai,
      jam_selesai:    next.jam_mulai,
      status:         'istirahat',
    }
  }

  return null
}
