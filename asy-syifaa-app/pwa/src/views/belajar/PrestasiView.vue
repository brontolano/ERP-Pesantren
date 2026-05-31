<template>
  <div class="min-h-screen bg-[#F5F7FA] pb-nav">

    <!-- Header -->
    <div class="relative bg-gradient-to-br from-amber-500 to-amber-700 px-5 pt-12 pb-24 overflow-hidden">
      <div class="absolute -top-6 -right-6 w-32 h-32 rounded-full bg-white/5" />
      <div class="absolute bottom-2 left-4 w-16 h-16 rounded-full bg-white/5" />
      <div class="relative flex items-center gap-3">
        <button @click="$router.back()" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center">
          <ChevronLeftIcon class="w-5 h-5 text-white" />
        </button>
        <div>
          <p class="text-white/70 text-xs font-medium">Prestasi & Aktivitas</p>
          <h1 class="text-white font-bold text-lg">{{ activeSantri?.nama ?? '—' }}</h1>
        </div>
      </div>
    </div>

    <!-- Score Cards -->
    <div class="relative z-10 px-4 -mt-16 mb-4 grid grid-cols-3 gap-2">
      <div v-for="s in scoreCards" :key="s.label"
           class="bg-white rounded-2xl p-3 shadow-sm border border-gray-100/80 text-center">
        <div class="text-2xl mb-1">{{ s.emoji }}</div>
        <p class="font-black text-base" :class="s.color">{{ s.value }}</p>
        <p class="text-gray-400 text-[10px] font-medium">{{ s.label }}</p>
      </div>
    </div>

    <!-- Category Tabs -->
    <div class="px-4 mb-4 flex gap-2 overflow-x-auto scrollbar-hide">
      <button v-for="t in tabs" :key="t.value" @click="activeTab = t.value"
              class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-semibold transition-colors"
              :class="activeTab === t.value
                ? 'bg-amber-500 text-white shadow-md'
                : 'bg-white text-gray-600 border border-gray-200'">
        {{ t.label }}
      </button>
    </div>

    <!-- List -->
    <div class="px-4 flex flex-col gap-2">
      <template v-if="loading">
        <div v-for="i in 4" :key="i" class="bg-white rounded-2xl p-4 border border-gray-100">
          <div class="skeleton h-4 w-40 rounded mb-2" />
          <div class="skeleton h-3 w-24 rounded" />
        </div>
      </template>

      <template v-else-if="filteredList.length">
        <div v-for="item in filteredList" :key="item.id"
             class="bg-white rounded-2xl border border-gray-100/80 overflow-hidden">
          <div class="h-1" :class="levelColor(item.level)" />
          <div class="p-4 flex items-start gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 bg-amber-50">
              <span class="text-xl">{{ categoryEmoji(item.kategori) }}</span>
            </div>
            <div class="flex-1">
              <div class="flex items-start justify-between gap-2">
                <p class="text-gray-800 font-bold text-sm leading-tight">{{ item.nama }}</p>
                <span v-if="item.level" class="text-xs px-2 py-0.5 rounded-full font-semibold flex-shrink-0"
                      :class="levelChip(item.level)">
                  {{ item.level }}
                </span>
              </div>
              <p class="text-gray-400 text-xs mt-0.5">{{ item.keterangan }}</p>
              <p class="text-gray-300 text-[10px] mt-1">{{ formatDate(item.tanggal) }}</p>
            </div>
          </div>
        </div>
      </template>

      <div v-else-if="!loading"
           class="bg-white rounded-2xl border border-gray-100 py-14 text-center text-gray-400 text-sm">
        <span class="text-4xl block mb-2">🏅</span>
        Belum ada data {{ activeTabLabel }}
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
const prestasiList = ref<any[]>([])
const activeTab    = ref('semua')

const tabs = [
  { label: 'Semua',       value: 'semua'      },
  { label: '🎯 Ekskul',   value: 'ekskul'     },
  { label: '🤝 Khidmah',  value: 'khidmah'    },
  { label: '📏 Disiplin', value: 'kedisiplinan'},
]

const scoreCards = ref([
  { label: 'Ekskul',    emoji: '🎯', value: '—', color: 'text-blue-600'   },
  { label: 'Khidmah',   emoji: '🤝', value: '—', color: 'text-emerald-600'},
  { label: 'Disiplin',  emoji: '📏', value: '—', color: 'text-amber-600'  },
])

const activeTabLabel = computed(() => tabs.find(t => t.value === activeTab.value)?.label ?? '')

const filteredList = computed(() =>
  activeTab.value === 'semua' ? prestasiList.value
    : prestasiList.value.filter(p => p.kategori === activeTab.value)
)

function categoryEmoji(k: string) {
  return { ekskul: '🎯', khidmah: '🤝', kedisiplinan: '📏' }[k] ?? '⭐'
}
function levelColor(l: string) {
  return { emas: 'bg-amber-400', perak: 'bg-gray-400', perunggu: 'bg-orange-600', positif: 'bg-emerald-400', negatif: 'bg-red-400' }[l] ?? 'bg-gray-200'
}
function levelChip(l: string) {
  return {
    emas:     'bg-amber-50 text-amber-700',
    perak:    'bg-gray-100 text-gray-600',
    perunggu: 'bg-orange-50 text-orange-700',
    positif:  'bg-emerald-50 text-emerald-700',
    negatif:  'bg-red-50 text-red-700',
  }[l] ?? 'bg-gray-100 text-gray-500'
}
function formatDate(d: string) {
  return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

onMounted(async () => {
  if (!activeSantri.value) return
  try {
    const data = await waliApi.prestasi(activeSantri.value.id) as any[]
    prestasiList.value = data
    scoreCards.value[0].value = String(data.filter(p => p.kategori === 'ekskul').length)
    scoreCards.value[1].value = String(data.filter(p => p.kategori === 'khidmah').length)
    scoreCards.value[2].value = String(data.filter(p => p.kategori === 'kedisiplinan').length)
  } finally { loading.value = false }
})
</script>
