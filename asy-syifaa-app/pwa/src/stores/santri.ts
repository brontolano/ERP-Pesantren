import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { waliApi, type Santri } from '@/api/wali'
import { DEMO_SANTRI } from '@/api/mock'

const DEMO = import.meta.env.VITE_DEMO_MODE === 'true'

export const useSantriStore = defineStore('santri', () => {
  const list          = ref<Santri[]>(DEMO ? (DEMO_SANTRI as unknown as Santri[]) : [])
  const activeIndex   = ref(0)
  const loading       = ref(false)

  const active = computed(() => list.value[activeIndex.value] ?? null)

  async function fetch() {
    if (DEMO) {
      list.value = DEMO_SANTRI as unknown as Santri[]
      return
    }
    loading.value = true
    try {
      list.value = await waliApi.getSantri()
    } finally {
      loading.value = false
    }
  }

  function setActive(index: number) {
    activeIndex.value = index
  }

  return { list, active, activeIndex, loading, fetch, setActive }
})
