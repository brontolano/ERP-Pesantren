import { ref, watch } from 'vue'
import { wilayahApi, type WilayahItem } from '@/api/wilayah'

export function useWilayah() {
  const provinces = ref<WilayahItem[]>([])
  const cities = ref<WilayahItem[]>([])
  const districts = ref<WilayahItem[]>([])
  const villages = ref<WilayahItem[]>([])

  const selectedProvince = ref('')
  const selectedCity = ref('')
  const selectedDistrict = ref('')
  const selectedVillage = ref('')
  const postalCode = ref('')

  const loading = ref(false)

  async function loadProvinces() {
    provinces.value = await wilayahApi.provinces()
  }

  watch(selectedProvince, async (code) => {
    cities.value = []
    districts.value = []
    villages.value = []
    selectedCity.value = ''
    selectedDistrict.value = ''
    selectedVillage.value = ''
    postalCode.value = ''
    if (code) {
      loading.value = true
      cities.value = await wilayahApi.cities(code)
      loading.value = false
    }
  })

  watch(selectedCity, async (code) => {
    districts.value = []
    villages.value = []
    selectedDistrict.value = ''
    selectedVillage.value = ''
    postalCode.value = ''
    if (code) {
      loading.value = true
      districts.value = await wilayahApi.districts(code)
      loading.value = false
    }
  })

  watch(selectedDistrict, async (code) => {
    villages.value = []
    selectedVillage.value = ''
    postalCode.value = ''
    if (code) {
      loading.value = true
      villages.value = await wilayahApi.villages(code)
      loading.value = false
    }
  })

  watch(selectedVillage, (code) => {
    const v = villages.value.find(v => v.code === code)
    postalCode.value = v?.postal_code ?? ''
  })

  function setFromCodes(province: string, city: string, district: string, village: string) {
    selectedProvince.value = province
    setTimeout(() => {
      selectedCity.value = city
      setTimeout(() => {
        selectedDistrict.value = district
        setTimeout(() => { selectedVillage.value = village }, 300)
      }, 300)
    }, 300)
  }

  loadProvinces()

  return {
    provinces, cities, districts, villages,
    selectedProvince, selectedCity, selectedDistrict, selectedVillage,
    postalCode, loading,
    setFromCodes,
  }
}
