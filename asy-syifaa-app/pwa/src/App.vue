<template>
  <router-view />
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
const auth = useAuthStore()
onMounted(() => {
  document.querySelector('meta[name="theme-color"]')?.setAttribute('content', '#1f6b43')
})
</script>
