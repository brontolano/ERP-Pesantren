/**
 * Composable: Jadwal Sholat + Countdown
 * - Demo mode  → pakai waktu hardcoded Sumedang
 * - Real mode  → Geolocation → aladhan.com API (gratis, tanpa key)
 */
import { ref, computed, onMounted, onUnmounted } from 'vue'

const DEMO_MODE = import.meta.env.VITE_DEMO_MODE === 'true'

// Waktu sholat demo untuk Sumedang, Jawa Barat
const DEMO_TIMES: Record<string, string> = {
  Fajr:    '04:29',
  Sunrise: '05:44',
  Dhuhr:   '11:54',
  Asr:     '15:14',
  Maghrib: '17:53',
  Isha:    '19:04',
}

export const PRAYER_LABELS: Record<string, string> = {
  Fajr:    'Shubuh',
  Dhuhr:   'Dzuhur',
  Asr:     'Ashr',
  Maghrib: 'Maghrib',
  Isha:    'Isya',
}

const PRAYER_KEYS = ['Fajr', 'Dhuhr', 'Asr', 'Maghrib', 'Isha']

export interface PrayerItem {
  key: string
  label: string
  time: string
  minutes: number
}

function toMinutes(t: string): number {
  const [h, m] = t.split(':').map(Number)
  return h * 60 + (m || 0)
}

function pad2(n: number) { return String(n).padStart(2, '0') }

export function useSholatTime() {
  const rawTimes  = ref<Record<string, string>>({})
  const cityName  = ref('Sumedang')
  const loading   = ref(true)
  const error     = ref(false)
  const now       = ref(new Date())
  let   ticker: ReturnType<typeof setInterval>

  // ── Prayer list (filtered to 5 main prayers) ───────────────────
  const prayerList = computed<PrayerItem[]>(() =>
    PRAYER_KEYS
      .filter(k => rawTimes.value[k])
      .map(k => ({
        key:     k,
        label:   PRAYER_LABELS[k],
        time:    rawTimes.value[k],
        minutes: toMinutes(rawTimes.value[k]),
      }))
  )

  // ── Next prayer ────────────────────────────────────────────────
  const nextPrayer = computed<PrayerItem | null>(() => {
    if (!prayerList.value.length) return null
    const nowMin = now.value.getHours() * 60 + now.value.getMinutes()
    return prayerList.value.find(p => p.minutes > nowMin) ?? prayerList.value[0]
  })

  // ── Countdown string HH:MM:SS ──────────────────────────────────
  const countdown = computed<string>(() => {
    if (!nextPrayer.value) return '--:--:--'
    const nowSec = now.value.getHours() * 3600 + now.value.getMinutes() * 60 + now.value.getSeconds()
    let diff = nextPrayer.value.minutes * 60 - nowSec
    if (diff <= 0) diff += 86400        // besok
    const h = Math.floor(diff / 3600)
    const m = Math.floor((diff % 3600) / 60)
    const s = diff % 60
    return `${pad2(h)}:${pad2(m)}:${pad2(s)}`
  })

  // ── Reverse geocode (OpenStreetMap Nominatim, gratis tanpa key) ──
  async function reverseGeocode(lat: number, lng: number) {
    try {
      const url = `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&zoom=12&accept-language=id`
      const res = await fetch(url, { headers: { Accept: 'application/json' } })
      const json = await res.json()
      const a = json?.address ?? {}
      const name =
        a.city || a.town || a.village || a.municipality ||
        a.county || a.suburb || a.state_district || a.state
      if (name) cityName.value = name
    } catch { /* keep previous cityName */ }
  }

  // ── Fetch waktu sholat dari aladhan API ────────────────────────
  async function fetchFromCoords(lat: number, lng: number) {
    try {
      const ts  = Math.floor(Date.now() / 1000)
      const url = `https://api.aladhan.com/v1/timings/${ts}?latitude=${lat}&longitude=${lng}&method=11`
      const res = await fetch(url)
      const json = await res.json()
      if (json.code === 200) {
        rawTimes.value = json.data.timings
        // Nama kota akurat lewat reverse geocode GPS user
        await reverseGeocode(lat, lng)
        return
      }
    } catch { /* fallthrough */ }
    rawTimes.value = DEMO_TIMES
    error.value = true
  }

  // ── Mount ──────────────────────────────────────────────────────
  onMounted(() => {
    if (DEMO_MODE) {
      rawTimes.value = DEMO_TIMES
      loading.value  = false
    } else if ('geolocation' in navigator) {
      navigator.geolocation.getCurrentPosition(
        pos => {
          fetchFromCoords(pos.coords.latitude, pos.coords.longitude)
            .finally(() => { loading.value = false })
        },
        () => {
          rawTimes.value = DEMO_TIMES
          loading.value  = false
          error.value    = true
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 300000 }
      )
    } else {
      rawTimes.value = DEMO_TIMES
      loading.value  = false
    }

    // Tick every second untuk countdown
    ticker = setInterval(() => { now.value = new Date() }, 1000)
  })

  onUnmounted(() => clearInterval(ticker))

  return {
    prayerList,
    nextPrayer,
    countdown,
    cityName,
    loading,
    error,
    now,
  }
}
