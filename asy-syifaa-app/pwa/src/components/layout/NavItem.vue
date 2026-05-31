<template>
  <router-link
    :to="to"
    class="flex flex-col items-center gap-0.5 flex-1 min-w-0 py-2 px-1
           active:opacity-60 transition-opacity select-none"
  >
    <!-- Material You pill + icon -->
    <span
      class="flex items-center justify-center w-14 h-8 rounded-full transition-all duration-300"
      :class="isActive ? 'bg-brand-100' : 'bg-transparent'"
    >
      <!-- Solid icon when active, outline when inactive -->
      <component
        :is="isActive ? (iconSolid ?? icon) : icon"
        class="w-6 h-6 transition-colors duration-200"
        :class="isActive ? 'text-brand-700' : 'text-gray-400'"
      />
    </span>
    <!-- Label -->
    <span
      class="text-[10px] font-semibold leading-none transition-colors duration-200"
      :class="isActive ? 'text-brand-700' : 'text-gray-400'"
    >
      {{ label }}
    </span>
  </router-link>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const props = defineProps<{
  to: string
  icon: object | Function
  iconSolid?: object | Function
  label: string
}>()

const route = useRoute()
const isActive = computed(() =>
  route.path === props.to || route.path.startsWith(props.to + '/')
)
</script>
