<template>
  <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100/80 flex items-center gap-3">
    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" :class="bgClass">
      <component :is="icon" class="w-5 h-5" :class="iconClass" />
    </div>
    <div class="min-w-0">
      <p class="text-xs text-gray-400 font-medium">{{ label }}</p>
      <p class="font-bold text-sm mt-0.5 truncate" :class="textClass">{{ value }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { FunctionalComponent, SVGAttributes } from 'vue'

const props = defineProps<{
  label: string
  value: string
  icon: FunctionalComponent<SVGAttributes>
  color: string
}>()

const bgClass   = computed(() => ({ green: 'bg-emerald-50', amber: 'bg-amber-50', red: 'bg-red-50' }[props.color] ?? 'bg-gray-50'))
const iconClass = computed(() => ({ green: 'text-emerald-600', amber: 'text-amber-500', red: 'text-red-500' }[props.color] ?? 'text-gray-400'))
const textClass = computed(() => ({ green: 'text-emerald-700', amber: 'text-amber-700', red: 'text-red-700' }[props.color] ?? 'text-gray-700'))
</script>
