<template>
  <div class="min-h-screen bg-[#F5F7FA] pb-nav">

    <div class="relative bg-gradient-to-br from-violet-600 to-violet-800 px-5 pt-12 pb-20 overflow-hidden">
      <div class="absolute -top-6 -right-6 w-32 h-32 rounded-full bg-white/5" />
      <div class="absolute bottom-2 left-4 w-16 h-16 rounded-full bg-white/5" />
      <div class="relative flex items-center justify-between">
        <div class="flex items-center gap-3">
          <button @click="$router.back()" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center">
            <ChevronLeftIcon class="w-5 h-5 text-white" />
          </button>
          <div>
            <p class="text-white/60 text-xs font-medium">Konseling</p>
            <h1 class="text-white font-bold text-lg">{{ activeSantri?.nama ?? '—' }}</h1>
          </div>
        </div>
        <button @click="openSheet"
                class="bg-white text-violet-700 text-sm font-bold px-4 py-2.5 rounded-xl active:bg-gray-50 shadow-sm">
          + Minta Sesi
        </button>
      </div>
    </div>

    <!-- List -->
    <div class="relative z-10 px-4 -mt-10 flex flex-col gap-3">
      <template v-if="loading">
        <div v-for="i in 3" :key="i" class="bg-white rounded-2xl p-4 border border-gray-100">
          <div class="skeleton h-4 w-40 rounded mb-2" /><div class="skeleton h-3 w-24 rounded" />
        </div>
      </template>

      <template v-else-if="konselingList.length">
        <div v-for="k in konselingList" :key="k.id"
             class="bg-white rounded-2xl shadow-sm border border-gray-100/80 overflow-hidden">
          <div class="h-1 bg-violet-500" />
          <div class="p-4">
            <div class="flex items-start justify-between gap-2 mb-2">
              <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-violet-50 flex items-center justify-center">
                  <ChatBubbleLeftRightIcon class="w-4 h-4 text-violet-600" />
                </div>
                <div>
                  <p class="font-bold text-gray-800 text-sm">{{ k.topik }}</p>
                  <p class="text-gray-400 text-xs">{{ k.konselor }}</p>
                </div>
              </div>
              <span class="text-xs px-2.5 py-1 rounded-full font-semibold flex-shrink-0"
                    :class="statusChip(k.status)">
                {{ statusLabel(k.status) }}
              </span>
            </div>
            <p class="text-xs text-gray-500">📅 {{ formatDate(k.tanggal) }}</p>
            <p v-if="k.catatan" class="text-xs text-gray-400 mt-2 bg-gray-50 rounded-xl px-3 py-2 leading-relaxed">
              {{ k.catatan }}
            </p>
          </div>
        </div>
      </template>

      <div v-else-if="!loading" class="bg-white rounded-2xl border border-gray-100 py-16 text-center text-gray-400 text-sm">
        <span class="text-4xl block mb-2">💬</span>
        Belum ada sesi konseling
      </div>
    </div>

    <!-- Sheet -->
    <Transition name="fade">
      <div v-if="sheet" class="fixed inset-0 bg-black/50 z-[60]" @click="sheet = false" />
    </Transition>
    <Transition name="sheet-up">
      <div v-if="sheet"
           class="fixed bottom-0 left-0 right-0 z-[70] bg-white rounded-t-3xl shadow-2xl px-5 pt-5"
           :style="{ paddingBottom: 'calc(1.5rem + env(safe-area-inset-bottom, 0px))' }">
        <div class="w-10 h-1 bg-gray-200 rounded-full mx-auto mb-5" />
        <h3 class="text-gray-800 font-bold text-base mb-4">Minta Sesi Konseling</h3>
        <form @submit.prevent="submitKonseling" class="flex flex-col gap-3">
          <div>
            <label class="text-sm font-semibold text-gray-700 mb-1.5 block">Topik</label>
            <input v-model="form.topik" type="text" required placeholder="Topik yang ingin didiskusikan"
                   class="w-full px-4 py-3.5 rounded-xl border border-gray-200 text-gray-800 text-base bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-500/30" />
          </div>
          <div>
            <label class="text-sm font-semibold text-gray-700 mb-1.5 block">Catatan (opsional)</label>
            <textarea v-model="form.catatan" rows="3" placeholder="Deskripsi singkat..."
                      class="w-full px-4 py-3.5 rounded-xl border border-gray-200 text-gray-800 text-base bg-gray-50 resize-none focus:outline-none focus:ring-2 focus:ring-brand-500/30" />
          </div>
          <button type="submit" :disabled="submitting"
                  class="w-full bg-violet-600 text-white font-bold py-4 rounded-2xl active:bg-violet-700 disabled:opacity-60">
            {{ submitting ? 'Mengirim...' : 'Kirim Permintaan' }}
          </button>
        </form>
      </div>
    </Transition>

  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useSantriStore } from '@/stores/santri'
import { waliApi } from '@/api/wali'
import { ChevronLeftIcon, ChatBubbleLeftRightIcon } from '@heroicons/vue/24/outline'

const santriStore  = useSantriStore()
const activeSantri = computed(() => santriStore.active)
const loading        = ref(true)
const konselingList  = ref<any[]>([])
const sheet          = ref(false)
const submitting     = ref(false)
const form           = ref({ topik: '', catatan: '' })

function statusLabel(s: string) {
  return { menunggu: 'Menunggu', dijadwalkan: 'Dijadwalkan', selesai: 'Selesai' }[s] ?? s
}
function statusChip(s: string) {
  return { menunggu: 'bg-amber-50 text-amber-700', dijadwalkan: 'bg-blue-50 text-blue-700', selesai: 'bg-emerald-50 text-emerald-700' }[s] ?? 'bg-gray-100 text-gray-500'
}
function formatDate(d: string) {
  return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}
function openSheet() { form.value = { topik: '', catatan: '' }; sheet.value = true }

async function submitKonseling() {
  if (!activeSantri.value) return
  submitting.value = true
  try {
    await waliApi.ajukanKonseling(activeSantri.value.id, form.value)
    sheet.value = false
    await loadKonseling()
  } catch { /* handle */ } finally { submitting.value = false }
}

async function loadKonseling() {
  if (!activeSantri.value) return
  loading.value = true
  try { konselingList.value = await waliApi.daftarKonseling(activeSantri.value.id) as any[] }
  finally { loading.value = false }
}
onMounted(loadKonseling)
</script>
