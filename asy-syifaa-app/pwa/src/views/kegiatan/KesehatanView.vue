<template>
  <div class="min-h-screen bg-[#F5F7FA] pb-nav">

    <div class="relative bg-gradient-to-br from-red-500 to-rose-700 px-5 pt-12 pb-20 overflow-hidden">
      <div class="absolute -top-6 -right-6 w-32 h-32 rounded-full bg-white/5" />
      <div class="absolute bottom-2 left-4 w-16 h-16 rounded-full bg-white/5" />
      <div class="relative flex items-center gap-3">
        <button @click="$router.back()" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center">
          <ChevronLeftIcon class="w-5 h-5 text-white" />
        </button>
        <div>
          <p class="text-white/60 text-xs font-medium">Riwayat Kesehatan</p>
          <h1 class="text-white font-bold text-lg">{{ activeSantri?.nama ?? '—' }}</h1>
        </div>
      </div>
    </div>

    <!-- Stats -->
    <div class="relative z-10 px-4 -mt-10 mb-4 grid grid-cols-3 gap-2">
      <div v-for="s in stats" :key="s.label" class="bg-white rounded-2xl p-3 shadow-sm border border-gray-100/80 text-center">
        <p class="text-xl mb-0.5">{{ s.emoji }}</p>
        <p class="font-black text-base" :class="s.color">{{ s.value }}</p>
        <p class="text-gray-400 text-[10px]">{{ s.label }}</p>
      </div>
    </div>

    <!-- Health List -->
    <div class="px-4 flex flex-col gap-3">
      <template v-if="loading">
        <div v-for="i in 4" :key="i" class="bg-white rounded-2xl p-4 border border-gray-100">
          <div class="skeleton h-4 w-40 rounded mb-2" /><div class="skeleton h-3 w-24 rounded" />
        </div>
      </template>

      <template v-else-if="kesehatanList.length">
        <div v-for="k in kesehatanList" :key="k.id"
             class="bg-white rounded-2xl shadow-sm border border-gray-100/80 overflow-hidden">
          <div class="h-1" :class="severityColor(k.tingkat)" />
          <div class="p-4">
            <div class="flex items-start justify-between gap-2 mb-2">
              <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-red-50 flex items-center justify-center">
                  <HeartIcon class="w-4 h-4 text-red-500" />
                </div>
                <p class="font-bold text-gray-800 text-sm">{{ k.keluhan }}</p>
              </div>
              <span class="text-xs px-2.5 py-1 rounded-full font-semibold flex-shrink-0"
                    :class="severityChip(k.tingkat)">
                {{ k.tingkat }}
              </span>
            </div>
            <p class="text-xs text-gray-500 mb-1">🏥 {{ k.tindakan }}</p>
            <p class="text-xs text-gray-400">📅 {{ formatDate(k.tanggal) }}</p>
            <div v-if="k.obat" class="mt-2 bg-amber-50 rounded-xl px-3 py-2">
              <p class="text-xs text-amber-700">💊 Obat: {{ k.obat }}</p>
            </div>
          </div>
        </div>
      </template>

      <div v-else-if="!loading" class="bg-white rounded-2xl border border-gray-100 py-16 text-center text-gray-400 text-sm">
        <span class="text-4xl block mb-2">❤️</span>
        Alhamdulillah, santri sehat!
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useSantriStore } from '@/stores/santri'
import { waliApi } from '@/api/wali'
import { ChevronLeftIcon, HeartIcon } from '@heroicons/vue/24/outline'

const santriStore  = useSantriStore()
const activeSantri = computed(() => santriStore.active)
const loading        = ref(true)
const kesehatanList  = ref<any[]>([])

const stats = ref([
  { label: 'Total',  emoji: '📋', value: '—', color: 'text-gray-700'  },
  { label: 'Ringan', emoji: '😊', value: '—', color: 'text-blue-500'  },
  { label: 'Berat',  emoji: '😷', value: '—', color: 'text-red-500'   },
])

function severityColor(t: string) {
  return { ringan: 'bg-blue-400', sedang: 'bg-amber-400', berat: 'bg-red-500' }[t] ?? 'bg-gray-200'
}
function severityChip(t: string) {
  return { ringan: 'bg-blue-50 text-blue-700', sedang: 'bg-amber-50 text-amber-700', berat: 'bg-red-50 text-red-700' }[t] ?? 'bg-gray-100 text-gray-500'
}
function formatDate(d: string) {
  return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

onMounted(async () => {
  if (!activeSantri.value) return
  try {
    const data = await waliApi.kesehatan(activeSantri.value.id) as any[]
    kesehatanList.value = data
    stats.value[0].value = String(data.length)
    stats.value[1].value = String(data.filter(k => k.tingkat === 'ringan').length)
    stats.value[2].value = String(data.filter(k => k.tingkat === 'berat').length)
  } finally { loading.value = false }
})
</script>
