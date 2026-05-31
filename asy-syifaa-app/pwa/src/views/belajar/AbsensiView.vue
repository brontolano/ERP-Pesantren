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
          <p class="text-white/60 text-xs font-medium">Absensi</p>
          <h1 class="text-white font-bold text-lg">{{ activeSantri?.nama ?? '—' }}</h1>
        </div>
      </div>
    </div>

    <!-- Stats -->
    <div class="relative z-10 px-4 -mt-10 mb-4 grid grid-cols-4 gap-2">
      <div v-for="s in summaryStats" :key="s.label"
           class="bg-white rounded-2xl p-3 shadow-sm border border-gray-100/80 text-center">
        <p class="text-base font-black" :class="s.color">{{ s.value }}</p>
        <p class="text-gray-400 text-[10px] font-medium leading-tight mt-0.5">{{ s.label }}</p>
      </div>
    </div>

    <!-- Month Selector -->
    <div class="px-4 mb-3 flex items-center justify-between">
      <button @click="prevMonth" class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center">
        <ChevronLeftIcon class="w-4 h-4 text-gray-500" />
      </button>
      <p class="text-gray-800 font-bold text-sm">{{ monthLabel }}</p>
      <button @click="nextMonth" class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center">
        <ChevronRightIcon class="w-4 h-4 text-gray-500" />
      </button>
    </div>

    <!-- Calendar Grid -->
    <div class="px-4 mb-4">
      <div class="bg-white rounded-2xl border border-gray-100/80 shadow-sm p-4">
        <!-- Day headers -->
        <div class="grid grid-cols-7 mb-2">
          <div v-for="d in ['Min','Sen','Sel','Rab','Kam','Jum','Sab']" :key="d"
               class="text-center text-[10px] font-semibold text-gray-400">{{ d }}</div>
        </div>
        <!-- Day cells -->
        <div class="grid grid-cols-7 gap-1">
          <div v-for="cell in calendarCells" :key="cell.key"
               class="aspect-square flex items-center justify-center rounded-lg text-xs font-semibold"
               :class="cellClass(cell)">
            {{ cell.day ?? '' }}
          </div>
        </div>

        <!-- Legend -->
        <div class="flex flex-wrap gap-3 mt-4 pt-3 border-t border-gray-50">
          <div v-for="l in legend" :key="l.label" class="flex items-center gap-1.5">
            <div class="w-3 h-3 rounded" :class="l.color" />
            <span class="text-[10px] text-gray-500">{{ l.label }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Detail List -->
    <div class="px-4 mb-4">
      <div class="flex items-center gap-2 mb-3">
        <div class="w-1 h-4 bg-brand-600 rounded-full" />
        <h2 class="text-gray-800 font-bold text-sm">Riwayat Absensi</h2>
      </div>
      <div v-if="loading" class="space-y-2">
        <div v-for="i in 4" :key="i" class="bg-white rounded-2xl p-4 border border-gray-100">
          <div class="skeleton h-3.5 w-40 rounded mb-2" />
          <div class="skeleton h-3 w-24 rounded" />
        </div>
      </div>
      <div v-else-if="absensiList.length" class="space-y-2">
        <div v-for="a in absensiList" :key="a.id"
             class="bg-white rounded-2xl p-4 border border-gray-100/80 flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
               :class="statusBg(a.status)">
            <span class="text-base">{{ statusEmoji(a.status) }}</span>
          </div>
          <div class="flex-1">
            <p class="text-gray-800 font-semibold text-sm">{{ a.mata_pelajaran ?? a.sesi }}</p>
            <p class="text-gray-400 text-xs">{{ formatDate(a.tanggal) }}</p>
          </div>
          <span class="text-xs px-2.5 py-1 rounded-full font-semibold"
                :class="statusChip(a.status)">
            {{ statusLabel(a.status) }}
          </span>
        </div>
      </div>
      <div v-else class="bg-white rounded-2xl border border-gray-100 py-10 text-center text-gray-400 text-sm">
        <span class="text-3xl block mb-2">📋</span>
        Belum ada data absensi
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useSantriStore } from '@/stores/santri'
import { waliApi } from '@/api/wali'
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/outline'

const santriStore  = useSantriStore()
const activeSantri = computed(() => santriStore.active)
const loading      = ref(true)
const absensiList  = ref<any[]>([])
const currentDate  = ref(new Date())

const summaryStats = ref([
  { label: 'Hadir',    value: '—', color: 'text-emerald-600' },
  { label: 'Sakit',    value: '—', color: 'text-amber-500'   },
  { label: 'Izin',     value: '—', color: 'text-blue-500'    },
  { label: 'Alpha',    value: '—', color: 'text-red-500'     },
])

const legend = [
  { label: 'Hadir',    color: 'bg-emerald-500' },
  { label: 'Sakit',    color: 'bg-amber-400'   },
  { label: 'Izin',     color: 'bg-blue-400'    },
  { label: 'Alpha',    color: 'bg-red-400'     },
  { label: 'Libur',    color: 'bg-gray-100'    },
]

const monthLabel = computed(() =>
  currentDate.value.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' })
)

const calendarCells = computed(() => {
  const year  = currentDate.value.getFullYear()
  const month = currentDate.value.getMonth()
  const first = new Date(year, month, 1).getDay()
  const days  = new Date(year, month + 1, 0).getDate()
  const cells: any[] = []
  for (let i = 0; i < first; i++) cells.push({ key: `e${i}`, day: null, status: null })
  for (let d = 1; d <= days; d++) {
    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`
    const found   = absensiList.value.find(a => a.tanggal?.startsWith(dateStr))
    cells.push({ key: dateStr, day: d, status: found?.status ?? null })
  }
  return cells
})

function cellClass(cell: any) {
  if (!cell.day) return ''
  const isToday = new Date().toISOString().slice(0, 10) === cell.key
  if (!cell.status) return isToday ? 'bg-brand-50 text-brand-700 ring-1 ring-brand-300' : 'text-gray-400'
  const map: Record<string, string> = {
    hadir: 'bg-emerald-500 text-white',
    sakit: 'bg-amber-400 text-white',
    izin:  'bg-blue-400 text-white',
    alpha: 'bg-red-400 text-white',
  }
  return map[cell.status] ?? 'text-gray-400'
}

function statusBg(s: string) {
  return { hadir: 'bg-emerald-50', sakit: 'bg-amber-50', izin: 'bg-blue-50', alpha: 'bg-red-50' }[s] ?? 'bg-gray-50'
}
function statusEmoji(s: string) {
  return { hadir: '✅', sakit: '🤒', izin: '📝', alpha: '❌' }[s] ?? '—'
}
function statusLabel(s: string) {
  return { hadir: 'Hadir', sakit: 'Sakit', izin: 'Izin', alpha: 'Alpha' }[s] ?? s
}
function statusChip(s: string) {
  return {
    hadir: 'bg-emerald-50 text-emerald-700',
    sakit: 'bg-amber-50 text-amber-700',
    izin:  'bg-blue-50 text-blue-700',
    alpha: 'bg-red-50 text-red-700',
  }[s] ?? 'bg-gray-100 text-gray-500'
}
function formatDate(d: string) {
  return new Date(d).toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short' })
}

function prevMonth() {
  const d = new Date(currentDate.value)
  d.setMonth(d.getMonth() - 1)
  currentDate.value = d
  loadAbsensi()
}
function nextMonth() {
  const d = new Date(currentDate.value)
  d.setMonth(d.getMonth() + 1)
  currentDate.value = d
  loadAbsensi()
}

async function loadAbsensi() {
  if (!activeSantri.value) return
  loading.value = true
  try {
    const year  = currentDate.value.getFullYear()
    const month = currentDate.value.getMonth() + 1
    const data  = await waliApi.absensi(activeSantri.value.id, { year, month }) as any[]
    absensiList.value = data
    // update summary
    ;[0,1,2,3].forEach(i => { if (summaryStats.value[i]) summaryStats.value[i]!.value = '0' })
    summaryStats.value[0]!.value = String(data.filter(a => a.status === 'hadir').length)
    summaryStats.value[1]!.value = String(data.filter(a => a.status === 'sakit').length)
    summaryStats.value[2]!.value = String(data.filter(a => a.status === 'izin').length)
    summaryStats.value[3]!.value = String(data.filter(a => a.status === 'alpha').length)
  } finally { loading.value = false }
}
onMounted(loadAbsensi)
</script>
