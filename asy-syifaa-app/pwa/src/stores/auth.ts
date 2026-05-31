import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { authApi, type AuthUser } from '@/api/auth'
import { DEMO_USER } from '@/api/mock'

const DEMO = import.meta.env.VITE_DEMO_MODE === 'true'

export const useAuthStore = defineStore('auth', () => {
  const token = ref<string | null>(
    DEMO ? 'demo-token' : localStorage.getItem('asf_token')
  )
  const user  = ref<AuthUser | null>(
    DEMO ? (DEMO_USER as AuthUser) : JSON.parse(localStorage.getItem('asf_user') ?? 'null')
  )

  const isLoggedIn = computed(() => !!token.value && !!user.value)
  const namaWali   = computed(() => user.value?.full_name ?? user.value?.username ?? '')

  async function login(login: string, password: string) {
    if (DEMO) {
      token.value = 'demo-token'
      user.value  = DEMO_USER as AuthUser
      return
    }
    const res = await authApi.login({ login, password })
    token.value = res.token
    user.value  = res.user
    localStorage.setItem('asf_token', res.token)
    localStorage.setItem('asf_user',  JSON.stringify(res.user))
  }

  async function logout() {
    if (!DEMO) {
      try { await authApi.logout() } catch {}
      localStorage.removeItem('asf_token')
      localStorage.removeItem('asf_user')
    }
    token.value = null
    user.value  = null
  }

  function updateUser(data: Partial<AuthUser>) {
    if (user.value) user.value = { ...user.value, ...data }
    if (!DEMO) localStorage.setItem('asf_user', JSON.stringify(user.value))
  }

  /**
   * Dipanggil saat SSO redirect dari ERP — token sudah ada di localStorage,
   * user data dari /api/v1/auth/me. Simpan ke store.
   */
  function setSession(userData: AuthUser, rawToken: string) {
    token.value = rawToken
    user.value  = userData
    localStorage.setItem('asf_token', rawToken)
    localStorage.setItem('asf_user',  JSON.stringify(userData))
  }

  return { token, user, isLoggedIn, namaWali, login, logout, updateUser, setSession }
})
