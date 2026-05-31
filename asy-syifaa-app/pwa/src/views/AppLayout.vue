<template>
  <div class="flex flex-col h-dvh bg-[#F5F7FA] max-w-md mx-auto relative overflow-hidden">
    <main class="flex-1 overflow-y-auto mb-nav">
      <router-view v-slot="{ Component, route }">
        <transition :name="transitionName">
          <component :is="Component" :key="route.path" />
        </transition>
      </router-view>
    </main>

    <!-- Bottom Navigation -->
    <nav class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-md
                bg-white/96 backdrop-blur-sm border-t border-gray-100/80 pb-safe z-50
                shadow-[0_-4px_24px_rgba(0,0,0,0.07)]">
      <div class="flex items-center justify-around px-1 pt-1 pb-1">
        <NavItem to="/dashboard"  :icon="HomeIcon"        :icon-solid="HomeSolid"    label="Beranda"  />
        <NavItem to="/keuangan"   :icon="WalletIcon"      :icon-solid="WalletSolid"  label="Keuangan" />
        <NavItem to="/belajar"    :icon="AcademicIcon"    :icon-solid="AcademicSolid" label="Belajar" />
        <NavItem to="/kegiatan"   :icon="SparklesIcon"    :icon-solid="SparklesSolid" label="Kegiatan"/>
        <NavItem to="/info"       :icon="BellIcon"        :icon-solid="BellSolid"    label="Info"     />
      </div>
    </nav>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import NavItem from '@/components/layout/NavItem.vue'

import { HomeIcon, WalletIcon, AcademicCapIcon as AcademicIcon, SparklesIcon, BellIcon } from '@heroicons/vue/24/outline'
import { HomeIcon as HomeSolid, WalletIcon as WalletSolid, AcademicCapIcon as AcademicSolid, SparklesIcon as SparklesSolid, BellIcon as BellSolid } from '@heroicons/vue/24/solid'

const route = useRoute()
const transitionName = ref('slide-left')

const NAV_ORDER = ['dashboard', 'keuangan', 'belajar', 'kegiatan', 'info']

function getTabIndex(path: string) {
  if (path.startsWith('/dashboard') || path.startsWith('/profil') || path.startsWith('/santri')) return 0
  if (path.startsWith('/keuangan'))  return 1
  if (path.startsWith('/belajar'))   return 2
  if (path.startsWith('/kegiatan'))  return 3
  if (path.startsWith('/info'))      return 4
  return -1
}

let prevTabIndex = 0
watch(() => route.path, (newPath, oldPath) => {
  const ni = getTabIndex(newPath)
  const oi = getTabIndex(oldPath)
  if (ni === -1 || oi === -1) {
    transitionName.value = 'slide-left'
  } else if (ni === oi) {
    // Sub-page navigation (going deeper)
    const isGoingDeeper = newPath.length > oldPath.length
    transitionName.value = isGoingDeeper ? 'slide-left' : 'slide-right'
  } else {
    transitionName.value = ni >= oi ? 'slide-left' : 'slide-right'
  }
  prevTabIndex = ni
})
</script>
