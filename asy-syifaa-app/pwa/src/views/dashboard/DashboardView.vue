<template>
  <div class="flex flex-col min-h-full bg-[#F5F7FA] pb-nav">

    <!-- ─── Header ─────────────────────────────────────────── -->
    <div class="relative bg-gradient-to-br from-brand-700 via-brand-700 to-brand-800
                px-5 pt-12 pb-28 overflow-hidden">
      <div class="absolute -top-8 -right-8 w-40 h-40 rounded-full bg-white/5" />
      <div class="absolute top-4 right-12 w-20 h-20 rounded-full bg-white/5" />
      <div class="absolute -bottom-6 -left-6 w-28 h-28 rounded-full bg-white/5" />

      <div class="relative flex items-center justify-between">
        <div>
          <p class="text-white/60 text-xs font-medium tracking-wide">Assalamu'alaikum,</p>
          <h1 class="text-white font-bold text-xl leading-tight mt-0.5">{{ auth.namaWali }}</h1>
        </div>
        <router-link to="/profil"
          class="w-11 h-11 rounded-full bg-white/15 border-2 border-white/25
                 flex items-center justify-center flex-shrink-0 active:bg-white/25 transition-colors">
          <span class="text-white font-bold text-base">{{ initials }}</span>
        </router-link>
      </div>

      <!-- Santri Tabs -->
      <div v-if="santriStore.list.length > 1"
           class="relative flex gap-2 mt-4 overflow-x-auto pb-1 scrollbar-hide">
        <button
          v-for="(s, i) in santriStore.list" :key="s.id"
          @click="santriStore.setActive(i)"
          class="flex-shrink-0 px-3.5 py-1.5 rounded-full text-sm font-semibold transition-all duration-200 border"
          :class="i === santriStore.activeIndex
            ? 'bg-white text-brand-700 border-white shadow-sm'
            : 'bg-white/10 text-white/80 border-white/20'"
        >
          {{ s.nama.split(' ')[0] }}
        </button>
      </div>
    </div>

    <!-- ─── Santri Card ───────────────────────────────────── -->
    <div class="relative z-10 px-4 -mt-16">
      <div v-if="loading" class="skeleton h-32 rounded-2xl" />
      <div v-else-if="santri"
           class="bg-white rounded-2xl p-4 shadow-lg shadow-brand-900/10 border border-white">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-600 to-brand-800
                      flex items-center justify-center flex-shrink-0">
            <span class="text-white font-bold text-base">
              {{ santri.nama.split(' ').slice(0,2).map((w: string) => w[0]).join('') }}
            </span>
          </div>
          <div class="flex-1 min-w-0">
            <p class="font-bold text-gray-800 truncate leading-tight">{{ santri.nama }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ santri.kelas }} · {{ santri.jenjang }}</p>
          </div>
          <span class="px-2.5 py-1 rounded-full text-xs font-bold shrink-0"
                :class="santri.status === 'aktif'
                  ? 'bg-emerald-50 text-emerald-700 border border-emerald-100'
                  : 'bg-gray-100 text-gray-500'">
            {{ santri.status === 'aktif' ? 'Aktif' : santri.status }}
          </span>
        </div>
        <div class="grid grid-cols-2 gap-3 mt-4 pt-3.5 border-t border-gray-50">
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-gray-50 flex items-center justify-center">
              <IdentificationIcon class="w-4 h-4 text-gray-400" />
            </div>
            <div>
              <p class="text-[10px] text-gray-400 leading-none">NIS</p>
              <p class="text-sm font-bold text-gray-700 leading-tight">{{ santri.nis }}</p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center"
                 :class="(santri.tunggakan_bulan ?? 0) > 0 ? 'bg-red-50' : 'bg-emerald-50'">
              <BanknotesIcon class="w-4 h-4"
                :class="(santri.tunggakan_bulan ?? 0) > 0 ? 'text-red-400' : 'text-emerald-500'" />
            </div>
            <div>
              <p class="text-[10px] text-gray-400 leading-none">Tunggakan</p>
              <p class="text-sm font-bold leading-tight"
                 :class="(santri.tunggakan_bulan ?? 0) > 0 ? 'text-red-600' : 'text-emerald-600'">
                {{ (santri.tunggakan_bulan ?? 0) > 0 ? `${santri.tunggakan_bulan} bulan` : 'Lunas ✓' }}
              </p>
            </div>
          </div>
        </div>
        <router-link to="/santri/detail"
          class="mt-3 flex items-center justify-center gap-1.5 py-2.5 rounded-xl bg-brand-50
                 text-brand-700 text-xs font-bold active:bg-brand-100 transition-colors">
          <InformationCircleIcon class="w-3.5 h-3.5" />
          Lihat Profil Lengkap Santri
        </router-link>
      </div>
      <div v-else-if="!loading"
           class="bg-white rounded-2xl p-5 shadow-sm text-center text-gray-400 text-sm">
        Data santri tidak ditemukan
      </div>
    </div>

    <!-- ─── Jadwal Sholat Widget ─────────────────────────── -->
    <div class="px-4 mt-4">
      <div class="bg-gradient-to-br from-brand-800 to-brand-900 rounded-2xl overflow-hidden shadow-lg shadow-brand-900/20">
        <!-- Countdown -->
        <div class="px-4 pt-4 pb-3">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-white/60 text-[10px] font-medium uppercase tracking-widest mb-0.5">Sholat Berikutnya</p>
              <p class="text-white font-black text-lg leading-none">
                {{ nextPrayer?.label ?? '—' }}
                <span class="text-white/50 font-normal text-sm ml-1">· {{ nextPrayer?.time ?? '--:--' }}</span>
              </p>
            </div>
            <!-- Countdown clock -->
            <div class="bg-white/10 rounded-xl px-3 py-2 text-right">
              <p class="text-white/60 text-[9px] font-medium mb-0.5">MENUJU SHOLAT</p>
              <p class="text-white font-black text-base font-mono tracking-tight">{{ countdown }}</p>
            </div>
          </div>
        </div>

        <!-- Prayer time row -->
        <div class="grid grid-cols-5 border-t border-white/10">
          <div
            v-for="p in prayerList" :key="p.key"
            class="flex flex-col items-center py-2.5 px-1"
            :class="p.key === nextPrayer?.key ? 'bg-white/10' : ''"
          >
            <p class="text-white/50 text-[9px] font-semibold mb-0.5">{{ p.label }}</p>
            <p class="text-white text-xs font-bold">{{ p.time }}</p>
            <div v-if="p.key === nextPrayer?.key" class="w-1 h-1 rounded-full bg-amber-400 mt-1" />
          </div>
        </div>

        <!-- Lokasi info -->
        <div class="px-4 py-2 border-t border-white/10 flex items-center gap-1.5">
          <MapPinIcon class="w-3 h-3 text-white/40" />
          <p class="text-white/40 text-[10px]">
            {{ sholatLoading ? 'Mendeteksi lokasi...' : cityName }}
          </p>
        </div>
      </div>
    </div>

    <!-- ─── Status Hari Ini ────────────────────────────────── -->
    <div class="px-4 mt-5">
      <SectionTitle title="Status Hari Ini" />

      <div v-if="loading" class="space-y-3">
        <div class="grid grid-cols-2 gap-3">
          <div class="skeleton h-[72px] rounded-2xl" />
          <div class="skeleton h-[72px] rounded-2xl" />
        </div>
        <div class="skeleton h-[76px] rounded-2xl" />
      </div>

      <template v-else>
        <!-- Kehadiran + Kesehatan -->
        <div class="grid grid-cols-2 gap-3 mb-3" v-if="statusHariIni">
          <StatusCard
            label="Kehadiran"
            :value="statusHariIni.status_label"
            :icon="statusHariIni.status_kehadiran === 'hadir' ? CheckCircleIcon : ExclamationTriangleIcon"
            :color="statusHariIni.status_kehadiran === 'hadir' ? 'green' : 'amber'"
          />
          <StatusCard
            label="Kesehatan"
            :value="statusHariIni.kesehatan_label"
            :icon="statusHariIni.status_kesehatan === 'sehat' ? HeartIcon : PlusCircleIcon"
            :color="statusHariIni.status_kesehatan === 'sehat' ? 'green' : 'red'"
          />
        </div>
        <div v-else class="mb-3 bg-white rounded-2xl p-4 text-center text-sm text-gray-400 border border-gray-100">
          <CalendarIcon class="w-8 h-8 text-gray-200 mx-auto mb-1" />
          Belum ada data kehadiran hari ini
        </div>

        <!-- Kegiatan Sekarang -->
        <div v-if="kegiatanSekarang"
             class="bg-white rounded-2xl border border-gray-100/80 shadow-sm overflow-hidden">
          <div class="h-1"
               :class="kegiatanSekarang.status === 'berlangsung' ? 'bg-brand-500' : 'bg-amber-400'" />
          <div class="p-4 flex items-start gap-3">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                 :class="kegiatanSekarang.status === 'berlangsung' ? 'bg-brand-50' : 'bg-amber-50'">
              <AcademicCapIcon v-if="kegiatanSekarang.status === 'berlangsung'"
                class="w-4 h-4 text-brand-600" />
              <ClockIcon v-else class="w-4 h-4 text-amber-500" />
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 mb-0.5">
                <span class="text-[9px] font-bold px-2 py-0.5 rounded-full"
                      :class="kegiatanSekarang.status === 'berlangsung'
                        ? 'bg-brand-50 text-brand-600'
                        : 'bg-amber-50 text-amber-600'">
                  {{ kegiatanSekarang.status === 'berlangsung' ? '● BERLANGSUNG' : 'ISTIRAHAT' }}
                </span>
                <span class="text-gray-400 text-[10px] font-mono">
                  {{ kegiatanSekarang.jam_mulai }} – {{ kegiatanSekarang.jam_selesai }}
                </span>
              </div>
              <p class="text-gray-800 font-bold text-sm truncate">{{ kegiatanSekarang.mata_pelajaran }}</p>
              <p v-if="kegiatanSekarang.guru" class="text-gray-400 text-xs mt-0.5 truncate">
                {{ kegiatanSekarang.guru }}
                <span v-if="kegiatanSekarang.ruang"> · {{ kegiatanSekarang.ruang }}</span>
              </p>
            </div>
          </div>
        </div>
        <div v-else
             class="bg-white rounded-2xl border border-gray-100/80 p-4 flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-gray-50 flex items-center justify-center flex-shrink-0">
            <MoonIcon class="w-4 h-4 text-gray-400" />
          </div>
          <div>
            <p class="text-gray-700 font-semibold text-sm">Tidak ada kegiatan</p>
            <p class="text-gray-400 text-xs">Waktu bebas / libur</p>
          </div>
        </div>
      </template>
    </div>

    <!-- ─── Akses Cepat: Keuangan ────────────────────────── -->
    <div class="px-4 mt-5">
      <SectionTitle title="Keuangan" />
      <div class="grid grid-cols-4 gap-2.5">
        <QuickAction to="/keuangan/tagihan"   :icon="ReceiptPercentIcon"  label="Tagihan"   color="red"    />
        <QuickAction to="/keuangan/tabungan"  :icon="BanknotesIcon"        label="Tabungan"  color="green"  />
        <QuickAction to="/keuangan/transaksi" :icon="ArrowsRightLeftIcon"  label="Transaksi" color="blue"   />
        <QuickAction to="/keuangan/bayar"     :icon="QrCodeIcon"           label="Bayar"     color="violet" />
      </div>
    </div>

    <!-- ─── Akses Cepat: Belajar ─────────────────────────── -->
    <div class="px-4 mt-4">
      <SectionTitle title="Belajar" />
      <div class="grid grid-cols-4 gap-2.5">
        <QuickAction to="/belajar/hafalan"   :icon="BookOpenIcon"               label="Hafalan"   color="brand"  />
        <QuickAction to="/belajar/jadwal"    :icon="CalendarDaysIcon"           label="Jadwal"    color="teal"   />
        <QuickAction to="/belajar/absensi"   :icon="ClipboardDocumentCheckIcon" label="Absensi"   color="amber"  />
        <QuickAction to="/belajar/akademik"  :icon="AcademicCapIcon"            label="Akademik"  color="indigo" />
      </div>
      <div class="grid grid-cols-4 gap-2.5 mt-2.5">
        <QuickAction to="/belajar/prestasi"  :icon="TrophyIcon"                 label="Prestasi"  color="orange" />
        <div class="col-span-3" />
      </div>
    </div>

    <!-- ─── Akses Cepat: Kegiatan & Info ─────────────────── -->
    <div class="px-4 mt-4">
      <SectionTitle title="Kegiatan & Info" />
      <div class="grid grid-cols-4 gap-2.5">
        <QuickAction to="/kegiatan/izin"      :icon="DocumentTextIcon"           label="Izin"      color="blue"   />
        <QuickAction to="/kegiatan/kesehatan" :icon="HeartIcon"                  label="Kesehatan" color="red"    />
        <QuickAction to="/kegiatan/kunjungan" :icon="HomeIcon"                   label="Kunjungan" color="green"  />
        <QuickAction to="/kegiatan/konseling" :icon="ChatBubbleLeftEllipsisIcon" label="Konseling" color="violet" />
      </div>
      <div class="grid grid-cols-4 gap-2.5 mt-2.5">
        <QuickAction to="/kegiatan/presensi"  :icon="CheckBadgeIcon"             label="Presensi"  color="teal"   />
        <QuickAction to="/info"               :icon="BellAlertIcon"              label="Info"      color="amber"  />
        <div class="col-span-2" />
      </div>
    </div>

    <!-- ─── Marketplace (Disabled) ────────────────────────── -->
    <div class="px-4 mt-4 mb-6">
      <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
          <div class="w-1 h-4 bg-gray-300 rounded-full" />
          <h2 class="text-sm font-bold text-gray-400">Marketplace</h2>
        </div>
        <span class="text-[10px] font-bold text-white bg-gray-400 px-2.5 py-1 rounded-full tracking-wide">
          SEGERA HADIR
        </span>
      </div>

      <!-- Disabled card -->
      <router-link to="/marketplace"
        class="block bg-white rounded-2xl border border-gray-100/80 shadow-sm overflow-hidden
               relative active:scale-[0.98] transition-transform select-none">
        <!-- Gray overlay -->
        <div class="absolute inset-0 bg-white/60 z-10 flex items-center justify-center">
          <div class="bg-gray-100 border border-gray-200 rounded-full px-4 py-1.5 flex items-center gap-2">
            <LockClosedIcon class="w-3.5 h-3.5 text-gray-400" />
            <span class="text-gray-500 text-xs font-bold">Belum Tersedia</span>
          </div>
        </div>
        <div class="p-4 filter grayscale opacity-50">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center">
              <ShoppingBagIcon class="w-5 h-5 text-gray-400" />
            </div>
            <div>
              <p class="font-bold text-gray-700 text-sm">Marketplace Pesantren</p>
              <p class="text-gray-400 text-xs">Belanja kebutuhan santri</p>
            </div>
          </div>
          <div class="grid grid-cols-4 gap-2">
            <div v-for="item in marketplacePreviews" :key="item"
                 class="bg-gray-50 rounded-xl py-3 flex flex-col items-center gap-1">
              <div class="w-6 h-6 rounded-lg bg-gray-200" />
              <p class="text-[9px] text-gray-300 font-medium">{{ item }}</p>
            </div>
          </div>
        </div>
      </router-link>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, defineComponent, h } from 'vue'
import { useAuthStore }    from '@/stores/auth'
import { useSantriStore }  from '@/stores/santri'
import { useSholatTime }   from '@/composables/useSholatTime'
import { waliApi, type StatusHarian } from '@/api/wali'
import { getKegiatanSekarang } from '@/api/mock'
import StatusCard  from './StatusCard.vue'
import QuickAction from './QuickAction.vue'

import {
  CheckCircleIcon, ExclamationTriangleIcon,
  HeartIcon, PlusCircleIcon, CalendarIcon,
  IdentificationIcon, BanknotesIcon,
  BookOpenIcon, BellAlertIcon,
  ReceiptPercentIcon, ArrowsRightLeftIcon, QrCodeIcon,
  CalendarDaysIcon, ClipboardDocumentCheckIcon,
  AcademicCapIcon, TrophyIcon, DocumentTextIcon,
  HomeIcon, ChatBubbleLeftEllipsisIcon, CheckBadgeIcon,
  InformationCircleIcon, MapPinIcon,
  ShoppingBagIcon, LockClosedIcon,
  ClockIcon, MoonIcon,
} from '@heroicons/vue/24/outline'

// ── Inline SectionTitle ────────────────────────────────────────
const SectionTitle = defineComponent({
  props: { title: String },
  setup(props) {
    return () => h('div', { class: 'flex items-center gap-2 mb-3' }, [
      h('div', { class: 'w-1 h-4 bg-brand-600 rounded-full' }),
      h('h2', { class: 'text-sm font-bold text-gray-700' }, props.title),
    ])
  },
})

// ── Stores & composables ───────────────────────────────────────
const auth        = useAuthStore()
const santriStore = useSantriStore()
const santri      = computed(() => santriStore.active)
const loading     = ref(true)
const statusData  = ref<{ hari_ini: StatusHarian[] } | null>(null)
const statusHariIni = computed(() => statusData.value?.hari_ini?.[0] ?? null)

// Jadwal sholat
const { prayerList, nextPrayer, countdown, cityName, loading: sholatLoading } = useSholatTime()

// Kegiatan sekarang (dari jadwal)
const kegiatanSekarang = computed(() => getKegiatanSekarang())

const initials = computed(() =>
  auth.namaWali.split(' ').slice(0, 2).map((w: string) => w[0] ?? '').join('').toUpperCase() || 'W'
)

const marketplacePreviews = ['Seragam', 'Kitab', 'Jajan', 'Alkes']

onMounted(async () => {
  if (!santriStore.list.length) await santriStore.fetch()
  if (santri.value) {
    try { statusData.value = await waliApi.getStatusHarian(santri.value.id) } catch {}
  }
  loading.value = false
})
</script>
