<template>
  <div class="min-h-screen bg-[#F5F7FA] pb-nav">

    <!-- Header -->
    <div class="relative bg-gradient-to-br from-violet-600 to-violet-800 px-5 pt-12 pb-20 overflow-hidden">
      <div class="absolute -top-6 -right-6 w-32 h-32 rounded-full bg-white/5" />
      <div class="absolute bottom-2 left-4 w-16 h-16 rounded-full bg-white/5" />
      <div class="relative flex items-center gap-3">
        <button @click="$router.back()" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center">
          <ChevronLeftIcon class="w-5 h-5 text-white" />
        </button>
        <div>
          <p class="text-white/60 text-xs font-medium">Akademik</p>
          <h1 class="text-white font-bold text-lg">{{ activeSantri?.nama ?? '—' }}</h1>
        </div>
      </div>
    </div>

    <!-- Semester Selector -->
    <div class="relative z-10 px-4 -mt-6 mb-4 flex gap-2 overflow-x-auto scrollbar-hide pb-1">
      <button v-for="s in semesters" :key="s.value" @click="activeSemester = s.value"
              class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-semibold transition-colors"
              :class="activeSemester === s.value
                ? 'bg-violet-600 text-white shadow-md'
                : 'bg-white text-gray-600 border border-gray-200'">
        {{ s.label }}
      </button>
    </div>

    <!-- GPA Card -->
    <div class="px-4 mb-4">
      <div class="bg-gradient-to-br from-violet-600 to-violet-800 rounded-2xl p-5 shadow-lg">
        <div class="flex justify-between items-center">
          <div>
            <p class="text-white/60 text-xs mb-1">Rata-rata Nilai</p>
            <p class="text-white text-4xl font-black">{{ avgNilai ?? '—' }}</p>
            <p class="text-white/60 text-xs mt-1">{{ semester?.label }}</p>
          </div>
          <div class="text-right">
            <div class="w-16 h-16 rounded-2xl bg-white/10 flex items-center justify-center">
              <AcademicCapIcon class="w-8 h-8 text-white" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Nilai Table -->
    <div class="px-4 mb-4">
      <div class="flex items-center gap-2 mb-3">
        <div class="w-1 h-4 bg-violet-600 rounded-full" />
        <h2 class="text-gray-800 font-bold text-sm">Nilai Per Mata Pelajaran</h2>
      </div>

      <div v-if="loading" class="space-y-2">
        <div v-for="i in 5" :key="i" class="bg-white rounded-2xl p-4 border border-gray-100">
          <div class="flex justify-between">
            <div class="skeleton h-3.5 w-32 rounded" />
            <div class="skeleton h-3.5 w-10 rounded" />
          </div>
        </div>
      </div>

      <div v-else-if="nilaiList.length" class="space-y-2">
        <div v-for="n in nilaiList" :key="n.id"
             class="bg-white rounded-2xl border border-gray-100/80 overflow-hidden">
          <div class="p-4 flex items-center gap-3">
            <div class="flex-1 min-w-0">
              <p class="text-gray-800 font-semibold text-sm truncate">{{ n.mata_pelajaran }}</p>
              <div class="flex gap-3 mt-2">
                <span class="text-[10px] text-gray-400">UH: <span class="font-semibold text-gray-600">{{ n.nilai_harian ?? '—' }}</span></span>
                <span class="text-[10px] text-gray-400">UTS: <span class="font-semibold text-gray-600">{{ n.nilai_uts ?? '—' }}</span></span>
                <span class="text-[10px] text-gray-400">UAS: <span class="font-semibold text-gray-600">{{ n.nilai_uas ?? '—' }}</span></span>
              </div>
              <!-- Progress bar -->
              <div class="h-1.5 bg-gray-100 rounded-full mt-2 overflow-hidden">
                <div class="h-full rounded-full transition-all"
                     :class="gradeColor(n.nilai_akhir)"
                     :style="{ width: Math.min(100, (n.nilai_akhir / 100) * 100) + '%' }" />
              </div>
            </div>
            <div class="text-center flex-shrink-0 w-12">
              <p class="text-lg font-black" :class="gradeColor(n.nilai_akhir).replace('bg-', 'text-')">
                {{ n.nilai_akhir ?? '—' }}
              </p>
              <p class="text-[10px] font-bold" :class="gradeColor(n.nilai_akhir).replace('bg-', 'text-')">
                {{ gradeLabel(n.nilai_akhir) }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <div v-else-if="!loading"
           class="bg-white rounded-2xl border border-gray-100 py-14 text-center text-gray-400 text-sm">
        <span class="text-4xl block mb-2">📚</span>
        Belum ada data nilai
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'
import { useSantriStore } from '@/stores/santri'
import { waliApi } from '@/api/wali'
import { ChevronLeftIcon, AcademicCapIcon } from '@heroicons/vue/24/outline'

const santriStore  = useSantriStore()
const activeSantri = computed(() => santriStore.active)
const loading      = ref(true)
const nilaiList    = ref<any[]>([])
const activeSemester = ref('2025-1')

const semesters = [
  { label: 'Sem 1 — 2025/2026', value: '2025-1' },
  { label: 'Sem 2 — 2024/2025', value: '2024-2' },
  { label: 'Sem 1 — 2024/2025', value: '2024-1' },
]

const semester   = computed(() => semesters.find(s => s.value === activeSemester.value))
const avgNilai   = computed(() => {
  const valid = nilaiList.value.filter(n => n.nilai_akhir != null)
  if (!valid.length) return null
  return (valid.reduce((s, n) => s + n.nilai_akhir, 0) / valid.length).toFixed(1)
})

function gradeColor(v: number) {
  if (v >= 90) return 'bg-emerald-500'
  if (v >= 75) return 'bg-blue-500'
  if (v >= 60) return 'bg-amber-400'
  return 'bg-red-500'
}
function gradeLabel(v: number) {
  if (v >= 90) return 'A'
  if (v >= 75) return 'B'
  if (v >= 60) return 'C'
  return 'D'
}

async function loadNilai() {
  if (!activeSantri.value) return
  loading.value = true
  try { nilaiList.value = await waliApi.akademik(activeSantri.value.id, activeSemester.value) as any[] }
  finally { loading.value = false }
}
watch(activeSemester, loadNilai)
onMounted(loadNilai)
</script>
