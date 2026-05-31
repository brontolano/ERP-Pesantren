<template>
  <div class="min-h-screen bg-[#F5F7FA] pb-nav">

    <!-- Header -->
    <div class="relative bg-gradient-to-br from-brand-700 to-brand-800 px-5 pt-12 pb-20 overflow-hidden">
      <div class="absolute -top-6 -right-6 w-32 h-32 rounded-full bg-white/5" />
      <div class="absolute bottom-2 left-4 w-16 h-16 rounded-full bg-white/5" />
      <div class="relative">
        <p class="text-white/60 text-xs font-medium tracking-wide">Tagihan & Pembayaran</p>
        <h1 class="text-white font-bold text-xl mt-0.5">{{ activeSantri?.nama ?? '—' }}</h1>
      </div>
    </div>

    <!-- Summary Card -->
    <div class="relative z-10 px-4 -mt-12 mb-4">
      <div class="bg-white rounded-2xl shadow-lg shadow-brand-900/10 border border-white p-5">
        <template v-if="loading">
          <div class="skeleton h-3 w-28 rounded mb-3" />
          <div class="skeleton h-8 w-44 rounded" />
        </template>
        <template v-else>
          <p class="text-xs font-medium text-gray-400 mb-1">Total Tunggakan</p>
          <p class="text-2xl font-black"
             :class="tagihan?.total_tunggakan ? 'text-red-600' : 'text-emerald-600'">
            {{ tagihan?.total_tunggakan
              ? 'Rp ' + tagihan.total_tunggakan.toLocaleString('id-ID')
              : 'Semua Lunas ✓' }}
          </p>
          <div v-if="tagihan?.total_tunggakan" class="mt-2 flex items-center gap-1.5">
            <div class="w-2 h-2 rounded-full bg-red-400 animate-pulse" />
            <p class="text-xs text-red-500">Segera lunasi tagihan Anda</p>
          </div>
        </template>
      </div>
    </div>

    <!-- Invoice List -->
    <div class="px-4 flex flex-col gap-3">
      <template v-if="loading">
        <div v-for="i in 3" :key="i" class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
          <div class="skeleton h-4 w-40 rounded mb-2" />
          <div class="skeleton h-3 w-24 rounded mb-3" />
          <div class="skeleton h-10 w-full rounded-xl" />
        </div>
      </template>

      <template v-else-if="tagihan?.invoices?.length">
        <div v-for="inv in tagihan.invoices" :key="inv.id"
             class="bg-white rounded-2xl shadow-sm border border-gray-100/80 overflow-hidden">
          <!-- Status stripe -->
          <div class="h-1"
               :class="{
                 'bg-emerald-400': inv.status === 'paid',
                 'bg-amber-400':   inv.status === 'partial',
                 'bg-red-400':     inv.status === 'unpaid',
               }" />
          <div class="p-4">
            <div class="flex items-start justify-between gap-2 mb-2">
              <p class="font-bold text-gray-800 text-sm leading-tight">{{ inv.keterangan }}</p>
              <span class="text-xs px-2.5 py-1 rounded-full font-semibold flex-shrink-0"
                    :class="{
                      'bg-emerald-50 text-emerald-700': inv.status === 'paid',
                      'bg-amber-50 text-amber-700':     inv.status === 'partial',
                      'bg-red-50 text-red-700':          inv.status === 'unpaid',
                    }">
                {{ statusLabel(inv.status) }}
              </span>
            </div>
            <p class="text-xs text-gray-400 mb-3">Jatuh tempo: {{ formatDate(inv.due_date) }}</p>

            <!-- Items -->
            <div class="bg-gray-50 rounded-xl p-3 mb-3 space-y-1.5">
              <div v-for="item in inv.items" :key="item.id"
                   class="flex justify-between text-xs text-gray-600">
                <span>{{ item.nama_item }}</span>
                <span class="font-medium">Rp {{ item.nominal.toLocaleString('id-ID') }}</span>
              </div>
            </div>

            <div class="flex justify-between items-center">
              <div>
                <p class="text-[10px] text-gray-400">Total</p>
                <p class="text-base font-black text-gray-800">Rp {{ inv.total.toLocaleString('id-ID') }}</p>
              </div>
              <button v-if="inv.status !== 'paid'"
                      @click="openUpload(inv)"
                      class="bg-brand-700 text-white text-sm font-bold px-4 py-2.5 rounded-xl
                             active:bg-brand-800 transition-colors shadow-sm shadow-brand-700/30">
                Upload Bukti
              </button>
              <span v-else class="flex items-center gap-1 text-emerald-600 text-sm font-semibold">
                <CheckCircleIcon class="w-4 h-4" />
                Lunas
              </span>
            </div>
          </div>
        </div>
      </template>

      <div v-else-if="!loading"
           class="text-center py-16 text-gray-400 text-sm bg-white rounded-2xl border border-gray-100">
        <span class="text-4xl block mb-2">🧾</span>
        Tidak ada data tagihan
      </div>
    </div>

    <!-- Backdrop -->
    <Transition name="fade">
      <div v-if="sheet" class="fixed inset-0 bg-black/50 z-[60]" @click="closeSheet" />
    </Transition>

    <!-- Bottom Sheet Upload -->
    <Transition name="sheet-up">
      <div v-if="sheet"
           class="fixed bottom-0 left-0 right-0 z-[70] bg-white rounded-t-3xl shadow-2xl px-5 pt-5"
           :style="{ paddingBottom: 'calc(1.5rem + env(safe-area-inset-bottom, 0px))' }">
        <div class="w-10 h-1 bg-gray-200 rounded-full mx-auto mb-5" />

        <div class="flex items-center gap-3 mb-5">
          <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center">
            <ArrowUpTrayIcon class="w-5 h-5 text-brand-700" />
          </div>
          <div>
            <h3 class="text-gray-800 font-bold text-base">Upload Bukti Transfer</h3>
            <p class="text-xs text-gray-400">{{ selectedInv?.keterangan }}</p>
          </div>
        </div>

        <form @submit.prevent="submitUpload" class="flex flex-col gap-4">
          <div>
            <label class="text-sm font-semibold text-gray-700 mb-1.5 block">Foto Bukti Transfer</label>
            <label class="flex flex-col items-center justify-center w-full h-32
                          border-2 border-dashed rounded-2xl cursor-pointer transition-colors
                          active:bg-gray-50"
                   :class="uploadForm.preview ? 'border-brand-200' : 'border-gray-200 bg-gray-50'">
              <template v-if="!uploadForm.preview">
                <PhotoIcon class="w-8 h-8 text-gray-300 mb-2" />
                <span class="text-sm text-gray-400">Pilih atau ambil foto</span>
              </template>
              <img v-else :src="uploadForm.preview" class="h-full w-full object-cover rounded-2xl" />
              <input type="file" accept="image/*" class="hidden" @change="onFileChange" />
            </label>
          </div>

          <div>
            <label class="text-sm font-semibold text-gray-700 mb-1.5 block">Nominal Transfer</label>
            <div class="relative">
              <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Rp</span>
              <input v-model="uploadForm.nominal" type="number" inputmode="numeric"
                     placeholder="0"
                     class="w-full pl-10 pr-4 py-3.5 rounded-xl border border-gray-200 text-gray-800
                            text-base focus:outline-none focus:ring-2 focus:ring-brand-500/30
                            focus:border-brand-400 bg-gray-50 transition-colors"
                     required />
            </div>
          </div>

          <div>
            <label class="text-sm font-semibold text-gray-700 mb-1.5 block">Tanggal Transfer</label>
            <input v-model="uploadForm.tanggal" type="date"
                   class="w-full px-4 py-3.5 rounded-xl border border-gray-200 text-gray-800
                          text-base focus:outline-none focus:ring-2 focus:ring-brand-500/30
                          focus:border-brand-400 bg-gray-50 transition-colors"
                   required />
          </div>

          <div v-if="uploadError"
               class="bg-red-50 border border-red-100 rounded-xl px-4 py-3
                      text-red-600 text-sm font-medium">
            {{ uploadError }}
          </div>

          <button type="submit" :disabled="uploading"
                  class="w-full bg-brand-700 text-white font-bold py-4 rounded-2xl text-base
                         active:bg-brand-800 transition-colors shadow-lg shadow-brand-700/25
                         disabled:opacity-60 disabled:cursor-not-allowed">
            <span v-if="!uploading">Kirim Bukti Pembayaran</span>
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
import type { Tagihan } from '@/api/wali'
import { ArrowUpTrayIcon, PhotoIcon, CheckCircleIcon } from '@heroicons/vue/24/outline'

const santriStore   = useSantriStore()
const activeSantri  = computed(() => santriStore.active)
const loading       = ref(true)
const tagihan       = ref<Tagihan | null>(null)
const sheet         = ref(false)
const selectedInv   = ref<Tagihan['invoices'][0] | null>(null)
const uploading     = ref(false)
const uploadError   = ref('')
const uploadForm    = ref({ file: null as File | null, preview: '', nominal: '', tanggal: '' })

function statusLabel(s: string) {
  return { paid: 'Lunas', partial: 'Sebagian', unpaid: 'Belum Bayar' }[s] ?? s
}
function formatDate(d: string) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}
function onFileChange(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0]
  if (!file) return
  uploadForm.value.file    = file
  uploadForm.value.preview = URL.createObjectURL(file)
}
function openUpload(inv: Tagihan['invoices'][0]) {
  selectedInv.value = inv
  uploadForm.value  = { file: null, preview: '', nominal: '', tanggal: '' }
  uploadError.value = ''
  sheet.value       = true
}
function closeSheet() { sheet.value = false }

async function submitUpload() {
  if (!uploadForm.value.file) { uploadError.value = 'Pilih foto bukti transfer terlebih dahulu'; return }
  uploading.value = true; uploadError.value = ''
  try {
    const fd = new FormData()
    fd.append('foto', uploadForm.value.file)
    fd.append('nominal_transfer', uploadForm.value.nominal)
    fd.append('tanggal_transfer', uploadForm.value.tanggal)
    await waliApi.uploadBuktiBayar(activeSantri.value!.id, selectedInv.value!.id, fd)
    sheet.value = false
    await loadTagihan()
  } catch (e: any) {
    uploadError.value = e?.response?.data?.message ?? 'Gagal mengirim. Coba lagi.'
  } finally { uploading.value = false }
}

async function loadTagihan() {
  if (!activeSantri.value) return
  loading.value = true
  try { tagihan.value = await waliApi.tagihan(activeSantri.value.id) }
  finally { loading.value = false }
}
onMounted(loadTagihan)
</script>
