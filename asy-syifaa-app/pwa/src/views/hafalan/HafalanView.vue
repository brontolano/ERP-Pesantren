<template>
  <div class="flex flex-col min-h-full bg-[#F5F7FA]">

    <!-- Header -->
    <div class="relative bg-gradient-to-br from-brand-700 to-brand-800 px-5 pt-12 pb-20 overflow-hidden">
      <div class="absolute -top-6 -right-6 w-32 h-32 rounded-full bg-white/5" />
      <div class="absolute bottom-2 left-4 w-16 h-16 rounded-full bg-white/5" />
      <div class="relative">
        <p class="text-white/60 text-xs font-medium tracking-wide">Progress Hafalan</p>
        <h1 class="text-white font-bold text-xl mt-0.5">{{ santriStore.active?.nama ?? '' }}</h1>
      </div>
    </div>

    <div class="relative z-10 px-4 -mt-12 flex flex-col gap-4 pb-6">

      <!-- Progress Card -->
      <div v-if="loading" class="skeleton h-40 rounded-2xl" />
      <div v-else-if="progress"
           class="bg-white rounded-2xl p-5 shadow-lg shadow-brand-900/10 border border-white">
        <!-- Header progress -->
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center gap-2.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
              <BookOpenIcon class="w-5 h-5 text-emerald-600" />
            </div>
            <div>
              <p class="font-bold text-gray-800 text-sm">Al-Qur'an</p>
              <p class="text-xs text-gray-500">{{ progress.total_juz_quran }} dari {{ progress.target_juz_quran }} Juz</p>
            </div>
          </div>
          <div class="text-right">
            <p class="text-3xl font-black text-brand-700 leading-none">{{ progress.persen_quran }}</p>
            <p class="text-xs text-gray-400 font-medium">persen</p>
          </div>
        </div>

        <!-- Progress bar gradient -->
        <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
          <div class="h-full rounded-full transition-all duration-700 ease-out
                      bg-gradient-to-r from-brand-500 to-brand-700"
               :style="{ width: progress.persen_quran + '%' }" />
        </div>
        <div class="flex justify-between mt-1.5">
          <span class="text-[10px] text-gray-400">{{ progress.total_juz_quran }} Juz</span>
          <span class="text-[10px] text-gray-400">Target: {{ progress.target_juz_quran }} Juz</span>
        </div>

        <!-- Hadist progress (if available) -->
        <div v-if="progress.target_hadist" class="mt-4 pt-4 border-t border-gray-50">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-semibold text-gray-700">Hadist</span>
            <span class="text-sm font-bold text-amber-600">{{ progress.total_hadist }}/{{ progress.target_hadist }}</span>
          </div>
          <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full rounded-full bg-gradient-to-r from-amber-400 to-amber-500 transition-all duration-700"
                 :style="{ width: Math.round((progress.total_hadist / progress.target_hadist) * 100) + '%' }" />
          </div>
        </div>

        <p v-if="progress.catatan"
           class="mt-4 text-xs text-gray-500 bg-gray-50 rounded-xl p-3 border border-gray-100 leading-relaxed">
          💬 {{ progress.catatan }}
        </p>
      </div>

      <div v-else-if="!loading"
           class="bg-amber-50 rounded-2xl p-5 border border-amber-100 text-center text-sm text-amber-700">
        Belum ada data progress hafalan
      </div>

      <!-- Riwayat Setoran -->
      <div>
        <div class="flex items-center gap-2 mb-3">
          <div class="w-1 h-4 bg-brand-600 rounded-full" />
          <h2 class="text-sm font-bold text-gray-700">Riwayat Setoran</h2>
        </div>

        <div v-if="loading" class="flex flex-col gap-3">
          <div v-for="i in 3" :key="i" class="skeleton h-20 rounded-2xl" />
        </div>

        <div v-else-if="riwayat.length" class="flex flex-col gap-2.5">
          <div v-for="r in riwayat" :key="r.id"
               class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100/80 flex gap-3">
            <!-- Left color bar based on nilai -->
            <div class="w-1 rounded-full flex-shrink-0 self-stretch"
                 :class="{
                   'bg-emerald-400': r.nilai === 'A',
                   'bg-sky-400':     r.nilai === 'B',
                   'bg-amber-400':   r.nilai === 'C',
                   'bg-red-400':     r.nilai === 'D',
                   'bg-gray-200':    !r.nilai,
                 }" />
            <div class="flex-1 min-w-0">
              <div class="flex items-start justify-between gap-2">
                <p class="font-semibold text-gray-800 text-sm leading-tight">{{ r.pencapaian }}</p>
                <span v-if="r.nilai"
                      class="px-2.5 py-0.5 rounded-full text-xs font-bold flex-shrink-0"
                      :class="{
                        'bg-emerald-50 text-emerald-700': r.nilai === 'A',
                        'bg-sky-50 text-sky-700':         r.nilai === 'B',
                        'bg-amber-50 text-amber-700':     r.nilai === 'C',
                        'bg-red-50 text-red-700':         r.nilai === 'D',
                      }">
                  Nilai {{ r.nilai }}
                </span>
              </div>
              <div class="flex items-center gap-2 mt-1.5">
                <span class="text-xs text-gray-400">{{ r.jenis_label }}</span>
                <span class="text-gray-200">·</span>
                <span class="text-xs text-gray-400">{{ formatTanggal(r.tanggal) }}</span>
              </div>
              <div v-if="r.ustadz" class="flex items-center gap-1 mt-1">
                <UserIcon class="w-3 h-3 text-gray-300" />
                <span class="text-[11px] text-gray-400">{{ r.ustadz }}</span>
              </div>
              <p v-if="r.catatan" class="text-xs text-gray-400 mt-1 italic">{{ r.catatan }}</p>
            </div>
          </div>
        </div>

        <div v-else-if="!loading"
             class="text-center py-12 text-gray-400 text-sm bg-white rounded-2xl border border-gray-100">
          <span class="text-3xl block mb-2">📖</span>
          Belum ada riwayat setoran
        </div>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useSantriStore } from '@/stores/santri'
import { waliApi, type TahfidzProgress, type TahfidzRecord } from '@/api/wali'
import { BookOpenIcon, UserIcon } from '@heroicons/vue/24/outline'

const santriStore = useSantriStore()
const loading  = ref(true)
const progress = ref<TahfidzProgress | null>(null)
const riwayat  = ref<TahfidzRecord[]>([])

onMounted(async () => {
  if (!santriStore.active) await santriStore.fetch()
  if (!santriStore.active) return
  const data = await waliApi.getHafalan(santriStore.active.id)
  progress.value = data.progress
  riwayat.value  = data.riwayat_setor
  loading.value  = false
})

function formatTanggal(d: string) {
  return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }).format(new Date(d))
}
</script>
