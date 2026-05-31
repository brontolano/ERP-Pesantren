<template>
  <div class="min-h-screen bg-[#F5F7FA] pb-nav">

    <!-- Header -->
    <div class="relative bg-gradient-to-br from-brand-700 to-brand-800 px-5 pt-12 pb-32 overflow-hidden">
      <div class="absolute -top-6 -right-6 w-32 h-32 rounded-full bg-white/5" />
      <div class="absolute bottom-2 left-4 w-16 h-16 rounded-full bg-white/5" />
      <div class="absolute top-8 right-20 w-10 h-10 rounded-full bg-white/5" />
      <div class="relative">
        <p class="text-white/60 text-xs font-medium tracking-wide uppercase">Keuangan</p>
        <h1 class="text-white font-bold text-xl mt-0.5">{{ activeSantri?.nama ?? '—' }}</h1>
        <p class="text-white/50 text-xs mt-1">Kelola keuangan santri Anda</p>
      </div>
    </div>

    <!-- Balance Preview Card -->
    <div class="relative z-10 px-4 -mt-16 mb-4">
      <router-link to="/keuangan/tabungan"
           class="block bg-gradient-to-br from-brand-600 to-brand-800 rounded-3xl p-5 shadow-xl shadow-brand-900/20">
        <div class="flex justify-between items-start mb-6">
          <div>
            <p class="text-white/60 text-xs font-medium mb-1">Saldo Tabungan</p>
            <p class="text-white text-2xl font-black">
              {{ tabungan ? 'Rp ' + tabungan.saldo.toLocaleString('id-ID') : '—' }}
            </p>
          </div>
          <div class="w-10 h-10 rounded-2xl bg-white/15 flex items-center justify-center">
            <WalletIcon class="w-5 h-5 text-white" />
          </div>
        </div>
        <div class="flex justify-between items-center">
          <div>
            <p class="text-white/50 text-[10px] mb-0.5">Limit Jajan Harian</p>
            <p class="text-white/90 text-sm font-bold">
              {{ tabungan ? 'Rp ' + tabungan.limit_harian.toLocaleString('id-ID') : '—' }}
            </p>
          </div>
          <div class="flex items-center gap-1 text-white/70 text-xs">
            <span>Lihat Detail</span>
            <ChevronRightIcon class="w-4 h-4" />
          </div>
        </div>
      </router-link>
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

    <!-- Recent Transactions -->
    <div class="px-4 mb-4">
      <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
          <div class="w-1 h-4 bg-brand-600 rounded-full" />
          <h2 class="text-gray-800 font-bold text-sm">Transaksi Terakhir</h2>
        </div>
        <router-link to="/keuangan/transaksi" class="text-brand-700 text-xs font-semibold">Lihat Semua</router-link>
      </div>

      <div v-if="loadingTx" class="space-y-3">
        <div v-for="i in 3" :key="i" class="bg-white rounded-2xl p-4 border border-gray-100">
          <div class="flex items-center gap-3">
            <div class="skeleton w-9 h-9 rounded-xl" />
            <div class="flex-1">
              <div class="skeleton h-3.5 w-32 rounded mb-2" />
              <div class="skeleton h-3 w-20 rounded" />
            </div>
            <div class="skeleton h-4 w-20 rounded" />
          </div>
        </div>
      </div>

      <div v-else-if="recentTx.length" class="space-y-2">
        <div v-for="tx in recentTx" :key="tx.id"
             class="bg-white rounded-2xl p-4 border border-gray-100/80 flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
               :class="tx.tipe === 'kredit' ? 'bg-emerald-50' : 'bg-red-50'">
            <ArrowDownIcon v-if="tx.tipe === 'kredit'" class="w-4 h-4 text-emerald-600" />
            <ArrowUpIcon v-else class="w-4 h-4 text-red-500" />
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-gray-800 font-semibold text-sm truncate">{{ tx.keterangan }}</p>
            <p class="text-gray-400 text-xs">{{ formatDate(tx.tanggal) }}</p>
          </div>
          <p class="font-bold text-sm flex-shrink-0"
             :class="tx.tipe === 'kredit' ? 'text-emerald-600' : 'text-red-500'">
            {{ tx.tipe === 'kredit' ? '+' : '-' }}Rp {{ tx.nominal.toLocaleString('id-ID') }}
          </p>
        </div>
      </div>

      <div v-else class="bg-white rounded-2xl border border-gray-100 py-10 text-center text-gray-400 text-sm">
        <span class="text-3xl block mb-2">📊</span>
        Belum ada transaksi
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useSantriStore } from '@/stores/santri'
import { waliApi } from '@/api/wali'
import {
  WalletIcon, BanknotesIcon, QrCodeIcon, ClipboardDocumentListIcon, ChevronRightIcon,
  ArrowDownIcon, ArrowUpIcon,
} from '@heroicons/vue/24/outline'

const santriStore  = useSantriStore()
const activeSantri = computed(() => santriStore.active)

const tabungan  = ref<{ saldo: number; limit_harian: number } | null>(null)
const recentTx  = ref<any[]>([])
const loadingTx = ref(true)

const features = [
  { to: '/keuangan/tabungan',  label: 'Tabungan',  desc: 'Saldo & limit jajan', icon: WalletIcon,                  iconBg: 'bg-brand-50',   iconColor: 'text-brand-700' },
  { to: '/keuangan/transaksi', label: 'Transaksi', desc: 'Riwayat transaksi',   icon: ClipboardDocumentListIcon,   iconBg: 'bg-blue-50',    iconColor: 'text-blue-600'  },
  { to: '/keuangan/tagihan',   label: 'Tagihan',   desc: 'Tagihan & invoice',   icon: BanknotesIcon,               iconBg: 'bg-amber-50',   iconColor: 'text-amber-600' },
  { to: '/keuangan/bayar',     label: 'Bayar',     desc: 'QRIS, VA & lainnya',  icon: QrCodeIcon,                  iconBg: 'bg-violet-50',  iconColor: 'text-violet-600'},
]

function formatDate(d: string) {
  return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })
}

onMounted(async () => {
  if (!activeSantri.value) return
  try {
    const [tab, tx] = await Promise.allSettled([
      waliApi.tabungan(activeSantri.value.id),
      waliApi.transaksi(activeSantri.value.id, { limit: 3 }),
    ])
    if (tab.status === 'fulfilled') tabungan.value = tab.value as any
    if (tx.status  === 'fulfilled') recentTx.value  = (tx.value as any).slice(0, 3)
  } finally { loadingTx.value = false }
})
</script>
