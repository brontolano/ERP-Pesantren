import { api } from './client'
import { mockApi } from './mock'

const DEMO = import.meta.env.VITE_DEMO_MODE === 'true'

// ── Types ──────────────────────────────────────────────────────

export interface Santri {
  id: number; nis: string; nama: string; kelas: string; jenjang: string
  rombel: string; status: string; tahun_masuk: string; gender: string
  tanggal_lahir: string; jalur_masuk: string
  tunggakan_bulan: number; spp_amount: number
}

export interface StatusHarian {
  id: number; tanggal: string; sesi: string
  status_kehadiran: string; status_label: string
  status_kesehatan: string; kesehatan_label: string
  keterangan: string | null
}

export interface RekapBulan {
  hadir: number; sakit: number; izin: number; alfa: number
}

export interface TahfidzProgress {
  total_juz_quran: number; target_juz_quran: number; persen_quran: number
  total_hadist: number; target_hadist: number
  update_terakhir: string | null; catatan: string | null
}

export interface TahfidzRecord {
  id: number; tanggal: string; kategori: string; pencapaian: string
  jenis: string; jenis_label: string
  nilai: string | null; nilai_label: string
  catatan: string | null; ustadz: string | null
}

export interface TagihanItem { id: number; nama_item: string; nominal: number }
export interface Invoice {
  id: number; nomor?: string; jenis?: string; keterangan: string; status: string
  is_prioritas?: boolean
  due_date: string | null; total: number; terbayar: number; sisa?: number
  items: TagihanItem[]
}
export interface Tagihan {
  total_tunggakan: number
  tunggakan_prioritas?: number
  invoices: Invoice[]
}

export interface PaymentMethod {
  id: number
  code: string
  type: 'bank' | 'ewallet' | 'qris'
  name: string
  bank_name: string | null
  account_number: string | null
  account_holder: string | null
  icon: string | null
  qris_image_url: string | null
  instructions: string | null
}

export interface Izin {
  id: number
  jenis_izin: string
  tanggal_mulai: string
  tanggal_selesai: string | null
  alasan: string
  status: string
}

// ── API calls ──────────────────────────────────────────────────

export const waliApi = {
  getSantri: () => DEMO
    ? mockApi.getSantri() as Promise<Santri[]>
    : api.get<{ ok: boolean; data: Santri[] }>('/wali/santri').then(r => r.data.data),

  getStatusHarian: (studentId: number) => DEMO
    ? mockApi.getStatusHarian(studentId)
    : api.get<{ ok: boolean; hari_ini: StatusHarian[]; riwayat: StatusHarian[]; rekap_bulan: RekapBulan }>(
        `/wali/santri/${studentId}/status-harian`
      ).then(r => r.data),

  getHafalan: (studentId: number) => DEMO
    ? mockApi.getHafalan(studentId)
    : api.get<{ ok: boolean; progress: TahfidzProgress | null; riwayat_setor: TahfidzRecord[] }>(
        `/wali/santri/${studentId}/hafalan`
      ).then(r => r.data),

  getTagihan: (studentId: number) => DEMO
    ? mockApi.getTagihan(studentId) as Promise<Tagihan>
    : api.get<Tagihan>(`/wali/santri/${studentId}/tagihan`).then(r => r.data),

  tagihan: (studentId: number) => DEMO
    ? mockApi.tagihan(studentId) as Promise<Tagihan>
    : api.get<Tagihan>(`/wali/santri/${studentId}/tagihan`).then(r => r.data),

  uploadBuktiBayar: (studentId: number, invoiceId: number, formData: FormData) => DEMO
    ? mockApi.uploadBuktiBayar()
    : api.post(`/wali/santri/${studentId}/tagihan/${invoiceId}/bukti-bayar`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      }).then(r => r.data),

  paymentMethods: (studentId: number) => DEMO
    ? Promise.resolve([] as PaymentMethod[])
    : api.get<{ ok: boolean; data: PaymentMethod[] }>(`/wali/santri/${studentId}/payment-methods`).then(r => r.data.data),

  getIzin: (studentId: number) => DEMO
    ? mockApi.getIzin(studentId) as Promise<Izin[]>
    : api.get<{ ok: boolean; data: Izin[] }>(`/wali/santri/${studentId}/izin`).then(r => r.data.data),

  daftarIzin: (studentId: number) => DEMO
    ? mockApi.daftarIzin(studentId) as Promise<Izin[]>
    : api.get<{ ok: boolean; data: Izin[] }>(`/wali/santri/${studentId}/izin`).then(r => r.data.data),

  ajukanIzin: (studentId: number, payload: Record<string, string>) => DEMO
    ? mockApi.ajukanIzin(studentId, payload)
    : api.post<{ ok: boolean; data: Izin }>(`/wali/santri/${studentId}/izin`, payload).then(r => r.data),

  // ── Tabungan ───────────────────────────────────────────────────
  tabungan: (studentId: number) => DEMO
    ? mockApi.tabungan(studentId)
    : api.get(`/wali/santri/${studentId}/tabungan`).then(r => (r.data as any).data ?? r.data),

  transaksi: (studentId: number, params?: Record<string, any>) => DEMO
    ? mockApi.transaksi(studentId)
    : api.get(`/wali/santri/${studentId}/transaksi`, { params }).then(r => (r.data as any).data ?? r.data),

  setLimitJajan: (studentId: number, limit: number) => DEMO
    ? mockApi.setLimitJajan(studentId, limit)
    : api.post(`/wali/santri/${studentId}/tabungan/limit`, { limit_harian: limit }).then(r => r.data),

  freezeTabungan: (studentId: number, frozen: boolean) => DEMO
    ? mockApi.freezeTabungan(studentId, frozen)
    : api.post(`/wali/santri/${studentId}/tabungan/freeze`, { frozen }).then(r => r.data),

  topupTabungan: (studentId: number, formData: FormData) => DEMO
    ? Promise.resolve({ ok: true, message: 'Bukti setoran terkirim (demo).' })
    : api.post(`/wali/santri/${studentId}/tabungan/topup`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      }).then(r => r.data),

  bayarTagihanDariSaldo: (studentId: number, invoiceId: number, nominal?: number) => DEMO
    ? Promise.resolve({ ok: true, message: 'Tagihan dilunasi dari saldo (demo).' })
    : api.post(`/wali/santri/${studentId}/tabungan/bayar-tagihan`, {
        invoice_id: invoiceId,
        ...(nominal ? { nominal } : {}),
      }).then(r => r.data),

  // ── Jadwal & Absensi ───────────────────────────────────────────
  jadwal: (studentId: number) => DEMO
    ? mockApi.jadwal(studentId)
    : api.get(`/wali/santri/${studentId}/jadwal`).then(r => (r.data as any).data ?? r.data),

  absensi: (studentId: number, params?: { year?: number; month?: number }) => DEMO
    ? mockApi.absensi(studentId)
    : api.get(`/wali/santri/${studentId}/absensi`, { params }).then(r => (r.data as any).data ?? r.data),

  // ── Prestasi & Akademik ────────────────────────────────────────
  prestasi: (studentId: number) => DEMO
    ? mockApi.prestasi(studentId)
    : api.get(`/wali/santri/${studentId}/prestasi`).then(r => (r.data as any).data ?? r.data),

  akademik: (studentId: number, semester?: string) => DEMO
    ? mockApi.akademik(studentId)
    : api.get(`/wali/santri/${studentId}/akademik`, { params: { semester } }).then(r => (r.data as any).data ?? r.data),

  // ── Kesehatan ──────────────────────────────────────────────────
  kesehatan: (studentId: number) => DEMO
    ? mockApi.kesehatan(studentId)
    : api.get(`/wali/santri/${studentId}/kesehatan`).then(r => (r.data as any).data ?? r.data),

  // ── Kunjungan ──────────────────────────────────────────────────
  daftarKunjungan: (studentId: number) => DEMO
    ? mockApi.daftarKunjungan(studentId)
    : api.get(`/wali/santri/${studentId}/kunjungan`).then(r => (r.data as any).data ?? r.data),

  ajukanKunjungan: (studentId: number, payload: Record<string, string>) => DEMO
    ? mockApi.ajukanKunjungan(studentId, payload)
    : api.post(`/wali/santri/${studentId}/kunjungan`, payload).then(r => r.data),

  // ── Konseling ──────────────────────────────────────────────────
  daftarKonseling: (studentId: number) => DEMO
    ? mockApi.daftarKonseling(studentId)
    : api.get(`/wali/santri/${studentId}/konseling`).then(r => (r.data as any).data ?? r.data),

  ajukanKonseling: (studentId: number, payload: Record<string, string>) => DEMO
    ? mockApi.ajukanKonseling(studentId, payload)
    : api.post(`/wali/santri/${studentId}/konseling`, payload).then(r => r.data),

  // ── Presensi ───────────────────────────────────────────────────
  presensi: (studentId: number) => DEMO
    ? mockApi.presensi(studentId)
    : api.get(`/wali/santri/${studentId}/presensi`).then(r => (r.data as any).data ?? r.data),
}
