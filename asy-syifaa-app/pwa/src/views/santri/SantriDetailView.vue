<template>
  <div class="min-h-screen bg-[#F5F7FA] pb-nav">

    <!-- Header -->
    <div class="relative bg-gradient-to-br from-brand-700 to-brand-800 px-5 pt-12 pb-28 overflow-hidden">
      <div class="absolute -top-6 -right-6 w-32 h-32 rounded-full bg-white/5" />
      <div class="absolute bottom-2 left-4 w-16 h-16 rounded-full bg-white/5" />
      <div class="relative flex items-center gap-3">
        <button @click="$router.back()" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center active:bg-white/20">
          <ChevronLeftIcon class="w-5 h-5 text-white" />
        </button>
        <div>
          <p class="text-white/60 text-xs font-medium">Data Santri</p>
          <h1 class="text-white font-bold text-lg">Informasi Lengkap</h1>
        </div>
      </div>
    </div>

    <!-- Avatar Card -->
    <div class="relative z-10 px-4 -mt-16 mb-4">
      <div class="bg-white rounded-2xl shadow-lg border border-white p-5">
        <div class="flex items-center gap-4">
          <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-brand-600 to-brand-800
                      flex items-center justify-center flex-shrink-0">
            <span class="text-white font-black text-2xl">{{ initials }}</span>
          </div>
          <div class="flex-1 min-w-0">
            <p class="font-black text-gray-800 text-base leading-tight">{{ santri?.nama ?? '—' }}</p>
            <p class="text-sm text-gray-500 mt-0.5">{{ santri?.kelas }} · {{ santri?.jenjang }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Rombel: {{ santri?.rombel ?? '—' }}</p>
          </div>
          <span class="px-2.5 py-1 rounded-full text-xs font-bold flex-shrink-0"
                :class="santri?.status === 'aktif'
                  ? 'bg-emerald-50 text-emerald-700 border border-emerald-100'
                  : 'bg-gray-100 text-gray-500'">
            {{ santri?.status === 'aktif' ? '✓ Aktif' : santri?.status }}
          </span>
        </div>

        <!-- NIS Badge -->
        <div class="mt-4 pt-4 border-t border-gray-50 flex gap-3">
          <div class="flex-1 bg-brand-50 rounded-xl px-3 py-2.5 text-center">
            <p class="text-[10px] text-brand-500 font-medium leading-none mb-1">NIS</p>
            <p class="text-brand-800 font-black text-sm">{{ santri?.nis ?? '—' }}</p>
          </div>
          <div class="flex-1 bg-gray-50 rounded-xl px-3 py-2.5 text-center">
            <p class="text-[10px] text-gray-400 font-medium leading-none mb-1">Tahun Masuk</p>
            <p class="text-gray-700 font-black text-sm">{{ santri?.tahun_masuk ?? '—' }}</p>
          </div>
          <div class="flex-1 bg-gray-50 rounded-xl px-3 py-2.5 text-center">
            <p class="text-[10px] text-gray-400 font-medium leading-none mb-1">Jalur</p>
            <p class="text-gray-700 font-black text-sm">{{ santri?.jalur_masuk ?? '—' }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Data Pribadi -->
    <div class="px-4 mb-4">
      <SectionLabel>Data Pribadi</SectionLabel>
      <div class="bg-white rounded-2xl border border-gray-100/80 shadow-sm overflow-hidden divide-y divide-gray-50">
        <DetailRow label="Tempat Lahir" :value="detail?.tempat_lahir ?? '—'" />
        <DetailRow label="Tanggal Lahir" :value="formatDate(detail?.tanggal_lahir)" />
        <DetailRow label="Jenis Kelamin" :value="santri?.gender === 'L' ? 'Laki-laki' : 'Perempuan'" />
        <DetailRow label="Golongan Darah" :value="detail?.golongan_darah ?? '—'" />
        <DetailRow label="Tinggi Badan" :value="detail?.tinggi_badan ? `${detail.tinggi_badan} cm` : '—'" />
        <DetailRow label="Berat Badan" :value="detail?.berat_badan ? `${detail.berat_badan} kg` : '—'" />
      </div>
    </div>

    <!-- Alamat -->
    <div class="px-4 mb-4">
      <SectionLabel>Alamat</SectionLabel>
      <div class="bg-white rounded-2xl border border-gray-100/80 shadow-sm overflow-hidden divide-y divide-gray-50">
        <DetailRow label="Alamat Jalan" :value="detail?.alamat ?? '—'" multiline />
        <DetailRow label="Desa/Kelurahan" :value="detail?.desa_kelurahan ?? '—'" />
        <DetailRow label="Kecamatan" :value="detail?.kecamatan ?? '—'" />
        <DetailRow label="Kab/Kota" :value="detail?.kab_kota ?? '—'" />
        <DetailRow label="Provinsi" :value="detail?.provinsi ?? '—'" />
        <DetailRow label="Kode Pos" :value="detail?.kode_pos ?? '—'" />
      </div>
    </div>

    <!-- Data Akademik -->
    <div class="px-4 mb-4">
      <SectionLabel>Data Akademik</SectionLabel>
      <div class="bg-white rounded-2xl border border-gray-100/80 shadow-sm overflow-hidden divide-y divide-gray-50">
        <DetailRow label="Jenjang" :value="santri?.jenjang ?? '—'" />
        <DetailRow label="Kelas / Rombel" :value="`${santri?.kelas ?? '—'} — ${santri?.rombel ?? '—'}`" />
        <DetailRow label="Status Santri" :value="santri?.status === 'aktif' ? 'Aktif' : (santri?.status ?? '—')" />
      </div>
    </div>

    <!-- Data Orang Tua / Wali -->
    <div class="px-4 mb-4">
      <SectionLabel>Data Orang Tua / Wali</SectionLabel>
      <div class="bg-white rounded-2xl border border-gray-100/80 shadow-sm overflow-hidden divide-y divide-gray-50">
        <DetailRow label="Nama Ayah" :value="detail?.nama_ayah ?? '—'" />
        <DetailRow label="No. HP Ayah" :value="detail?.no_hp_ayah ?? '—'" />
        <DetailRow label="Nama Ibu" :value="detail?.nama_ibu ?? '—'" />
        <DetailRow label="No. HP Ibu" :value="detail?.no_hp_ibu ?? '—'" />
      </div>
    </div>

    <!-- Kesehatan -->
    <div class="px-4 mb-4">
      <SectionLabel>Informasi Kesehatan</SectionLabel>
      <div class="bg-white rounded-2xl border border-gray-100/80 shadow-sm overflow-hidden divide-y divide-gray-50">
        <DetailRow label="Riwayat Penyakit" :value="detail?.riwayat_penyakit ?? '—'" />
        <DetailRow label="Alergi" :value="detail?.alergi ?? '—'" />
      </div>
    </div>

    <!-- Keuangan Ringkas -->
    <div class="px-4 mb-8">
      <SectionLabel>Keuangan</SectionLabel>
      <div class="bg-white rounded-2xl border border-gray-100/80 shadow-sm overflow-hidden divide-y divide-gray-50">
        <DetailRow label="SPP Bulanan" :value="santri?.spp_amount ? `Rp ${(santri.spp_amount).toLocaleString('id-ID')}` : '—'" />
        <div class="flex items-center justify-between px-4 py-3.5">
          <p class="text-sm text-gray-500">Tunggakan</p>
          <span class="font-semibold text-sm"
                :class="(santri?.tunggakan_bulan ?? 0) > 0 ? 'text-red-600' : 'text-emerald-600'">
            {{ (santri?.tunggakan_bulan ?? 0) > 0 ? `${santri!.tunggakan_bulan} bulan` : 'Lunas ✓' }}
          </span>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { computed, defineComponent, h } from 'vue'
import { useSantriStore } from '@/stores/santri'
import { DEMO_SANTRI } from '@/api/mock'
import { ChevronLeftIcon } from '@heroicons/vue/24/outline'

const santriStore = useSantriStore()
const santri      = computed(() => santriStore.active)

// Extend data from mock (detail fields tidak ada di Santri interface)
const detail = computed(() => {
  const s = santri.value
  if (!s) return null
  // Cari detail dari mock (atau nanti dari API)
  return DEMO_SANTRI.find(d => d.id === s.id) ?? null
})

const initials = computed(() =>
  (santri.value?.nama ?? 'S').split(' ').slice(0, 2).map((w: string) => w[0]).join('').toUpperCase()
)

function formatDate(d?: string) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

// ── Inline components ──────────────────────────────────────────
const SectionLabel = defineComponent({
  setup(_, { slots }) {
    return () => h('p', { class: 'text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 px-1' }, slots.default?.())
  },
})

const DetailRow = defineComponent({
  props: { label: String, value: String, multiline: Boolean },
  setup(props) {
    return () => h('div', { class: 'flex items-start gap-3 px-4 py-3.5' }, [
      h('p', { class: 'text-sm text-gray-400 flex-shrink-0 w-32' }, props.label),
      h('p', { class: `text-sm font-semibold text-gray-800 flex-1 text-right${props.multiline ? ' break-words' : ' truncate'}` }, props.value ?? '—'),
    ])
  },
})
</script>
