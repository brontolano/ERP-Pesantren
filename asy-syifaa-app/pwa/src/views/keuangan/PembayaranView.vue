<template>
  <div class="min-h-screen bg-[#F5F7FA] pb-nav">

    <!-- Header -->
    <div class="relative bg-gradient-to-br from-brand-700 to-brand-800 px-5 pt-12 pb-20 overflow-hidden">
      <div class="absolute -top-6 -right-6 w-32 h-32 rounded-full bg-white/5" />
      <div class="absolute bottom-2 left-4 w-16 h-16 rounded-full bg-white/5" />
      <div class="relative flex items-center gap-3">
        <button @click="$router.back()" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center active:bg-white/20">
          <ChevronLeftIcon class="w-5 h-5 text-white" />
        </button>
        <div>
          <p class="text-white/60 text-xs font-medium">Metode Pembayaran</p>
          <h1 class="text-white font-bold text-lg">Bayar Tagihan</h1>
        </div>
      </div>
    </div>

    <div class="relative z-10 px-4 -mt-10 space-y-4">

      <!-- Loading skeleton -->
      <template v-if="loading">
        <div v-for="i in 3" :key="i" class="bg-white rounded-2xl p-4 border border-gray-100">
          <div class="skeleton h-4 w-32 rounded mb-3" />
          <div class="skeleton h-12 w-full rounded" />
        </div>
      </template>

      <template v-else>
        <!-- QRIS -->
        <div v-for="qr in qrisMethods" :key="qr.id"
             class="bg-white rounded-2xl shadow-sm border border-gray-100/80 overflow-hidden">
          <div class="flex items-center gap-3 p-4 border-b border-gray-50">
            <div class="w-9 h-9 rounded-xl bg-violet-50 flex items-center justify-center">
              <QrCodeIcon class="w-5 h-5 text-violet-600" />
            </div>
            <div>
              <p class="text-gray-800 font-bold text-sm">{{ qr.name }}</p>
              <p class="text-gray-400 text-xs">Scan & bayar dari semua e-wallet / m-banking</p>
            </div>
            <span class="ml-auto text-[10px] bg-emerald-100 text-emerald-700 font-bold px-2 py-1 rounded-full">AKTIF</span>
          </div>
          <div class="p-5 flex flex-col items-center">
            <div v-if="qr.qris_image_url"
                 class="w-56 h-56 bg-white rounded-2xl border border-gray-100 flex items-center justify-center mb-3 overflow-hidden">
              <img :src="qr.qris_image_url" alt="QRIS" class="w-full h-full object-contain" />
            </div>
            <div v-else
                 class="w-48 h-48 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 flex items-center justify-center mb-3">
              <div class="text-center text-gray-300">
                <QrCodeIcon class="w-16 h-16 mx-auto mb-2" />
                <p class="text-xs">QRIS belum tersedia</p>
              </div>
            </div>
            <p v-if="qr.account_holder" class="text-sm font-bold text-gray-700">{{ qr.account_holder }}</p>
            <p v-if="qr.instructions" class="text-xs text-gray-400 text-center mt-1">{{ qr.instructions }}</p>
          </div>
        </div>

        <!-- Virtual Account / Bank -->
        <div v-if="bankMethods.length" class="bg-white rounded-2xl shadow-sm border border-gray-100/80 overflow-hidden">
          <div class="flex items-center gap-3 p-4 border-b border-gray-50">
            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
              <BuildingLibraryIcon class="w-5 h-5 text-blue-600" />
            </div>
            <div>
              <p class="text-gray-800 font-bold text-sm">Transfer Bank</p>
              <p class="text-gray-400 text-xs">Transfer ke rekening pesantren</p>
            </div>
          </div>
          <div class="p-4 space-y-3">
            <div v-for="bank in bankMethods" :key="bank.id"
                 class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
              <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-lg bg-white shadow-sm flex items-center justify-center flex-shrink-0">
                  <BuildingLibraryIcon class="w-5 h-5 text-blue-500" />
                </div>
                <div class="min-w-0">
                  <p class="text-gray-700 font-semibold text-sm truncate">{{ bank.bank_name || bank.name }}</p>
                  <p class="text-gray-500 text-xs font-mono truncate">{{ bank.account_number || '-' }}</p>
                  <p v-if="bank.account_holder" class="text-gray-400 text-[11px] truncate">a.n. {{ bank.account_holder }}</p>
                </div>
              </div>
              <button v-if="bank.account_number" @click="copyVA(bank.account_number)"
                      class="p-2 rounded-lg bg-white shadow-sm active:bg-gray-100 flex-shrink-0">
                <ClipboardDocumentIcon class="w-4 h-4 text-gray-500" />
              </button>
            </div>
          </div>
        </div>

        <!-- E-Wallet -->
        <div v-if="ewalletMethods.length" class="bg-white rounded-2xl shadow-sm border border-gray-100/80 overflow-hidden">
          <div class="flex items-center gap-3 p-4 border-b border-gray-50">
            <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center">
              <DevicePhoneMobileIcon class="w-5 h-5 text-amber-600" />
            </div>
            <div>
              <p class="text-gray-800 font-bold text-sm">E-Wallet</p>
              <p class="text-gray-400 text-xs">Transfer ke nomor e-wallet pesantren</p>
            </div>
          </div>
          <div class="p-4 space-y-3">
            <div v-for="ew in ewalletMethods" :key="ew.id"
                 class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
              <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-lg bg-white shadow-sm flex items-center justify-center flex-shrink-0">
                  <DevicePhoneMobileIcon class="w-5 h-5 text-amber-500" />
                </div>
                <div class="min-w-0">
                  <p class="text-gray-700 font-semibold text-sm truncate">{{ ew.name }}</p>
                  <p class="text-gray-500 text-xs font-mono truncate">{{ ew.account_number || '-' }}</p>
                  <p v-if="ew.account_holder" class="text-gray-400 text-[11px] truncate">a.n. {{ ew.account_holder }}</p>
                </div>
              </div>
              <button v-if="ew.account_number" @click="copyVA(ew.account_number)"
                      class="p-2 rounded-lg bg-white shadow-sm active:bg-gray-100 flex-shrink-0">
                <ClipboardDocumentIcon class="w-4 h-4 text-gray-500" />
              </button>
            </div>
          </div>
        </div>

        <!-- Empty -->
        <div v-if="!methods.length"
             class="bg-white rounded-2xl border border-gray-100 py-16 text-center text-gray-400 text-sm">
          <span class="text-4xl block mb-2">💳</span>
          Metode pembayaran belum tersedia.<br>Hubungi admin pesantren.
        </div>

        <!-- Info Box -->
        <div class="bg-amber-50 rounded-2xl border border-amber-100 p-4 flex gap-3">
          <InformationCircleIcon class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" />
          <div>
            <p class="text-amber-800 font-semibold text-sm mb-1">Cara Pembayaran</p>
            <ol class="text-amber-700 text-xs space-y-1 list-decimal list-inside leading-relaxed">
              <li>Pilih metode pembayaran di atas</li>
              <li>Transfer / scan QRIS sesuai nominal tagihan</li>
              <li>Upload bukti transfer di menu Tagihan</li>
              <li>Admin akan memverifikasi dalam 1x24 jam</li>
            </ol>
          </div>
        </div>
      </template>

    </div>

    <!-- Copy Toast -->
    <Transition name="fade">
      <div v-if="copied"
           class="fixed bottom-24 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-sm px-4 py-2.5 rounded-full shadow-lg z-[80]">
        ✓ Nomor disalin
      </div>
    </Transition>

  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useSantriStore } from '@/stores/santri'
import { waliApi, type PaymentMethod } from '@/api/wali'
import {
  ChevronLeftIcon, QrCodeIcon, BuildingLibraryIcon,
  DevicePhoneMobileIcon, ClipboardDocumentIcon, InformationCircleIcon,
} from '@heroicons/vue/24/outline'

const santriStore  = useSantriStore()
const activeSantri = computed(() => santriStore.active)
const loading  = ref(true)
const copied   = ref(false)
const methods  = ref<PaymentMethod[]>([])

const qrisMethods    = computed(() => methods.value.filter(m => m.type === 'qris'))
const bankMethods    = computed(() => methods.value.filter(m => m.type === 'bank'))
const ewalletMethods = computed(() => methods.value.filter(m => m.type === 'ewallet'))

function copyVA(nomor: string) {
  navigator.clipboard.writeText(nomor).catch(() => {})
  copied.value = true
  setTimeout(() => { copied.value = false }, 2000)
}

onMounted(async () => {
  if (!activeSantri.value) { loading.value = false; return }
  try { methods.value = await waliApi.paymentMethods(activeSantri.value.id) }
  catch { /* keep empty */ } finally { loading.value = false }
})
</script>
