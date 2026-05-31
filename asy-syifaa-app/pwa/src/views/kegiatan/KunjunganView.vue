<template>
  <div class="min-h-screen bg-[#F5F7FA] pb-nav">

    <div class="relative bg-gradient-to-br from-emerald-600 to-emerald-800 px-5 pt-12 pb-20 overflow-hidden">
      <div class="absolute -top-6 -right-6 w-32 h-32 rounded-full bg-white/5" />
      <div class="absolute bottom-2 left-4 w-16 h-16 rounded-full bg-white/5" />
      <div class="relative flex items-center justify-between">
        <div class="flex items-center gap-3">
          <button @click="$router.back()" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center">
            <ChevronLeftIcon class="w-5 h-5 text-white" />
          </button>
          <div>
            <p class="text-white/60 text-xs font-medium">Kunjungan</p>
            <h1 class="text-white font-bold text-lg">{{ activeSantri?.nama ?? '—' }}</h1>
          </div>
        </div>
        <button @click="openSheet"
                class="bg-white text-emerald-700 text-sm font-bold px-4 py-2.5 rounded-xl active:bg-gray-50 shadow-sm">
          + Ajukan
        </button>
      </div>
    </div>

    <!-- Info Card -->
    <div class="relative z-10 px-4 -mt-10 mb-4">
      <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-4 flex gap-3">
        <InformationCircleIcon class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" />
        <div>
          <p class="text-emerald-800 font-semibold text-sm">Jadwal Kunjungan Umum</p>
          <p class="text-emerald-700 text-xs mt-0.5">Setiap Ahad pukul 08.00–16.00 WIB</p>
        </div>
      </div>
    </div>

    <!-- List -->
    <div class="px-4 flex flex-col gap-3">
      <template v-if="loading">
        <div v-for="i in 3" :key="i" class="bg-white rounded-2xl p-4 border border-gray-100">
          <div class="skeleton h-4 w-40 rounded mb-2" /><div class="skeleton h-3 w-24 rounded" />
        </div>
      </template>

      <template v-else-if="kunjunganList.length">
        <div v-for="k in kunjunganList" :key="k.id"
             class="bg-white rounded-2xl shadow-sm border border-gray-100/80 overflow-hidden">
          <div class="h-1" :class="{ 'bg-amber-400': k.status==='menunggu','bg-emerald-400': k.status==='disetujui','bg-red-400': k.status==='ditolak' }" />
          <div class="p-4">
            <div class="flex items-start justify-between gap-2 mb-2">
              <div>
                <p class="font-bold text-gray-800 text-sm">{{ k.nama_pengunjung }}</p>
                <p class="text-gray-400 text-xs">{{ k.hubungan }}</p>
              </div>
              <span class="text-xs px-2.5 py-1 rounded-full font-semibold flex-shrink-0"
                    :class="{ 'bg-amber-50 text-amber-700':k.status==='menunggu','bg-emerald-50 text-emerald-700':k.status==='disetujui','bg-red-50 text-red-700':k.status==='ditolak' }">
                {{ statusLabel(k.status) }}
              </span>
            </div>
            <p class="text-xs text-gray-500">📅 {{ formatDate(k.tanggal_kunjungan) }}</p>
            <p class="text-xs text-gray-400 mt-0.5">⏰ {{ k.jam_mulai }} – {{ k.jam_selesai }}</p>
          </div>
        </div>
      </template>

      <div v-else-if="!loading" class="bg-white rounded-2xl border border-gray-100 py-16 text-center text-gray-400 text-sm">
        <span class="text-4xl block mb-2">🏡</span>
        Belum ada pengajuan kunjungan
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
        <h3 class="text-gray-800 font-bold text-base mb-4">Ajukan Kunjungan</h3>
        <form @submit.prevent="submitKunjungan" class="flex flex-col gap-3">
          <div>
            <label class="text-sm font-semibold text-gray-700 mb-1.5 block">Nama Pengunjung</label>
            <input v-model="form.nama_pengunjung" type="text" required
                   placeholder="Nama lengkap pengunjung"
                   class="w-full px-4 py-3.5 rounded-xl border border-gray-200 text-gray-800 text-base bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-500/30" />
          </div>
          <div>
            <label class="text-sm font-semibold text-gray-700 mb-1.5 block">Hubungan</label>
            <select v-model="form.hubungan" required
                    class="w-full px-4 py-3.5 rounded-xl border border-gray-200 text-gray-800 text-base bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
              <option value="" disabled>Pilih hubungan</option>
              <option value="Orang Tua">Orang Tua</option>
              <option value="Wali">Wali</option>
              <option value="Saudara">Saudara</option>
              <option value="Kerabat">Kerabat</option>
            </select>
          </div>
          <div>
            <label class="text-sm font-semibold text-gray-700 mb-1.5 block">Tanggal Kunjungan</label>
            <input v-model="form.tanggal_kunjungan" type="date" required
                   class="w-full px-4 py-3.5 rounded-xl border border-gray-200 text-gray-800 text-base bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-500/30" />
          </div>
          <button type="submit" :disabled="submitting"
                  class="w-full bg-brand-700 text-white font-bold py-4 rounded-2xl active:bg-brand-800 disabled:opacity-60">
            {{ submitting ? 'Mengirim...' : 'Kirim Permohonan' }}
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
import { ChevronLeftIcon, InformationCircleIcon } from '@heroicons/vue/24/outline'

const santriStore  = useSantriStore()
const activeSantri = computed(() => santriStore.active)
const loading        = ref(true)
const kunjunganList  = ref<any[]>([])
const sheet          = ref(false)
const submitting     = ref(false)
const form           = ref({ nama_pengunjung: '', hubungan: '', tanggal_kunjungan: '' })

function statusLabel(s: string) {
  return { menunggu: 'Menunggu', disetujui: 'Disetujui', ditolak: 'Ditolak' }[s] ?? s
}
function formatDate(d: string) {
  return new Date(d).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })
}
function openSheet() {
  form.value = { nama_pengunjung: '', hubungan: '', tanggal_kunjungan: '' }
  sheet.value = true
}

async function submitKunjungan() {
  if (!activeSantri.value) return
  submitting.value = true
  try {
    await waliApi.ajukanKunjungan(activeSantri.value.id, form.value)
    sheet.value = false
    await loadKunjungan()
  } catch { /* handle */ } finally { submitting.value = false }
}

async function loadKunjungan() {
  if (!activeSantri.value) return
  loading.value = true
  try { kunjunganList.value = await waliApi.daftarKunjungan(activeSantri.value.id) as any[] }
  finally { loading.value = false }
}
onMounted(loadKunjungan)
</script>
