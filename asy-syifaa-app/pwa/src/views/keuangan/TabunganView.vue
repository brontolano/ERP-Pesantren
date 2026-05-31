<template>
  <div class="min-h-screen bg-[#F5F7FA] pb-nav">

    <!-- Header -->
    <div class="relative bg-gradient-to-br from-brand-700 to-brand-800 px-5 pt-12 pb-28 overflow-hidden">
      <div class="absolute -top-6 -right-6 w-32 h-32 rounded-full bg-white/5" />
      <div class="absolute bottom-2 left-4 w-16 h-16 rounded-full bg-white/5" />
      <div class="relative flex items-center gap-3">
        <button @click="$router.back()" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center active:bg-white/20">
          <ChevronLeftIcon class="w-5 h-5 text-white" />
        </button>
        <div>
          <p class="text-white/60 text-xs font-medium">Tabungan Santri</p>
          <h1 class="text-white font-bold text-lg">{{ activeSantri?.nama ?? '—' }}</h1>
        </div>
      </div>
    </div>

    <!-- Virtual Card -->
    <div class="relative z-10 px-4 -mt-20 mb-5">
      <div class="relative bg-gradient-to-br from-brand-600 via-brand-700 to-brand-900 rounded-3xl p-6 shadow-2xl shadow-brand-900/30 overflow-hidden">
        <!-- Card decoration -->
        <div class="absolute -top-8 -right-8 w-36 h-36 rounded-full bg-white/5" />
        <div class="absolute bottom-0 right-8 w-24 h-24 rounded-full bg-white/5" />
        <div class="absolute top-4 right-4">
          <div class="flex gap-1">
            <div class="w-6 h-6 rounded-full bg-amber-400/80" />
            <div class="w-6 h-6 rounded-full bg-amber-300/50 -ml-3" />
          </div>
        </div>

        <p class="text-white/60 text-xs font-medium mb-1">Saldo Tabungan</p>
        <template v-if="loading">
          <div class="skeleton h-8 w-44 rounded mb-4 bg-white/20" />
        </template>
        <template v-else>
          <p class="text-white text-3xl font-black mb-4">
            Rp {{ (tabungan?.saldo ?? 0).toLocaleString('id-ID') }}
          </p>
        </template>

        <div class="h-px bg-white/10 mb-4" />

        <div class="flex justify-between items-center">
          <div>
            <p class="text-white/50 text-[10px] mb-0.5">Nama Santri</p>
            <p class="text-white text-sm font-bold">{{ activeSantri?.nama ?? '—' }}</p>
          </div>
          <div class="text-right">
            <p class="text-white/50 text-[10px] mb-0.5">NIS</p>
            <p class="text-white/80 text-sm font-mono">{{ activeSantri?.nis ?? '—' }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Tagihan Prioritas (gating SPP/Ujian) -->
    <div v-if="!loading && tabungan?.topup_jajan_locked" class="px-4 mb-4">
      <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4">
        <div class="flex items-start gap-3">
          <div class="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
            <LockClosedIcon class="w-4 h-4 text-amber-600" />
          </div>
          <div class="flex-1">
            <p class="text-amber-800 font-bold text-sm">Setor saldo terkunci</p>
            <p class="text-amber-700 text-xs mt-0.5 leading-relaxed">
              Lunasi tagihan prioritas (SPP &amp; Ujian) sebesar
              <span class="font-bold">Rp {{ (tabungan?.tunggakan_prioritas ?? 0).toLocaleString('id-ID') }}</span>
              terlebih dahulu. Pengecualian dapat diatur oleh staff pesantren.
            </p>
            <button @click="$router.push({ name: 'tagihan' })"
                    class="mt-2.5 text-amber-800 text-xs font-bold px-3 py-1.5 rounded-lg bg-amber-100 active:bg-amber-200">
              Lihat Tagihan
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Action: Setor Saldo -->
    <div class="px-4 mb-4">
      <button @click="openTopupSheet" :disabled="tabungan?.topup_jajan_locked"
              class="w-full flex items-center justify-center gap-2 py-3.5 rounded-2xl
                     bg-brand-700 text-white font-bold shadow-sm shadow-brand-900/20
                     active:bg-brand-800 active:scale-[0.99] transition-transform
                     disabled:opacity-50 disabled:active:scale-100">
        <PlusCircleIcon class="w-5 h-5" />
        Setor Saldo
      </button>
    </div>

    <!-- Limit Jajan Harian -->
    <div class="px-4 mb-4">
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100/80 p-5">
        <div class="flex items-center justify-between mb-3">
          <div>
            <p class="text-gray-800 font-bold text-sm">Limit Jajan Harian</p>
            <p class="text-gray-400 text-xs mt-0.5">Reset setiap tengah malam</p>
          </div>
          <button @click="openLimitSheet"
                  class="text-brand-700 text-xs font-bold px-3 py-1.5 rounded-lg bg-brand-50 active:bg-brand-100">
            Atur
          </button>
        </div>

        <template v-if="loading">
          <div class="skeleton h-3 w-full rounded mb-2" />
          <div class="skeleton h-2 w-28 rounded" />
        </template>
        <template v-else>
          <div class="h-2 bg-gray-100 rounded-full overflow-hidden mb-2">
            <div class="h-full rounded-full transition-all duration-500"
                 :class="progressColor"
                 :style="{ width: limitProgress + '%' }" />
          </div>
          <div class="flex justify-between text-xs text-gray-400">
            <span>Terpakai: Rp {{ (tabungan?.transaksi_hari_ini ?? 0).toLocaleString('id-ID') }}</span>
            <span>Limit: Rp {{ (tabungan?.limit_harian ?? 0).toLocaleString('id-ID') }}</span>
          </div>
        </template>
      </div>
    </div>

    <!-- Stats Row -->
    <div class="px-4 mb-4 grid grid-cols-2 gap-3">
      <div class="bg-white rounded-2xl p-4 border border-gray-100/80 shadow-sm">
        <p class="text-gray-400 text-xs mb-1">Sisa Limit Hari Ini</p>
        <p class="text-emerald-600 font-black text-base">
          Rp {{ (tabungan?.saldo_dapat_dipakai ?? 0).toLocaleString('id-ID') }}
        </p>
      </div>
      <div class="bg-white rounded-2xl p-4 border border-gray-100/80 shadow-sm">
        <p class="text-gray-400 text-xs mb-1">Tunggakan Prioritas</p>
        <p class="font-black text-base"
           :class="(tabungan?.tunggakan_prioritas ?? 0) > 0 ? 'text-red-500' : 'text-emerald-600'">
          Rp {{ (tabungan?.tunggakan_prioritas ?? 0).toLocaleString('id-ID') }}
        </p>
      </div>
    </div>

    <!-- Freeze Toggle -->
    <div class="px-4 mb-4">
      <div class="bg-white rounded-2xl p-4 border border-gray-100/80 shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
            <LockClosedIcon class="w-4 h-4 text-blue-600" />
          </div>
          <div>
            <p class="text-gray-800 font-semibold text-sm">Bekukan Akun</p>
            <p class="text-gray-400 text-xs">Nonaktifkan sementara transaksi</p>
          </div>
        </div>
        <button @click="toggleFreeze"
                class="w-12 h-6 rounded-full transition-colors duration-200 flex items-center px-0.5"
                :class="tabungan?.is_frozen ? 'bg-blue-600' : 'bg-gray-200'">
          <div class="w-5 h-5 rounded-full bg-white shadow transition-transform duration-200"
               :class="tabungan?.is_frozen ? 'translate-x-6' : 'translate-x-0'" />
        </button>
      </div>
    </div>

    <!-- Backdrop -->
    <Transition name="fade">
      <div v-if="sheet" class="fixed inset-0 bg-black/50 z-[60]" @click="sheet = false" />
    </Transition>

    <!-- Limit Sheet -->
    <Transition name="sheet-up">
      <div v-if="sheet"
           class="fixed bottom-0 left-0 right-0 z-[70] bg-white rounded-t-3xl shadow-2xl px-5 pt-5"
           :style="{ paddingBottom: 'calc(1.5rem + env(safe-area-inset-bottom, 0px))' }">
        <div class="w-10 h-1 bg-gray-200 rounded-full mx-auto mb-5" />
        <h3 class="text-gray-800 font-bold text-base mb-1">Atur Limit Jajan Harian</h3>
        <p class="text-gray-400 text-sm mb-5">Masukkan nominal limit pengeluaran per hari</p>

        <div class="relative mb-4">
          <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-medium">Rp</span>
          <input v-model="newLimit" type="number" inputmode="numeric"
                 placeholder="50.000"
                 class="w-full pl-10 pr-4 py-3.5 rounded-xl border border-gray-200 text-gray-800
                        text-base focus:outline-none focus:ring-2 focus:ring-brand-500/30
                        focus:border-brand-400 bg-gray-50" />
        </div>

        <div class="flex gap-3">
          <button @click="sheet = false"
                  class="flex-1 py-3.5 rounded-2xl border border-gray-200 text-gray-700 font-semibold">
            Batal
          </button>
          <button @click="saveLimit" :disabled="savingLimit"
                  class="flex-1 py-3.5 rounded-2xl bg-brand-700 text-white font-bold
                         disabled:opacity-60 active:bg-brand-800">
            {{ savingLimit ? 'Menyimpan...' : 'Simpan' }}
          </button>
        </div>
      </div>
    </Transition>

    <!-- Topup Backdrop -->
    <Transition name="fade">
      <div v-if="topupSheet" class="fixed inset-0 bg-black/50 z-[60]" @click="topupSheet = false" />
    </Transition>

    <!-- Topup Sheet -->
    <Transition name="sheet-up">
      <div v-if="topupSheet"
           class="fixed bottom-0 left-0 right-0 z-[70] bg-white rounded-t-3xl shadow-2xl px-5 pt-5
                  max-h-[90vh] overflow-y-auto"
           :style="{ paddingBottom: 'calc(1.5rem + env(safe-area-inset-bottom, 0px))' }">
        <div class="w-10 h-1 bg-gray-200 rounded-full mx-auto mb-5" />
        <h3 class="text-gray-800 font-bold text-base mb-1">Setor Saldo Tabungan</h3>
        <p class="text-gray-400 text-sm mb-4">Transfer ke rekening pondok, lalu unggah bukti transfer.</p>

        <!-- Metode pembayaran -->
        <template v-if="methods.length">
          <p class="text-gray-700 font-semibold text-sm mb-2">Tujuan Transfer</p>
          <div class="space-y-2 mb-5">
            <div v-for="m in methods" :key="m.id"
                 class="rounded-2xl border border-gray-100 bg-gray-50 p-3.5">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-gray-800 font-bold text-sm">{{ m.bank_name ?? m.name }}</p>
                  <p v-if="m.account_number" class="text-gray-600 text-sm font-mono mt-0.5">{{ m.account_number }}</p>
                  <p v-if="m.account_holder" class="text-gray-400 text-xs mt-0.5">a.n. {{ m.account_holder }}</p>
                </div>
                <button v-if="m.account_number" @click="copyText(m.account_number)"
                        class="text-brand-700 text-xs font-bold px-3 py-1.5 rounded-lg bg-brand-50 active:bg-brand-100">
                  Salin
                </button>
              </div>
              <img v-if="m.qris_image_url" :src="m.qris_image_url"
                   class="w-40 h-40 object-contain mx-auto mt-3 rounded-xl bg-white p-2" alt="QRIS" />
            </div>
          </div>
        </template>

        <!-- Form -->
        <div class="space-y-3 mb-5">
          <div>
            <label class="text-gray-700 text-xs font-semibold mb-1.5 block">Nominal Setoran</label>
            <div class="relative">
              <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-medium">Rp</span>
              <input v-model="topupForm.nominal" type="number" inputmode="numeric" placeholder="100.000"
                     class="w-full pl-10 pr-4 py-3.5 rounded-xl border border-gray-200 text-gray-800 text-base
                            focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-400 bg-gray-50" />
            </div>
          </div>
          <div>
            <label class="text-gray-700 text-xs font-semibold mb-1.5 block">Tanggal Transfer</label>
            <input v-model="topupForm.tanggal" type="date"
                   class="w-full px-4 py-3.5 rounded-xl border border-gray-200 text-gray-800 text-base
                          focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-400 bg-gray-50" />
          </div>
          <div>
            <label class="text-gray-700 text-xs font-semibold mb-1.5 block">Nama Pengirim (opsional)</label>
            <input v-model="topupForm.namaPengirim" type="text" placeholder="Nama di rekening pengirim"
                   class="w-full px-4 py-3.5 rounded-xl border border-gray-200 text-gray-800 text-base
                          focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-400 bg-gray-50" />
          </div>
          <div>
            <label class="text-gray-700 text-xs font-semibold mb-1.5 block">Bukti Transfer</label>
            <label class="flex flex-col items-center justify-center gap-2 py-6 rounded-xl border-2 border-dashed
                          border-gray-200 bg-gray-50 cursor-pointer active:bg-gray-100">
              <template v-if="topupForm.fotoPreview">
                <img :src="topupForm.fotoPreview" class="w-28 h-28 object-cover rounded-lg" alt="Preview" />
                <span class="text-brand-700 text-xs font-semibold">Ganti foto</span>
              </template>
              <template v-else>
                <PhotoIcon class="w-8 h-8 text-gray-300" />
                <span class="text-gray-400 text-xs">Ketuk untuk unggah foto/screenshot</span>
              </template>
              <input type="file" accept="image/*" class="hidden" @change="onFotoChange" />
            </label>
          </div>
        </div>

        <p v-if="topupError" class="text-red-500 text-xs mb-3">{{ topupError }}</p>

        <div class="flex gap-3">
          <button @click="topupSheet = false"
                  class="flex-1 py-3.5 rounded-2xl border border-gray-200 text-gray-700 font-semibold">
            Batal
          </button>
          <button @click="submitTopup" :disabled="submittingTopup"
                  class="flex-1 py-3.5 rounded-2xl bg-brand-700 text-white font-bold
                         disabled:opacity-60 active:bg-brand-800">
            {{ submittingTopup ? 'Mengirim...' : 'Kirim Bukti' }}
          </button>
        </div>
      </div>
    </Transition>

  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useSantriStore } from '@/stores/santri'
import { waliApi, type PaymentMethod } from '@/api/wali'
import { ChevronLeftIcon, LockClosedIcon, PlusCircleIcon, PhotoIcon } from '@heroicons/vue/24/outline'

const santriStore  = useSantriStore()
const activeSantri = computed(() => santriStore.active)
const loading      = ref(true)
const tabungan     = ref<any>(null)
const sheet        = ref(false)
const newLimit     = ref('')
const savingLimit  = ref(false)

// ── Topup state ──────────────────────────────────────────────
const topupSheet      = ref(false)
const methods         = ref<PaymentMethod[]>([])
const submittingTopup = ref(false)
const topupError      = ref('')
const topupForm = ref<{ nominal: string; tanggal: string; namaPengirim: string; foto: File | null; fotoPreview: string }>({
  nominal: '',
  tanggal: new Date().toISOString().slice(0, 10),
  namaPengirim: '',
  foto: null,
  fotoPreview: '',
})

const limitProgress = computed(() => {
  if (!tabungan.value?.limit_harian) return 0
  return Math.min(100, ((tabungan.value.transaksi_hari_ini ?? 0) / tabungan.value.limit_harian) * 100)
})

const progressColor = computed(() => {
  const p = limitProgress.value
  if (p >= 90) return 'bg-red-500'
  if (p >= 60) return 'bg-amber-400'
  return 'bg-emerald-500'
})

function openLimitSheet() {
  newLimit.value = tabungan.value?.limit_harian?.toString() ?? ''
  sheet.value = true
}

async function saveLimit() {
  if (!activeSantri.value || !newLimit.value) return
  savingLimit.value = true
  try {
    await waliApi.setLimitJajan(activeSantri.value.id, parseInt(newLimit.value))
    if (tabungan.value) tabungan.value.limit_harian = parseInt(newLimit.value)
    sheet.value = false
  } catch { /* handle */ } finally { savingLimit.value = false }
}

async function toggleFreeze() {
  if (!activeSantri.value || !tabungan.value) return
  const newState = !tabungan.value.is_frozen
  tabungan.value.is_frozen = newState
  try { await waliApi.freezeTabungan(activeSantri.value.id, newState) }
  catch { tabungan.value.is_frozen = !newState }
}

async function openTopupSheet() {
  topupError.value = ''
  topupSheet.value = true
  if (!methods.value.length && activeSantri.value) {
    try { methods.value = await waliApi.paymentMethods(activeSantri.value.id) }
    catch { /* metode opsional */ }
  }
}

function onFotoChange(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0]
  if (!file) return
  topupForm.value.foto = file
  topupForm.value.fotoPreview = URL.createObjectURL(file)
}

async function copyText(text: string) {
  try { await navigator.clipboard.writeText(text) } catch { /* noop */ }
}

async function submitTopup() {
  if (!activeSantri.value) return
  topupError.value = ''
  const nominal = parseInt(topupForm.value.nominal || '0')
  if (!nominal || nominal < 10000) { topupError.value = 'Nominal minimal Rp 10.000'; return }
  if (!topupForm.value.foto) { topupError.value = 'Unggah bukti transfer terlebih dahulu'; return }

  submittingTopup.value = true
  try {
    const fd = new FormData()
    fd.append('foto', topupForm.value.foto)
    fd.append('nominal_transfer', String(nominal))
    fd.append('tanggal_transfer', topupForm.value.tanggal)
    if (topupForm.value.namaPengirim) fd.append('nama_pengirim', topupForm.value.namaPengirim)
    await waliApi.topupTabungan(activeSantri.value.id, fd)
    topupSheet.value = false
    topupForm.value = { nominal: '', tanggal: new Date().toISOString().slice(0, 10), namaPengirim: '', foto: null, fotoPreview: '' }
  } catch (err: any) {
    topupError.value = err?.response?.data?.message ?? 'Gagal mengirim bukti. Coba lagi.'
  } finally {
    submittingTopup.value = false
  }
}

onMounted(async () => {
  if (!activeSantri.value) return
  try { tabungan.value = await waliApi.tabungan(activeSantri.value.id) }
  finally { loading.value = false }
})
</script>
