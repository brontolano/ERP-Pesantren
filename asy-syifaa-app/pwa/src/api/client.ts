import axios from 'axios'

const API_BASE = import.meta.env.VITE_API_BASE ?? 'https://api.asy-syifaa.com'

export const api = axios.create({
  baseURL: `${API_BASE}/api/v1`,
  headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
  timeout: 15000,
})

// Inject token dari localStorage pada setiap request
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('asf_token')
  if (token) config.headers.Authorization = `Bearer ${token}`
  return config
})

// Handle 401 — token expired / invalid
api.interceptors.response.use(
  (res) => res,
  (err) => {
    if (err.response?.status === 401) {
      localStorage.removeItem('asf_token')
      localStorage.removeItem('asf_user')
      window.location.href = '/login'
    }
    return Promise.reject(err)
  }
)
