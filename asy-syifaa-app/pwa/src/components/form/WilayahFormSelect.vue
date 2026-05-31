<template>
  <div class="flex flex-col gap-3">
    <!-- Provinsi -->
    <div>
      <label class="text-sm font-semibold text-gray-700 mb-1.5 block">Provinsi</label>
      <select v-model="selectedProvince"
              class="w-full px-4 py-3.5 rounded-xl border border-gray-200 text-gray-800 text-sm
                     bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-500/30 appearance-none">
        <option value="">— Pilih Provinsi —</option>
        <option v-for="p in provinces" :key="p.code" :value="p.code">{{ p.name }}</option>
      </select>
    </div>

    <!-- Kab/Kota -->
    <div>
      <label class="text-sm font-semibold text-gray-700 mb-1.5 block">Kabupaten/Kota</label>
      <select v-model="selectedCity" :disabled="!selectedProvince"
              class="w-full px-4 py-3.5 rounded-xl border border-gray-200 text-gray-800 text-sm
                     bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-500/30 appearance-none
                     disabled:opacity-50">
        <option value="">— Pilih Kab/Kota —</option>
        <option v-for="c in cities" :key="c.code" :value="c.code">{{ c.name }}</option>
      </select>
    </div>

    <!-- Kecamatan -->
    <div>
      <label class="text-sm font-semibold text-gray-700 mb-1.5 block">Kecamatan</label>
      <select v-model="selectedDistrict" :disabled="!selectedCity"
              class="w-full px-4 py-3.5 rounded-xl border border-gray-200 text-gray-800 text-sm
                     bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-500/30 appearance-none
                     disabled:opacity-50">
        <option value="">— Pilih Kecamatan —</option>
        <option v-for="d in districts" :key="d.code" :value="d.code">{{ d.name }}</option>
      </select>
    </div>

    <!-- Desa/Kelurahan -->
    <div>
      <label class="text-sm font-semibold text-gray-700 mb-1.5 block">Desa/Kelurahan</label>
      <select v-model="selectedVillage" :disabled="!selectedDistrict"
              class="w-full px-4 py-3.5 rounded-xl border border-gray-200 text-gray-800 text-sm
                     bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-500/30 appearance-none
                     disabled:opacity-50">
        <option value="">— Pilih Desa/Kelurahan —</option>
        <option v-for="v in villages" :key="v.code" :value="v.code">{{ v.name }}</option>
      </select>
    </div>

    <!-- Kode Pos -->
    <div>
      <label class="text-sm font-semibold text-gray-700 mb-1.5 block">Kode Pos</label>
      <input :value="postalCode" readonly
             class="w-full px-4 py-3.5 rounded-xl border border-gray-200 text-gray-800 text-sm
                    bg-gray-100 cursor-not-allowed" />
    </div>

    <!-- Loading indicator -->
    <div v-if="loading" class="flex items-center gap-2 text-xs text-gray-400">
      <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
      </svg>
      Memuat data wilayah...
    </div>
  </div>
</template>

<script setup lang="ts">
import { useWilayah } from '@/composables/useWilayah'

const {
  provinces, cities, districts, villages,
  selectedProvince, selectedCity, selectedDistrict, selectedVillage,
  postalCode, loading,
} = useWilayah()

defineExpose({
  selectedProvince, selectedCity, selectedDistrict, selectedVillage, postalCode,
})
</script>
