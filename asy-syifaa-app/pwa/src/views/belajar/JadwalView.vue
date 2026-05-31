<template>
  <div class="min-h-screen bg-[#F5F7FA] pb-nav">

    <!-- Header -->
    <div class="relative bg-gradient-to-br from-brand-700 to-brand-800 px-5 pt-12 pb-20 overflow-hidden">
      <div class="absolute -top-6 -right-6 w-32 h-32 rounded-full bg-white/5" />
      <div class="absolute bottom-2 left-4 w-16 h-16 rounded-full bg-white/5" />
      <div class="relative flex items-center gap-3">
        <button @click="$router.back()" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center">
          <ChevronLeftIcon class="w-5 h-5 text-white" />
        </button>
        <div>
          <p class="text-white/60 text-xs font-medium">Jadwal Pelajaran</p>
          <h1 class="text-white font-bold text-lg">{{ activeSantri?.nama ?? '—' }}</h1>
        </div>
      </div>
    </div>

    <!-- Day Tabs -->
    <div class="relative z-10 px-4 -mt-6 mb-4 flex gap-2 overflow-x-auto scrollbar-hide pb-1">
      <button v-for="(d, i) in days" :key="i" @click="activeDay = i"
              class="flex-shrink-0 flex flex-col items-center px-4 py-2.5 rounded-2xl text-sm transition-colors"
              :class="activeDay === i
                ? 'bg-brand-700 text-white shadow-md shadow-brand-700/20'
                : 'bg-white text-gray-600 border border-gray-200'">
        <span class="text-[10px] font-medium" :class="activeDay === i ? 'text-white/70' : 'text-gray-400'">
          {{ d.short }}
        </span>
        <span class="font-bold text-sm">{{ d.label }}</span>
      </button>
    </div>

    <!-- Schedule List -->
    <div class="px-4 flex flex-col gap-2">
      <template v-if="loading">
        <div v-for="i in 5" :key="i" class="bg-white rounded-2xl p-4 border border-gray-100">
          <div class="flex gap-3">
            <div class="skeleton w-12 h-10 rounded-lg" />
            <div class="flex-1">
              <div class="skeleton h-4 w-32 rounded mb-2" />
              <div class="skeleton h-3 w-24 rounded" />
            </div>
          </div>
        </div>
      </template>

      <template v-else-if="todaySchedule.length">
        <div v-for="(item, idx) in todaySchedule" :key="item.id"
             class="bg-white rounded-2xl border border-gray-100/80 overflow-hidden flex">
          <!-- Time column -->
          <div class="w-16 flex-shrink-0 flex flex-col items-center justify-center py-4 border-r border-gray-50"
               :class="isCurrentClass(item) ? 'bg-brand-700' : 'bg-gray-50'">
            <p class="font-black text-sm" :class="isCurrentClass(item) ? 'text-white' : 'text-brand-700'">
              {{ item.jam_mulai }}
            </p>
            <p class="text-[10px]" :class="isCurrentClass(item) ? 'text-white/60' : 'text-gray-400'">
              {{ item.jam_selesai }}
            </p>
          </div>
          <!-- Content -->
          <div class="flex-1 p-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                 :class="subjectColors[idx % subjectColors.length].bg">
              <span class="text-base">{{ item.emoji ?? '📚' }}</span>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-gray-800 font-bold text-sm truncate">{{ item.mata_pelajaran }}</p>
              <p class="text-gray-400 text-xs truncate">{{ item.pengajar }}</p>
            </div>
            <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-500 font-medium flex-shrink-0">
              {{ item.ruangan }}
            </span>
          </div>
          <!-- Current indicator -->
          <div v-if="isCurrentClass(item)"
               class="w-1 bg-brand-700 rounded-r-full" />
        </div>
      </template>

      <div v-else-if="!loading"
           class="bg-white rounded-2xl border border-gray-100 py-14 text-center text-gray-400 text-sm">
        <span class="text-4xl block mb-2">🎉</span>
        Tidak ada jadwal hari ini
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useSantriStore } from '@/stores/santri'
import { waliApi } from '@/api/wali'
import { ChevronLeftIcon } from '@heroicons/vue/24/outline'

const santriStore  = useSantriStore()
const activeSantri = computed(() => santriStore.active)
const loading      = ref(true)
const jadwalData   = ref<any[]>([])
const activeDay    = ref(new Date().getDay()) // 0=Sun

const days = [
  { label: 'Min', short: 'Minggu', idx: 0 },
  { label: 'Sen', short: 'Senin', idx: 1 },
  { label: 'Sel', short: 'Selasa', idx: 2 },
  { label: 'Rab', short: 'Rabu', idx: 3 },
  { label: 'Kam', short: 'Kamis', idx: 4 },
  { label: 'Jum', short: "Jum'at", idx: 5 },
  { label: 'Sab', short: 'Sabtu', idx: 6 },
]

const subjectColors = [
  { bg: 'bg-blue-50' }, { bg: 'bg-emerald-50' }, { bg: 'bg-amber-50' },
  { bg: 'bg-violet-50' }, { bg: 'bg-rose-50' }, { bg: 'bg-cyan-50' },
]

const todaySchedule = computed(() =>
  jadwalData.value.filter(j => j.hari_ke === activeDay.value)
    .sort((a, b) => a.jam_mulai.localeCompare(b.jam_mulai))
)

function isCurrentClass(item: any) {
  const now = new Date().toTimeString().slice(0, 5)
  return now >= item.jam_mulai && now <= item.jam_selesai && activeDay.value === new Date().getDay()
}

onMounted(async () => {
  if (!activeSantri.value) return
  try { jadwalData.value = await waliApi.jadwal(activeSantri.value.id) as any[] }
  finally { loading.value = false }
})
</script>
