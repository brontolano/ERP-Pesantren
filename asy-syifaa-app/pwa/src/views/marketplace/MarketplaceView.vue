<template>
  <div class="min-h-screen bg-[#F5F7FA] pb-nav">

    <!-- Header -->
    <div class="relative bg-gradient-to-br from-gray-500 to-gray-700 px-5 pt-12 pb-28 overflow-hidden">
      <div class="absolute -top-6 -right-6 w-32 h-32 rounded-full bg-white/5" />
      <div class="absolute bottom-2 left-4 w-16 h-16 rounded-full bg-white/5" />
      <div class="relative flex items-center gap-3">
        <button @click="$router.back()" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center active:bg-white/20">
          <ChevronLeftIcon class="w-5 h-5 text-white" />
        </button>
        <div>
          <p class="text-white/60 text-xs font-medium">Fitur Baru</p>
          <h1 class="text-white font-bold text-lg">Marketplace Pesantren</h1>
        </div>
      </div>
    </div>

    <!-- Coming Soon Card -->
    <div class="relative z-10 px-4 -mt-16 mb-5">
      <div class="bg-white rounded-3xl shadow-xl border border-white overflow-hidden">
        <!-- Ilustrasi -->
        <div class="bg-gradient-to-br from-gray-50 to-gray-100 flex flex-col items-center justify-center py-10 px-6">
          <div class="relative mb-4">
            <div class="w-28 h-28 rounded-3xl bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
              <ShoppingBagIcon class="w-14 h-14 text-gray-400" />
            </div>
            <!-- Badge -->
            <div class="absolute -top-2 -right-2 bg-amber-400 text-white text-[10px] font-black px-2.5 py-1 rounded-full shadow">
              SEGERA
            </div>
          </div>
          <h2 class="text-gray-700 font-black text-2xl text-center mb-2">Marketplace<br>Pesantren</h2>
          <p class="text-gray-400 text-sm text-center leading-relaxed">
            Fitur belanja khusus untuk wali santri.<br>Belanjakan kebutuhan anak Anda langsung dari genggaman.
          </p>
        </div>

        <!-- Status Bar -->
        <div class="px-5 py-4 border-t border-gray-50">
          <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-bold text-gray-600">Progress Pengembangan</p>
            <span class="text-xs font-bold text-amber-500">65%</span>
          </div>
          <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full w-[65%] bg-gradient-to-r from-amber-300 to-amber-500 rounded-full" />
          </div>
          <p class="text-[10px] text-gray-400 mt-1.5">Estimasi rilis: Semester 2 - 2026</p>
        </div>
      </div>
    </div>

    <!-- Fitur Yang Akan Hadir -->
    <div class="px-4 mb-5">
      <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 px-1">Yang Akan Hadir</p>
      <div class="flex flex-col gap-3">
        <FeatureCard
          :icon="CubeIcon"
          color="bg-blue-50"
          iconColor="text-blue-500"
          title="Kebutuhan Santri"
          desc="Peralatan mandi, belajar, pakaian, dan perlengkapan pondok"
        />
        <FeatureCard
          :icon="HeartIcon"
          color="bg-red-50"
          iconColor="text-red-400"
          title="Kiriman Makanan & Jajan"
          desc="Kirimkan makanan favorit atau jajan langsung ke pondok"
        />
        <FeatureCard
          :icon="BookOpenIcon"
          color="bg-green-50"
          iconColor="text-emerald-500"
          title="Buku & Kitab"
          desc="Kitab kuning, buku pelajaran, dan alat tulis resmi pesantren"
        />
        <FeatureCard
          :icon="CurrencyDollarIcon"
          color="bg-violet-50"
          iconColor="text-violet-500"
          title="Bayar Langsung ke Tabungan"
          desc="Pembelian otomatis dipotong dari tabungan santri"
        />
      </div>
    </div>

    <!-- Notifikasi -->
    <div class="px-4 mb-8">
      <div class="bg-amber-50 border border-amber-100 rounded-2xl p-5">
        <div class="flex items-start gap-3">
          <BellIcon class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" />
          <div>
            <p class="text-amber-800 font-bold text-sm mb-1">Ingin Diberitahu Saat Rilis?</p>
            <p class="text-amber-700 text-xs leading-relaxed mb-3">
              Aktifkan notifikasi agar kami memberitahu Anda saat Marketplace Pesantren resmi diluncurkan.
            </p>
            <button
              @click="notifActivated = !notifActivated"
              class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold transition-colors"
              :class="notifActivated
                ? 'bg-emerald-500 text-white'
                : 'bg-amber-100 text-amber-800 active:bg-amber-200'"
            >
              <CheckCircleIcon v-if="notifActivated" class="w-4 h-4" />
              <BellIcon v-else class="w-4 h-4" />
              {{ notifActivated ? 'Notifikasi Aktif ✓' : 'Aktifkan Notifikasi' }}
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, defineComponent, h } from 'vue'
import {
  ChevronLeftIcon, ShoppingBagIcon, CubeIcon, HeartIcon,
  BookOpenIcon, BellIcon, CheckCircleIcon,
} from '@heroicons/vue/24/outline'
import { CurrencyDollarIcon } from '@heroicons/vue/24/solid'

const notifActivated = ref(false)

const FeatureCard = defineComponent({
  props: { icon: Object, color: String, iconColor: String, title: String, desc: String },
  setup(props) {
    return () => h('div', { class: 'bg-white rounded-2xl border border-gray-100/80 shadow-sm p-4 flex items-start gap-3' }, [
      h('div', { class: `w-10 h-10 rounded-xl ${props.color} flex items-center justify-center flex-shrink-0` },
        [h(props.icon as any, { class: `w-5 h-5 ${props.iconColor}` })]),
      h('div', { class: 'flex-1' }, [
        h('p', { class: 'text-gray-800 font-bold text-sm mb-0.5' }, props.title),
        h('p', { class: 'text-gray-400 text-xs leading-relaxed' }, props.desc),
      ]),
    ])
  },
})
</script>
