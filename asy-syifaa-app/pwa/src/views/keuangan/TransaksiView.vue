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
          <p class="text-white/60 text-xs font-medium">Riwayat Transaksi</p>
          <h1 class="text-white font-bold text-lg">{{ activeSantri?.nama ?? '—' }}</h1>
        </div>
      </div>
    </div>

    <!-- Filter Chips -->
    <div class="relative z-10 px-4 -mt-6 mb-4 flex gap-2 overflow-x-auto scrollbar-hide pb-1">
      <button v-for="f in filters" :key="f.value" @click="activeFilter = f.value"
              class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-semibold transition-colors"
              :class="activeFilter === f.value
                ? 'bg-brand-700 text-white shadow-md shadow-brand-700/20'
                : 'bg-white text-gray-600 border border-gray-200'">
        {{ f.label }}
      </button>
    </div>

    <!-- List -->
    <div class="px-4 flex flex-col gap-2">
      <template v-if="loading">
        <div v-for="i in 6" :key="i" class="bg-white rounded-2xl p-4 border border-gray-100">
          <div class="flex items-center gap-3">
            <div class="skeleton w-10 h-10 rounded-xl" />
            <div class="flex-1">
              <div class="skeleton h-3.5 w-40 rounded mb-2" />
              <div class="skeleton h-3 w-24 rounded" />
            </div>
            <div class="skeleton h-4 w-24 rounded" />
          </div>
        </div>
      </template>

      <template v-else-if="filteredTx.length">
        <!-- Group by date -->
        <template v-for="group in groupedTx" :key="group.date">
          <p class="text-xs font-semibold text-gray-400 px-1 mt-2 first:mt-0">{{ group.dateLabel }}</p>
          <div v-for="tx in group.items" :key="tx.id"
               class="bg-white rounded-2xl p-4 border border-gray-100/80 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                 :class="tx.tipe === 'kredit' ? 'bg-emerald-50' : 'bg-red-50'">
              <ArrowDownIcon v-if="tx.tipe === 'kredit'" class="w-5 h-5 text-emerald-600" />
              <ArrowUpIcon v-else class="w-5 h-5 text-red-500" />
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-gray-800 font-semibold text-sm truncate">{{ tx.keterangan }}</p>
              <p class="text-gray-400 text-xs">{{ formatTime(tx.tanggal) }}</p>
            </div>
            <div class="text-right flex-shrink-0">
              <p class="font-bold text-sm" :class="tx.tipe === 'kredit' ? 'text-emerald-600' : 'text-red-500'">
                {{ tx.tipe === 'kredit' ? '+' : '-' }}Rp {{ tx.nominal.toLocaleString('id-ID') }}
              </p>
              <p class="text-gray-300 text-[10px]">Saldo: Rp {{ tx.saldo_setelah?.toLocaleString('id-ID') ?? '—' }}</p>
            </div>
          </div>
        </template>
      </template>

      <div v-else-if="!loading"
           class="text-center py-16 text-gray-400 text-sm bg-white rounded-2xl border border-gray-100">
        <span class="text-4xl block mb-2">📊</span>
        Tidak ada transaksi
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useSantriStore } from '@/stores/santri'
import { waliApi } from '@/api/wali'
import { ChevronLeftIcon, ArrowDownIcon, ArrowUpIcon } from '@heroicons/vue/24/outline'

const santriStore  = useSantriStore()
const activeSantri = computed(() => santriStore.active)
const loading  = ref(true)
const txList   = ref<any[]>([])
const activeFilter = ref('semua')

const filters = [
  { label: 'Semua',        value: 'semua'  },
  { label: 'Pemasukan',    value: 'kredit' },
  { label: 'Pengeluaran',  value: 'debit'  },
]

const filteredTx = computed(() =>
  activeFilter.value === 'semua' ? txList.value : txList.value.filter(t => t.tipe === activeFilter.value)
)

const groupedTx = computed(() => {
  const map = new Map<string, any[]>()
  for (const tx of filteredTx.value) {
    const key = tx.tanggal.slice(0, 10)
    if (!map.has(key)) map.set(key, [])
    map.get(key)!.push(tx)
  }
  return [...map.entries()].map(([date, items]) => ({
    date,
    dateLabel: new Date(date).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long' }),
    items,
  }))
})

function formatTime(d: string) {
  return new Date(d).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
}

onMounted(async () => {
  if (!activeSantri.value) return
  try { txList.value = await waliApi.transaksi(activeSantri.value.id) as any[] }
  finally { loading.value = false }
})
</script>
