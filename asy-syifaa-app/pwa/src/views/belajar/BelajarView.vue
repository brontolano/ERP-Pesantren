<template>
  <div class="min-h-screen bg-[#F5F7FA] pb-nav">

    <!-- Header -->
    <div class="relative bg-gradient-to-br from-brand-700 to-brand-800 px-5 pt-12 pb-28 overflow-hidden">
      <div class="absolute -top-6 -right-6 w-32 h-32 rounded-full bg-white/5" />
      <div class="absolute bottom-2 left-4 w-16 h-16 rounded-full bg-white/5" />
      <div class="absolute top-8 right-20 w-10 h-10 rounded-full bg-white/5" />
      <div class="relative">
        <p class="text-white/60 text-xs font-medium tracking-wide uppercase">Belajar</p>
        <h1 class="text-white font-bold text-xl mt-0.5">{{ activeSantri?.nama ?? '—' }}</h1>
        <p class="text-white/50 text-xs mt-1">Pantau perkembangan akademik santri</p>
      </div>
    </div>

    <!-- Quick Stats -->
    <div class="relative z-10 px-4 -mt-14 mb-4 grid grid-cols-3 gap-2">
      <div v-for="s in stats" :key="s.label"
           class="bg-white rounded-2xl p-3 shadow-sm border border-gray-100/80 text-center">
        <p class="text-lg font-black" :class="s.color">{{ s.value }}</p>
        <p class="text-gray-400 text-[10px] font-medium mt-0.5">{{ s.label }}</p>
      </div>
    </div>

    <!-- Feature Grid -->
    <div class="px-4 mb-6">
      <div class="grid grid-cols-2 gap-3">
        <router-link v-for="f in features" :key="f.to" :to="f.to"
             class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100/80 active:scale-95 transition-transform">
          <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3"
               :class="f.iconBg">
            <component :is="f.icon" class="w-5 h-5" :class="f.iconColor" />
          </div>
          <p class="text-gray-800 font-bold text-sm mb-0.5">{{ f.label }}</p>
          <p class="text-gray-400 text-xs leading-relaxed">{{ f.desc }}</p>
        </router-link>
      </div>
    </div>

    <!-- Jadwal Hari Ini -->
    <div class="px-4 mb-4">
      <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
          <div class="w-1 h-4 bg-brand-600 rounded-full" />
          <h2 class="text-gray-800 font-bold text-sm">Jadwal Hari Ini</h2>
        </div>
        <router-link to="/belajar/jadwal" class="text-brand-700 text-xs font-semibold">Lihat Semua</router-link>
      </div>

      <div v-if="jadwalHariIni.length" class="space-y-2">
        <div v-for="j in jadwalHariIni" :key="j.id"
             class="bg-white rounded-2xl p-4 border border-gray-100/80 flex items-center gap-3">
          <div class="text-center w-12 flex-shrink-0">
            <p class="text-brand-700 font-black text-sm">{{ j.jam_mulai }}</p>
            <p class="text-gray-300 text-[10px]">{{ j.jam_selesai }}</p>
          </div>
          <div class="w-px h-8 bg-gray-100 flex-shrink-0" />
          <div class="flex-1 min-w-0">
            <p class="text-gray-800 font-semibold text-sm truncate">{{ j.mata_pelajaran }}</p>
            <p class="text-gray-400 text-xs">{{ j.pengajar }}</p>
          </div>
          <span class="text-xs px-2 py-1 rounded-full bg-brand-50 text-brand-700 font-semibold flex-shrink-0">
            {{ j.ruangan }}
          </span>
        </div>
      </div>

      <div v-else class="bg-white rounded-2xl border border-gray-100 py-8 text-center text-gray-400 text-sm">
        <span class="text-3xl block mb-2">📅</span>
        Tidak ada jadwal hari ini
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useSantriStore } from '@/stores/santri'
import { waliApi } from '@/api/wali'
import {
  BookOpenIcon, CalendarDaysIcon, ClipboardDocumentCheckIcon,
  TrophyIcon, AcademicCapIcon,
} from '@heroicons/vue/24/outline'

const santriStore  = useSantriStore()
const activeSantri = computed(() => santriStore.active)
const jadwalHariIni = ref<any[]>([])

const stats = ref([
  { label: 'Kehadiran', value: '—%',  color: 'text-emerald-600' },
  { label: 'Hafalan',   value: '— Juz', color: 'text-brand-700'   },
  { label: 'Nilai',     value: '—',   color: 'text-blue-600'    },
])

const features = [
  { to: '/belajar/hafalan',  label: 'Hafalan',  desc: 'Progress hafalan Al-Qur\'an',  icon: BookOpenIcon,               iconBg: 'bg-brand-50',  iconColor: 'text-brand-700' },
  { to: '/belajar/jadwal',   label: 'Jadwal',   desc: 'Jadwal pelajaran harian',      icon: CalendarDaysIcon,           iconBg: 'bg-blue-50',   iconColor: 'text-blue-600'  },
  { to: '/belajar/absensi',  label: 'Absensi',  desc: 'Kehadiran & ketidakhadiran',   icon: ClipboardDocumentCheckIcon, iconBg: 'bg-emerald-50',iconColor: 'text-emerald-600'},
  { to: '/belajar/prestasi', label: 'Prestasi', desc: 'Ekskul, khidmah & disiplin',   icon: TrophyIcon,                 iconBg: 'bg-amber-50',  iconColor: 'text-amber-600' },
  { to: '/belajar/akademik', label: 'Akademik', desc: 'Nilai & laporan akademik',     icon: AcademicCapIcon,            iconBg: 'bg-violet-50', iconColor: 'text-violet-600'},
]

onMounted(async () => {
  if (!activeSantri.value) return
  try {
    const data = await waliApi.jadwal(activeSantri.value.id) as any[]
    const today = new Date().getDay() // 0=Sun
    jadwalHariIni.value = data.filter((j: any) => j.hari_ke === today).slice(0, 3)
  } catch { /* ignore */ }
})
</script>
