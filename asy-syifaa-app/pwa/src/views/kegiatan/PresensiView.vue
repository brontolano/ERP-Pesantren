<template>
  <div class="min-h-screen bg-[#F5F7FA] pb-nav">

    <div class="relative bg-gradient-to-br from-brand-700 to-brand-800 px-5 pt-12 pb-20 overflow-hidden">
      <div class="absolute -top-6 -right-6 w-32 h-32 rounded-full bg-white/5" />
      <div class="absolute bottom-2 left-4 w-16 h-16 rounded-full bg-white/5" />
      <div class="relative flex items-center gap-3">
        <button @click="$router.back()" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center">
          <ChevronLeftIcon class="w-5 h-5 text-white" />
        </button>
        <div>
          <p class="text-white/60 text-xs font-medium">Presensi Kegiatan</p>
          <h1 class="text-white font-bold text-lg">{{ activeSantri?.nama ?? '—' }}</h1>
        </div>
      </div>
    </div>

    <!-- Summary -->
    <div class="relative z-10 px-4 -mt-10 mb-4 grid grid-cols-3 gap-2">
      <div v-for="s in stats" :key="s.label" class="bg-white rounded-2xl p-3 shadow-sm border border-gray-100/80 text-center">
        <p class="font-black text-base" :class="s.color">{{ s.value }}</p>
        <p class="text-gray-400 text-[10px]">{{ s.label }}</p>
      </div>
    </div>

    <!-- Category Filter -->
    <div class="px-4 mb-4 flex gap-2 overflow-x-auto scrollbar-hide pb-1">
      <button v-for="f in filters" :key="f.value" @click="activeFilter = f.value"
              class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-semibold transition-colors"
              :class="activeFilter === f.value
                ? 'bg-brand-700 text-white shadow-md'
                : 'bg-white text-gray-600 border border-gray-200'">
        {{ f.label }}
      </button>
    </div>

    <!-- Presensi List -->
    <div class="px-4 flex flex-col gap-2">
      <template v-if="loading">
        <div v-for="i in 5" :key="i" class="bg-white rounded-2xl p-4 border border-gray-100">
          <div class="flex gap-3"><div class="skeleton w-9 h-9 rounded-xl" /><div class="flex-1"><div class="skeleton h-3.5 w-32 rounded mb-2" /><div class="skeleton h-3 w-24 rounded" /></div></div>
        </div>
      </template>

      <template v-else-if="filteredList.length">
        <div v-for="p in filteredList" :key="p.id"
             class="bg-white rounded-2xl border border-gray-100/80 flex items-center gap-3 p-4">
          <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
               :class="p.hadir ? 'bg-emerald-50' : 'bg-red-50'">
            <span class="text-base">{{ p.hadir ? '✅' : '❌' }}</span>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-gray-800 font-semibold text-sm truncate">{{ p.kegiatan }}</p>
            <p class="text-gray-400 text-xs">{{ formatDate(p.tanggal) }} • {{ p.sesi }}</p>
          </div>
          <span class="text-xs px-2.5 py-1 rounded-full font-semibold flex-shrink-0"
                :class="p.hadir ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700'">
            {{ p.hadir ? 'Hadir' : 'Tidak Hadir' }}
          </span>
        </div>
      </template>

      <div v-else-if="!loading" class="bg-white rounded-2xl border border-gray-100 py-14 text-center text-gray-400 text-sm">
        <span class="text-4xl block mb-2">📋</span>
        Belum ada data presensi
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
const loading        = ref(true)
const presensiList   = ref<any[]>([])
const activeFilter   = ref('semua')

const filters = [
  { label: 'Semua',         value: 'semua'   },
  { label: '✅ Hadir',      value: 'hadir'   },
  { label: '❌ Tidak Hadir', value: 'absen'  },
]

const stats = ref([
  { label: 'Total',        value: '—', color: 'text-gray-700'   },
  { label: 'Hadir',        value: '—', color: 'text-emerald-600'},
  { label: 'Tidak Hadir',  value: '—', color: 'text-red-500'    },
])

const filteredList = computed(() => {
  if (activeFilter.value === 'hadir') return presensiList.value.filter(p => p.hadir)
  if (activeFilter.value === 'absen') return presensiList.value.filter(p => !p.hadir)
  return presensiList.value
})

function formatDate(d: string) {
  return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

onMounted(async () => {
  if (!activeSantri.value) return
  try {
    const data = await waliApi.presensi(activeSantri.value.id) as any[]
    presensiList.value = data
    stats.value[0].value = String(data.length)
    stats.value[1].value = String(data.filter(p => p.hadir).length)
    stats.value[2].value = String(data.filter(p => !p.hadir).length)
  } finally { loading.value = false }
})
</script>
