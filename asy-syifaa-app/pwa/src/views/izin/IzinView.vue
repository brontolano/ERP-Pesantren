<template>
  <div class="min-h-screen bg-[#F5F7FA] pb-nav">

    <!-- Header -->
    <div class="relative bg-gradient-to-br from-brand-700 to-brand-800 px-5 pt-12 pb-20 overflow-hidden">
      <div class="absolute -top-6 -right-6 w-32 h-32 rounded-full bg-white/5" />
      <div class="absolute bottom-2 left-4 w-16 h-16 rounded-full bg-white/5" />
      <div class="relative flex items-center justify-between">
        <div>
          <p class="text-white/60 text-xs font-medium tracking-wide">Perizinan</p>
          <h1 class="text-white font-bold text-xl mt-0.5">{{ activeSantri?.nama ?? '—' }}</h1>
        </div>
        <button @click="openSheet"
                class="bg-white text-brand-700 text-sm font-bold px-4 py-2.5 rounded-xl
                       active:bg-gray-50 transition-colors shadow-sm">
          + Ajukan
        </button>
      </div>
    </div>

    <!-- List -->
    <div class="relative z-10 px-4 -mt-12 flex flex-col gap-3">
      <template v-if="loading">
        <div v-for="i in 3" :key="i" class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
          <div class="skeleton h-4 w-36 rounded mb-2" />
          <div class="skeleton h-3 w-28 rounded mb-1" />
          <div class="skeleton h-3 w-48 rounded" />
        </div>
      </template>

      <template v-else-if="izinList.length">
        <div v-for="izin in izinList" :key="izin.id"
             class="bg-white rounded-2xl shadow-sm border border-gray-100/80 overflow-hidden">
          <!-- Status stripe -->
          <div class="h-1"
               :class="{
                 'bg-amber-400':  izin.status === 'menunggu',
                 'bg-emerald-400': izin.status === 'disetujui',
                 'bg-red-400':    izin.status === 'ditolak',
                 'bg-gray-200':   izin.status === 'selesai',
               }" />
          <div class="p-4">
            <div class="flex items-start justify-between gap-2 mb-2">
              <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                     :class="{
                       'bg-amber-50':  izin.status === 'menunggu',
                       'bg-emerald-50': izin.status === 'disetujui',
                       'bg-red-50':    izin.status === 'ditolak',
                       'bg-gray-50':   izin.status === 'selesai',
                     }">
                  <ArrowRightStartOnRectangleIcon
                    class="w-4 h-4"
                    :class="{
                      'text-amber-500':   izin.status === 'menunggu',
                      'text-emerald-600': izin.status === 'disetujui',
                      'text-red-500':     izin.status === 'ditolak',
                      'text-gray-400':    izin.status === 'selesai',
                    }" />
                </div>
                <p class="font-bold text-gray-800 text-sm">{{ jenisLabel(izin.jenis_izin) }}</p>
              </div>
              <span class="text-xs px-2.5 py-1 rounded-full font-semibold flex-shrink-0"
                    :class="{
                      'bg-amber-50 text-amber-700':   izin.status === 'menunggu',
                      'bg-emerald-50 text-emerald-700': izin.status === 'disetujui',
                      'bg-red-50 text-red-700':         izin.status === 'ditolak',
                      'bg-gray-100 text-gray-500':      izin.status === 'selesai',
                    }">
                {{ statusLabel(izin.status) }}
              </span>
            </div>
            <p class="text-xs text-gray-500 mb-1">
              📅 {{ formatDate(izin.tanggal_mulai) }}
              {{ izin.tanggal_selesai ? ' — ' + formatDate(izin.tanggal_selesai) : '' }}
            </p>
            <p class="text-sm text-gray-600 leading-relaxed">{{ izin.alasan }}</p>
          </div>
        </div>
      </template>

      <div v-else-if="!loading"
           class="text-center py-16 text-gray-400 text-sm bg-white rounded-2xl border border-gray-100">
        <span class="text-4xl block mb-2">📋</span>
        Belum ada pengajuan izin
      </div>
    </div>

    <!-- Backdrop -->
    <Transition name="fade">
      <div v-if="sheet" class="fixed inset-0 bg-black/50 z-[60]" @click="closeSheet" />
    </Transition>

    <!-- Bottom Sheet Form -->
    <Transition name="sheet-up">
      <div v-if="sheet"
           class="fixed bottom-0 left-0 right-0 z-[70] bg-white rounded-t-3xl shadow-2xl px-5 pt-5"
           :style="{ paddingBottom: 'calc(1.5rem + env(safe-area-inset-bottom, 0px))' }">
        <div class="w-10 h-1 bg-gray-200 rounded-full mx-auto mb-5" />

        <div class="flex items-center gap-3 mb-5">
          <div class="w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center">
            <ArrowRightStartOnRectangleIcon class="w-5 h-5 text-sky-500" />
          </div>
          <div>
            <h3 class="text-gray-800 font-bold text-base">Ajukan Izin</h3>
            <p class="text-xs text-gray-400">Isi form di bawah ini</p>
          </div>
        </div>

        <form @submit.prevent="submitIzin" class="flex flex-col gap-4">
          <div>
            <label class="text-sm font-semibold text-gray-700 mb-1.5 block">Jenis Izin</label>
            <select v-model="form.jenis_izin"
                    class="w-full px-4 py-3.5 rounded-xl border border-gray-200 text-gray-800
                           text-base focus:outline-none focus:ring-2 focus:ring-brand-500/30
                           focus:border-brand-400 bg-gray-50 transition-colors"
                    required>
              <option value="" disabled>Pilih jenis izin</option>
              <option value="pulang">🏠 Izin Pulang</option>
              <option value="keluar_area">🚶 Keluar Area</option>
              <option value="kegiatan_luar">🎪 Kegiatan Luar</option>
              <option value="sakit">🏥 Sakit</option>
              <option value="lainnya">📝 Lainnya</option>
            </select>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="text-sm font-semibold text-gray-700 mb-1.5 block">Mulai</label>
              <input v-model="form.tanggal_mulai" type="date"
                     class="w-full px-3 py-3.5 rounded-xl border border-gray-200 text-gray-800
                            text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30
                            focus:border-brand-400 bg-gray-50 transition-colors"
                     required />
            </div>
            <div>
              <label class="text-sm font-semibold text-gray-700 mb-1.5 block">Selesai</label>
              <input v-model="form.tanggal_selesai" type="date"
                     class="w-full px-3 py-3.5 rounded-xl border border-gray-200 text-gray-800
                            text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30
                            focus:border-brand-400 bg-gray-50 transition-colors"
                     required />
            </div>
          </div>

          <div>
            <label class="text-sm font-semibold text-gray-700 mb-1.5 block">Alasan</label>
            <textarea v-model="form.alasan" rows="3"
                      placeholder="Jelaskan alasan pengajuan izin..."
                      class="w-full px-4 py-3.5 rounded-xl border border-gray-200 text-gray-800
                             text-base focus:outline-none focus:ring-2 focus:ring-brand-500/30
                             focus:border-brand-400 bg-gray-50 transition-colors resize-none leading-relaxed"
                      required />
          </div>

          <div v-if="formError"
               class="bg-red-50 border border-red-100 rounded-xl px-4 py-3 text-red-600 text-sm font-medium">
            {{ formError }}
          </div>

          <button type="submit" :disabled="submitting"
                  class="w-full bg-brand-700 text-white font-bold py-4 rounded-2xl text-base
                         active:bg-brand-800 transition-colors shadow-lg shadow-brand-700/25
                         disabled:opacity-60 disabled:cursor-not-allowed">
            <span v-if="!submitting">Kirim Pengajuan Izin</span>
            <span v-else class="flex items-center justify-center gap-2">
              <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
              </svg>
              Mengirim...
            </span>
          </button>
        </form>
      </div>
    </Transition>

  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useSantriStore } from '@/stores/santri'
import { waliApi } from '@/api/wali'
import type { Izin } from '@/api/wali'
import { ArrowRightStartOnRectangleIcon } from '@heroicons/vue/24/outline'

const santriStore  = useSantriStore()
const activeSantri = computed(() => santriStore.active)
const loading      = ref(true)
const izinList     = ref<Izin[]>([])
const sheet        = ref(false)
const submitting   = ref(false)
const formError    = ref('')
const form = ref({ jenis_izin: '', tanggal_mulai: '', tanggal_selesai: '', alasan: '' })

function jenisLabel(j: string) {
  const map: Record<string, string> = {
    pulang: 'Izin Pulang', keluar_area: 'Keluar Area',
    kegiatan_luar: 'Kegiatan Luar', sakit: 'Sakit', lainnya: 'Lainnya',
  }
  return map[j] ?? j
}
function statusLabel(s: string) {
  return { menunggu: 'Menunggu', disetujui: 'Disetujui', ditolak: 'Ditolak', selesai: 'Selesai' }[s] ?? s
}
function formatDate(d: string) {
  if (!d) return ''
  return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

function openSheet() {
  form.value = { jenis_izin: '', tanggal_mulai: '', tanggal_selesai: '', alasan: '' }
  formError.value = ''
  sheet.value = true
}
function closeSheet() { sheet.value = false }

async function submitIzin() {
  submitting.value = true; formError.value = ''
  try {
    await waliApi.ajukanIzin(activeSantri.value!.id, form.value)
    sheet.value = false
    await loadIzin()
  } catch (e: any) {
    formError.value = e?.response?.data?.message ?? 'Gagal mengajukan izin. Coba lagi.'
  } finally { submitting.value = false }
}

async function loadIzin() {
  if (!activeSantri.value) return
  loading.value = true
  try { izinList.value = await waliApi.daftarIzin(activeSantri.value.id) }
  finally { loading.value = false }
}
onMounted(loadIzin)
</script>
