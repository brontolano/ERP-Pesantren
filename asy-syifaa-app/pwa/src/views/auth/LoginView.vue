<template>
  <div class="min-h-dvh bg-gradient-to-br from-brand-700 via-brand-700 to-brand-900 flex flex-col relative overflow-hidden">

    <!-- Decorative circles -->
    <div class="absolute -top-12 -right-12 w-56 h-56 rounded-full bg-white/5" />
    <div class="absolute top-20 right-8 w-24 h-24 rounded-full bg-white/5" />
    <div class="absolute top-1/3 -left-8 w-32 h-32 rounded-full bg-white/5" />

    <!-- Splash hero -->
    <div class="relative flex-1 flex flex-col items-center justify-center px-6 pt-16 pb-8">
      <div class="w-20 h-20 rounded-3xl bg-white/15 border border-white/25
                  flex items-center justify-center mb-6 shadow-2xl">
        <img src="/logo.svg" alt="Logo" class="w-12 h-12 object-contain rounded-xl" />
      </div>
      <h1 class="text-white text-2xl font-black text-center mb-2 tracking-tight">Asy-Syifaa App</h1>
      <p class="text-white/60 text-sm text-center font-medium">Pantau perkembangan santri Anda</p>

      <div class="flex flex-wrap gap-2 mt-5 justify-center">
        <span v-for="f in ['📖 Hafalan', '💳 Tagihan', '🚪 Perizinan', '📢 Info']" :key="f"
              class="px-3 py-1 rounded-full bg-white/10 text-white/70 text-xs font-medium border border-white/10">
          {{ f }}
        </span>
      </div>
    </div>

    <!-- SSO Loading State -->
    <div v-if="loading && isSsoRedirect"
         class="relative bg-white rounded-t-3xl px-6 pt-10 pb-12 shadow-2xl flex flex-col items-center gap-4">
      <div class="w-10 h-1 bg-gray-200 rounded-full mx-auto mb-2" />
      <svg class="animate-spin w-10 h-10 text-brand-600" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
      </svg>
      <p class="text-gray-600 font-semibold text-base">Menyiapkan sesi Anda...</p>
      <p class="text-gray-400 text-sm text-center">Diarahkan dari portal admin pesantren</p>
    </div>

    <!-- Login card -->
    <div v-else class="relative bg-white rounded-t-3xl px-6 pt-7 shadow-2xl"
         :style="{ paddingBottom: 'calc(2.5rem + env(safe-area-inset-bottom, 0px))' }">

      <div class="w-10 h-1 bg-gray-200 rounded-full mx-auto mb-6" />

      <h2 class="text-gray-800 text-xl font-bold mb-1">Masuk ke Akun</h2>
      <p class="text-gray-400 text-sm mb-6">Gunakan username atau nomor HP Anda</p>

      <form @submit.prevent="handleLogin" class="flex flex-col gap-4">
        <!-- Username / HP -->
        <div>
          <label class="text-sm font-semibold text-gray-700 mb-1.5 block">Username / No. HP</label>
          <div class="relative">
            <div class="absolute left-4 top-1/2 -translate-y-1/2">
              <UserIcon class="w-4 h-4 text-gray-400" />
            </div>
            <input
              v-model="form.login"
              type="text"
              inputmode="text"
              autocomplete="username"
              placeholder="Masukkan username atau no. HP"
              class="w-full pl-11 pr-4 py-3.5 rounded-xl border border-gray-200 text-gray-800
                     text-base focus:outline-none focus:ring-2 focus:ring-brand-500/30
                     focus:border-brand-400 transition-colors bg-gray-50"
              required
            />
          </div>
        </div>

        <!-- Password -->
        <div>
          <label class="text-sm font-semibold text-gray-700 mb-1.5 block">Password</label>
          <div class="relative">
            <div class="absolute left-4 top-1/2 -translate-y-1/2">
              <LockClosedIcon class="w-4 h-4 text-gray-400" />
            </div>
            <input
              v-model="form.password"
              :type="showPass ? 'text' : 'password'"
              autocomplete="current-password"
              placeholder="Masukkan password"
              class="w-full pl-11 pr-12 py-3.5 rounded-xl border border-gray-200 text-gray-800
                     text-base focus:outline-none focus:ring-2 focus:ring-brand-500/30
                     focus:border-brand-400 transition-colors bg-gray-50"
              required
            />
            <button type="button" @click="showPass = !showPass"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 p-1.5 rounded-lg active:bg-gray-100 transition-colors">
              <EyeIcon v-if="!showPass" class="w-5 h-5" />
              <EyeSlashIcon v-else class="w-5 h-5" />
            </button>
          </div>
        </div>

        <!-- Error -->
        <div v-if="error"
             class="bg-red-50 border border-red-100 rounded-xl px-4 py-3
                    text-red-600 text-sm font-medium flex items-center gap-2">
          <ExclamationCircleIcon class="w-4 h-4 flex-shrink-0" />
          {{ error }}
        </div>

        <!-- Submit -->
        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-brand-700 text-white font-bold py-4 rounded-2xl text-base
                 transition-all duration-150 active:bg-brand-800
                 disabled:opacity-60 disabled:cursor-not-allowed
                 shadow-lg shadow-brand-700/30 mt-1"
        >
          <span v-if="!loading">Masuk</span>
          <span v-else class="flex items-center justify-center gap-2">
            <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            Memverifikasi...
          </span>
        </button>
      </form>

      <p class="text-center text-xs text-gray-400 mt-6 leading-relaxed">
        Akun disiapkan oleh admin pesantren.<br>
        Hubungi admin jika belum memiliki akun.
      </p>
    </div><!-- end login card -->
  </div>
</template>

<script setup lang="ts">
import { reactive, ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useSantriStore } from '@/stores/santri'
import { UserIcon, LockClosedIcon, EyeIcon, EyeSlashIcon, ExclamationCircleIcon } from '@heroicons/vue/24/outline'
import { api } from '@/api/client'

const auth    = useAuthStore()
const santri  = useSantriStore()
const router  = useRouter()
const route   = useRoute()

const form         = reactive({ login: '', password: '' })
const loading      = ref(false)
const error        = ref('')
const showPass     = ref(false)
// true jika URL mengandung sso_token (redirect dari ERP)
const isSsoRedirect = computed(() => !!route.query.sso_token)

// ── SSO Token dari ERP ─────────────────────────────────────────
// Ketika wali login via erp.asy-syifaa.com, mereka di-redirect ke
// app.asy-syifaa.com?sso_token=TOKEN — kita terima token ini di sini.
onMounted(async () => {
  const ssoToken = route.query.sso_token as string | undefined
  if (!ssoToken) return

  // Tandai sedang loading agar UI tidak flicker
  loading.value = true
  error.value   = ''

  try {
    // Simpan token dulu (interceptor akan mengirimnya sebagai Bearer)
    localStorage.setItem('asf_token', ssoToken)

    // Validasi token — ambil data user dari API (baseURL sudah termasuk /api/v1)
    const res = await api.get('/auth/me')
    const user = res.data.user

    // Simpan ke auth store
    auth.setSession(user, ssoToken)

    // Bersihkan token dari URL agar tidak muncul di history/share
    window.history.replaceState({}, '', window.location.pathname)

    // Muat santri (non-kritis — jangan gagalkan sesi jika ini error)
    try { await santri.fetch() } catch { /* dashboard akan retry */ }

    router.replace('/dashboard')
  } catch {
    // Token expired / invalid — hapus dan minta login ulang
    localStorage.removeItem('asf_token')
    error.value = 'Sesi dari ERP tidak valid. Silakan login kembali.'
    loading.value = false
  }
})

// ── Login Manual ───────────────────────────────────────────────
async function handleLogin() {
  error.value   = ''
  loading.value = true
  try {
    await auth.login(form.login, form.password)
    await santri.fetch()
    router.replace('/dashboard')
  } catch (e: any) {
    error.value = e?.response?.data?.message ?? 'Login gagal. Periksa username dan password.'
  } finally {
    loading.value = false
  }
}
</script>
