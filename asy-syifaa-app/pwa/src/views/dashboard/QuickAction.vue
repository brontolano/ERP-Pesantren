<template>
  <router-link :to="to"
    class="flex flex-col items-center gap-2.5 bg-white rounded-2xl py-4 px-2
           shadow-sm border border-gray-100/80 active:scale-95 transition-transform select-none">
    <div class="w-12 h-12 rounded-2xl flex items-center justify-center" :class="bgClass">
      <component :is="icon" class="w-6 h-6" :class="iconClass" />
    </div>
    <span class="text-xs font-semibold text-gray-700">{{ label }}</span>
  </router-link>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { FunctionalComponent, SVGAttributes } from 'vue'

const props = defineProps<{
  to: string
  label: string
  icon: FunctionalComponent<SVGAttributes>
  color?: 'green' | 'amber' | 'blue' | 'violet' | 'red' | 'orange' | 'teal' | 'pink' | 'indigo' | 'brand'
}>()

const bgClass = computed(() => ({
  green:  'bg-emerald-50',
  amber:  'bg-amber-50',
  blue:   'bg-sky-50',
  violet: 'bg-violet-50',
  red:    'bg-red-50',
  orange: 'bg-orange-50',
  teal:   'bg-teal-50',
  pink:   'bg-pink-50',
  indigo: 'bg-indigo-50',
  brand:  'bg-brand-50',
}[props.color ?? 'brand']))

const iconClass = computed(() => ({
  green:  'text-emerald-600',
  amber:  'text-amber-500',
  blue:   'text-sky-500',
  violet: 'text-violet-500',
  red:    'text-red-500',
  orange: 'text-orange-500',
  teal:   'text-teal-500',
  pink:   'text-pink-500',
  indigo: 'text-indigo-500',
  brand:  'text-brand-700',
}[props.color ?? 'brand']))
</script>
