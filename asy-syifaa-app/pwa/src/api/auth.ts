import { api } from './client'

export interface LoginPayload { login: string; password: string }
export interface AuthUser {
  id: number; username: string; full_name: string; phone: string; roles: string[]
}
export interface AuthResponse {
  ok: boolean; token: string; user: AuthUser; redirect: string
}

export const authApi = {
  login: (payload: LoginPayload) =>
    api.post<AuthResponse>('/auth/login', payload).then(r => r.data),

  me: () =>
    api.get<{ ok: boolean; user: AuthUser }>('/auth/me').then(r => r.data),

  logout: () =>
    api.post('/auth/logout'),
}
