<template>
  <div class="min-h-screen bg-[#F5F7FA] pb-nav">

    <!-- Header -->
    <div class="relative bg-gradient-to-br from-brand-700 to-brand-800 px-5 pt-12 pb-28 overflow-hidden">
      <div class="absolute -top-6 -right-6 w-32 h-32 rounded-full bg-white/5" />
      <div class="absolute bottom-2 left-4 w-16 h-16 rounded-full bg-white/5" />
      <div class="absolute top-8 right-20 w-10 h-10 rounded-full bg-white/5" />
      <div class="relative">
        <p class="text-white/60 text-xs font-medium tracking-wide uppercase">Kegiatan</p>
        <h1 class="text-white font-bold text-xl mt-0.5">{{ activeSantri?.nama ?? '—' }}</h1>
        <p class="text-white/50 text-xs mt-1">Aktivitas & layanan santri</p>
      </div>
    </div>

    <!-- Feature Grid -->
    <div class="relative z-10 px-4 -mt-14 mb-6">
      <div class="grid grid-cols-2 gap-3">
        <router-link v-for="f in features" :key="f.to" :to="f.to"
             class="bg-white rounded-2xl p-4 shadow-md border border-gray-100/80 active:scale-95 transition-transform">
          <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3"
               :class="f.iconBg">
            <component :is="f.icon" class="w-5 h-5" :class="f.iconColor" />
          </div>
          <p class="text-gray-800 font-bold text-sm mb-0.5">{{ f.label }}</p>
          <p class="text-gray-400 text-xs leading-relaxed">{{ f.desc }}</p>
        </router-link>
      </div>
    </div>

    <!-- Recent Izin -->
    <div class="px-4 mb-4">
      <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
          <div class="w-1 h-4 bg-brand-600 rounded-full" />
          <h2 class="text-gray-800 font-bold text-sm">Izin Terakhir</h2>
        </div>
        <router-link to="/kegiatan/izin" class="text-brand-700 text-xs font-semibold">Lihat Semua</router-link>
      </div>

      <div v-if="loadingIzin" class="space-y-2">
        <div v-for="i in 2" :key="i" class="bg-white rounded-2xl p-4 border border-gray-100">
          <div class="skeleton h-3.5 w-40 rounded mb-2" />
          <div class="skeleton h-3 w-24 rounded" />
        </div>
      </div>

      <div v-else-if="recentIzin.length" class="space-y-2">
        <div v-for="izin in recentIzin" :key="izin.id"
             class="bg-white rounded-2xl border border-gray-100/80 overflow-hidden flex">
          <div class="w-1 flex-shrink-0 rounded-l-2xl"
               :class="{
                 'bg-amber-400':   izin.status === 'menunggu',
                 'bg-emerald-400': izin.status === 'disetujui',
                 'bg-red-400':     izin.status === 'ditolak',
               }" />
          <div class="flex-1 p-4 flex items-center justify-between">
            <div>
              <p class="text-gray-800 font-semibold text-sm">{{ jenisLabel(izin.jenis_izin) }}</p>
              <p class="text-gray-400 text-xs mt-0.5">{{ formatDate(izin.tanggal_mulai) }}</p>
            </div>
            <span class="text-xs px-2.5 py-1 rounded-full font-semibold"
                  :class="{
                    'bg-amber-50 text-amber-700':     izin.status === 'menunggu',
                    'bg-emerald-50 text-emerald-700': izin.status === 'disetujui',
                    'bg-red-50 text-red-700':         izin.status === 'ditolak',
                  }">
              {{ statusLabel(izin.status) }}
            </span>
          </div>
        </div>
      </div>

      <div v-else class="bg-white rounded-2xl border border-gray-100 py-8 text-center text-gray-400 text-sm">
        <span class="text-3xl block mb-2">🚪</span>
        Belum ada pengajuan izin
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useSantriStore } from '@/stores/santri'
import { waliApi } from '@/api/wali'
import {
  ArrowRightStartOnRectangleIcon, HeartIcon,
  HomeModernIcon, ChatBubbleLeftRightIcon,
  ClipboardDocumentCheckIcon,
} from '@heroicons/vue/24/outline'

const santriStore  = useSantriStore()
const activeSantri = computed(() => santriStore.active)
const loadingIzin  = ref(true)
const recentIzin   = ref<any[]>([])

const features = [
  { to: '/kegiatan/izin',      label: 'Perizinan', desc: 'Ajukan izin keluar/pulang',   icon: ArrowRightStartOnRectangleIcon, iconBg: 'bg-sky-50',    iconColor: 'text-sky-600'   },
  { to: '/kegiatan/kesehatan', label: 'Kesehatan', desc: 'Riwayat kesehatan santri',    icon: HeartIcon,                     iconBg: 'bg-red-50',    iconColor: 'text-red-500'   },
  { to: '/kegiatan/kunjungan', label: 'Kunjungan', desc: 'Jadwal & permohonan kunjungan', icon: HomeModernIcon,              iconBg: 'bg-emerald-50',iconColor: 'text-emerald-600'},
  { to: '/kegiatan/konseling', label: 'Konseling', desc: 'Sesi konseling & bimbingan',  icon: ChatBubbleLeftRightIcon,       iconBg: 'bg-violet-50', iconColor: 'text-violet-600'},
  { to: '/kegiatan/presensi',  label: 'Presensi',  desc: 'Kehadiran kegiatan harian',   icon: ClipboardDocumentCheckIcon,   iconBg: 'bg-amber-50',  iconColor: 'text-amber-600' },
]

function jenisLabel(j: string) {
  const map: Record<string, string> = { pulang: 'Izin Pulang', keluar_area: 'Keluar Area', kegiatan_luar: 'Kegiatan Luar', sakit: 'Sakit', lainnya: 'Lainnya' }
  return map[j] ?? j
}
function statusLabel(s: string) {
  return { menunggu: 'Menunggu', disetujui: 'Disetujui', ditolak: 'Ditolak', selesai: 'Selesai' }[s] ?? s
}
function formatDate(d: string) {
  return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

onMounted(async () => {
  if (!activeSantri.value) return
  try {
    const data = await waliApi.daftarIzin(activeSantri.value.id)
    recentIzin.value = data.slice(0, 2)
  } finally { loadingIzin.value = false }
})
</script>
