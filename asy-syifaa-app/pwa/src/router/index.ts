import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    // Auth
    {
      path: '/login',
      name: 'login',
      component: () => import('@/views/auth/LoginView.vue'),
      meta: { guest: true },
    },

    // App (protected)
    {
      path: '/',
      component: () => import('@/views/AppLayout.vue'),
      meta: { requiresAuth: true },
      children: [
        { path: '', redirect: '/dashboard' },

        // ── Beranda ───────────────────────────────────────────
        { path: 'dashboard',      name: 'dashboard',     component: () => import('@/views/dashboard/DashboardView.vue') },
        { path: 'profil',         name: 'profil',        component: () => import('@/views/profil/ProfilView.vue') },
        { path: 'santri/detail',  name: 'santri-detail', component: () => import('@/views/santri/SantriDetailView.vue') },
        { path: 'marketplace',    name: 'marketplace',   component: () => import('@/views/marketplace/MarketplaceView.vue') },

        // ── Keuangan Hub ──────────────────────────────────────
        { path: 'keuangan',           name: 'keuangan',   component: () => import('@/views/keuangan/KeuanganView.vue') },
        { path: 'keuangan/tabungan',  name: 'tabungan',   component: () => import('@/views/keuangan/TabunganView.vue') },
        { path: 'keuangan/transaksi', name: 'transaksi',  component: () => import('@/views/keuangan/TransaksiView.vue') },
        { path: 'keuangan/tagihan',   name: 'tagihan',    component: () => import('@/views/keuangan/TagihanView.vue') },
        { path: 'keuangan/bayar',     name: 'bayar',      component: () => import('@/views/keuangan/PembayaranView.vue') },

        // ── Belajar Hub ───────────────────────────────────────
        { path: 'belajar',          name: 'belajar',   component: () => import('@/views/belajar/BelajarView.vue') },
        { path: 'belajar/hafalan',  name: 'hafalan',   component: () => import('@/views/hafalan/HafalanView.vue') },
        { path: 'belajar/absensi',  name: 'absensi',   component: () => import('@/views/belajar/AbsensiView.vue') },
        { path: 'belajar/jadwal',   name: 'jadwal',    component: () => import('@/views/belajar/JadwalView.vue') },
        { path: 'belajar/prestasi', name: 'prestasi',  component: () => import('@/views/belajar/PrestasiView.vue') },
        { path: 'belajar/akademik', name: 'akademik',  component: () => import('@/views/belajar/AkademikView.vue') },

        // ── Kegiatan Hub ──────────────────────────────────────
        { path: 'kegiatan',             name: 'kegiatan',   component: () => import('@/views/kegiatan/KegiatanView.vue') },
        { path: 'kegiatan/izin',        name: 'izin',       component: () => import('@/views/izin/IzinView.vue') },
        { path: 'kegiatan/kesehatan',   name: 'kesehatan',  component: () => import('@/views/kegiatan/KesehatanView.vue') },
        { path: 'kegiatan/kunjungan',   name: 'kunjungan',  component: () => import('@/views/kegiatan/KunjunganView.vue') },
        { path: 'kegiatan/konseling',   name: 'konseling',  component: () => import('@/views/kegiatan/KonselingView.vue') },
        { path: 'kegiatan/presensi',    name: 'presensi',   component: () => import('@/views/kegiatan/PresensiView.vue') },

        // ── Info ──────────────────────────────────────────────
        { path: 'info', name: 'info', component: () => import('@/views/info/InfoView.vue') },
      ],
    },

    { path: '/:pathMatch(.*)*', redirect: '/' },
  ],
})

router.beforeEach((to, _from) => {
  const auth = useAuthStore()
  if (to.meta.requiresAuth && !auth.isLoggedIn) return '/login'
  if (to.meta.guest && auth.isLoggedIn) return '/dashboard'
})

export default router
