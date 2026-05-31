<template>
  <div class="min-h-screen bg-[#F5F7FA] pb-nav">

    <!-- Header -->
    <div class="relative bg-gradient-to-br from-brand-700 to-brand-800 px-5 pt-12 pb-28 overflow-hidden">
      <div class="absolute -top-6 -right-6 w-32 h-32 rounded-full bg-white/5" />
      <div class="absolute bottom-2 left-4 w-16 h-16 rounded-full bg-white/5" />
      <div class="relative flex items-center gap-3">
        <button @click="$router.back()" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center active:bg-white/20">
          <ChevronLeftIcon class="w-5 h-5 text-white" />
        </button>
        <div>
          <p class="text-white/60 text-xs font-medium">Akun Saya</p>
          <h1 class="text-white font-bold text-lg">Profil & Pengaturan</h1>
        </div>
      </div>
    </div>

    <!-- Avatar Card -->
    <div class="relative z-10 px-4 -mt-16 mb-4">
      <div class="bg-white rounded-2xl shadow-lg shadow-brand-900/10 border border-white p-5 flex items-center gap-4">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-brand-600 to-brand-800
                    flex items-center justify-center flex-shrink-0">
          <span class="text-white font-black text-2xl">{{ initials }}</span>
        </div>
        <div class="flex-1 min-w-0">
          <p class="font-bold text-gray-800 text-lg leading-tight">{{ auth.user?.full_name }}</p>
          <p class="text-xs text-gray-400 mt-0.5">{{ auth.user?.roles?.[0] ?? 'Wali Santri' }}</p>
          <div class="flex items-center gap-1.5 mt-1.5">
            <div class="w-2 h-2 rounded-full bg-emerald-400" />
            <p class="text-xs text-emerald-600 font-medium">Akun Aktif</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Info Akun -->
    <div class="px-4 mb-4">
      <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 px-1">Informasi Akun</p>
      <div class="bg-white rounded-2xl border border-gray-100/80 shadow-sm overflow-hidden divide-y divide-gray-50">
        <InfoRow :icon="UserIcon" label="Nama Lengkap" :value="auth.user?.full_name ?? '—'" />
        <InfoRow :icon="AtSymbolIcon" label="Username" :value="auth.user?.username ?? '—'" />
        <InfoRow :icon="PhoneIcon" label="Nomor HP" :value="auth.user?.phone ?? '—'" />
      </div>
    </div>

    <!-- Pengaturan -->
    <div class="px-4 mb-4">
      <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 px-1">Pengaturan</p>
      <div class="bg-white rounded-2xl border border-gray-100/80 shadow-sm overflow-hidden divide-y divide-gray-50">

        <!-- Ganti Nomor HP -->
        <button @click="openSheet('phone')"
                class="w-full flex items-center gap-4 px-4 py-4 active:bg-gray-50 transition-colors">
          <div class="w-9 h-9 rounded-xl bg-sky-50 flex items-center justify-center flex-shrink-0">
            <PhoneIcon class="w-4 h-4 text-sky-500" />
          </div>
          <div class="flex-1 text-left">
            <p class="text-gray-800 font-semibold text-sm">Ganti Nomor HP</p>
            <p class="text-gray-400 text-xs">Perbarui nomor yang terdaftar</p>
          </div>
          <ChevronRightIcon class="w-4 h-4 text-gray-300" />
        </button>

        <!-- Ganti Password -->
        <button @click="openSheet('password')"
                class="w-full flex items-center gap-4 px-4 py-4 active:bg-gray-50 transition-colors">
          <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
            <LockClosedIcon class="w-4 h-4 text-amber-500" />
          </div>
          <div class="flex-1 text-left">
            <p class="text-gray-800 font-semibold text-sm">Ubah Password</p>
            <p class="text-gray-400 text-xs">Ganti kata sandi akun</p>
          </div>
          <ChevronRightIcon class="w-4 h-4 text-gray-300" />
        </button>

        <!-- Notifikasi -->
        <div class="flex items-center gap-4 px-4 py-4">
          <div class="w-9 h-9 rounded-xl bg-violet-50 flex items-center justify-center flex-shrink-0">
            <BellIcon class="w-4 h-4 text-violet-500" />
          </div>
          <div class="flex-1">
            <p class="text-gray-800 font-semibold text-sm">Notifikasi Push</p>
            <p class="text-gray-400 text-xs">Terima pemberitahuan real-time</p>
          </div>
          <button @click="notifOn = !notifOn"
                  class="w-12 h-6 rounded-full transition-colors duration-200 flex items-center px-0.5"
                  :class="notifOn ? 'bg-brand-600' : 'bg-gray-200'">
            <div class="w-5 h-5 rounded-full bg-white shadow transition-transform duration-200"
                 :class="notifOn ? 'translate-x-6' : 'translate-x-0'" />
          </button>
        </div>
      </div>
    </div>

    <!-- Tentang Aplikasi -->
    <div class="px-4 mb-4">
      <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 px-1">Tentang</p>
      <div class="bg-white rounded-2xl border border-gray-100/80 shadow-sm overflow-hidden divide-y divide-gray-50">
        <InfoRow :icon="BuildingLibraryIcon" label="Pesantren" value="Asy-Syifaa, Sumedang" />
        <InfoRow :icon="DevicePhoneMobileIcon" label="Versi Aplikasi" value="1.3.0 (Demo)" />
        <button class="w-full flex items-center gap-4 px-4 py-4 active:bg-gray-50">
          <div class="w-9 h-9 rounded-xl bg-gray-50 flex items-center justify-center flex-shrink-0">
            <ShieldCheckIcon class="w-4 h-4 text-gray-400" />
          </div>
          <div class="flex-1 text-left">
            <p class="text-gray-800 font-semibold text-sm">Kebijakan Privasi</p>
          </div>
          <ChevronRightIcon class="w-4 h-4 text-gray-300" />
        </button>
      </div>
    </div>

    <!-- Logout -->
    <div class="px-4 mb-8">
      <button @click="confirmLogout = true"
              class="w-full flex items-center justify-center gap-2 bg-red-50 border border-red-100
                     text-red-600 font-bold py-4 rounded-2xl active:bg-red-100 transition-colors">
        <ArrowRightOnRectangleIcon class="w-5 h-5" />
        Keluar dari Akun
      </button>
    </div>

    <!-- ── Backdrop ───────────────────────────────────────────── -->
    <Transition name="fade">
      <div v-if="sheet || confirmLogout" class="fixed inset-0 bg-black/50 z-[60]"
           @click="sheet = null; confirmLogout = false" />
    </Transition>

    <!-- ── Sheet: Ganti HP ────────────────────────────────────── -->
    <Transition name="sheet-up">
      <div v-if="sheet === 'phone'"
           class="fixed bottom-0 left-0 right-0 z-[70] bg-white rounded-t-3xl shadow-2xl px-5 pt-5"
           :style="{ paddingBottom: 'calc(1.5rem + env(safe-area-inset-bottom, 0px))' }">
        <div class="w-10 h-1 bg-gray-200 rounded-full mx-auto mb-5" />
        <div class="flex items-center gap-3 mb-5">
          <div class="w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center">
            <PhoneIcon class="w-5 h-5 text-sky-500" />
          </div>
          <div>
            <h3 class="text-gray-800 font-bold text-base">Ganti Nomor HP</h3>
            <p class="text-xs text-gray-400">Nomor saat ini: {{ auth.user?.phone }}</p>
          </div>
        </div>
        <div class="flex flex-col gap-3">
          <div>
            <label class="text-sm font-semibold text-gray-700 mb-1.5 block">Nomor HP Baru</label>
            <input v-model="phoneForm.phone" type="tel" inputmode="numeric"
                   placeholder="08xxxxxxxxxx"
                   class="w-full px-4 py-3.5 rounded-xl border border-gray-200 text-gray-800
                          text-base bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-500/30" />
          </div>
          <div>
            <label class="text-sm font-semibold text-gray-700 mb-1.5 block">Konfirmasi Password</label>
            <input v-model="phoneForm.password" type="password"
                   placeholder="Password saat ini"
                   class="w-full px-4 py-3.5 rounded-xl border border-gray-200 text-gray-800
                          text-base bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-500/30" />
          </div>
          <div v-if="formError" class="bg-red-50 border border-red-100 rounded-xl px-4 py-3 text-red-600 text-sm">{{ formError }}</div>
          <div v-if="formSuccess" class="bg-emerald-50 border border-emerald-100 rounded-xl px-4 py-3 text-emerald-700 text-sm font-medium">{{ formSuccess }}</div>
          <div class="flex gap-3 pt-1">
            <button @click="sheet = null" class="flex-1 py-3.5 rounded-2xl border border-gray-200 text-gray-700 font-semibold">Batal</button>
            <button @click="savePhone" :disabled="saving"
                    class="flex-1 py-3.5 rounded-2xl bg-brand-700 text-white font-bold disabled:opacity-60 active:bg-brand-800">
              {{ saving ? 'Menyimpan...' : 'Simpan' }}
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- ── Sheet: Ubah Password ───────────────────────────────── -->
    <Transition name="sheet-up">
      <div v-if="sheet === 'password'"
           class="fixed bottom-0 left-0 right-0 z-[70] bg-white rounded-t-3xl shadow-2xl px-5 pt-5"
           :style="{ paddingBottom: 'calc(1.5rem + env(safe-area-inset-bottom, 0px))' }">
        <div class="w-10 h-1 bg-gray-200 rounded-full mx-auto mb-5" />
        <div class="flex items-center gap-3 mb-5">
          <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
            <LockClosedIcon class="w-5 h-5 text-amber-500" />
          </div>
          <div>
            <h3 class="text-gray-800 font-bold text-base">Ubah Password</h3>
            <p class="text-xs text-gray-400">Gunakan password yang kuat</p>
          </div>
        </div>
        <div class="flex flex-col gap-3">
          <div>
            <label class="text-sm font-semibold text-gray-700 mb-1.5 block">Password Lama</label>
            <input v-model="passForm.old" type="password" placeholder="Password saat ini"
                   class="w-full px-4 py-3.5 rounded-xl border border-gray-200 text-gray-800
                          text-base bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-500/30" />
          </div>
          <div>
            <label class="text-sm font-semibold text-gray-700 mb-1.5 block">Password Baru</label>
            <input v-model="passForm.new" type="password" placeholder="Minimal 8 karakter"
                   class="w-full px-4 py-3.5 rounded-xl border border-gray-200 text-gray-800
                          text-base bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-500/30" />
          </div>
          <div>
            <label class="text-sm font-semibold text-gray-700 mb-1.5 block">Konfirmasi Password Baru</label>
            <input v-model="passForm.confirm" type="password" placeholder="Ulangi password baru"
                   class="w-full px-4 py-3.5 rounded-xl border border-gray-200 text-gray-800
                          text-base bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-500/30" />
          </div>
          <div v-if="formError" class="bg-red-50 border border-red-100 rounded-xl px-4 py-3 text-red-600 text-sm">{{ formError }}</div>
          <div v-if="formSuccess" class="bg-emerald-50 border border-emerald-100 rounded-xl px-4 py-3 text-emerald-700 text-sm font-medium">{{ formSuccess }}</div>
          <div class="flex gap-3 pt-1">
            <button @click="sheet = null" class="flex-1 py-3.5 rounded-2xl border border-gray-200 text-gray-700 font-semibold">Batal</button>
            <button @click="savePassword" :disabled="saving"
                    class="flex-1 py-3.5 rounded-2xl bg-brand-700 text-white font-bold disabled:opacity-60 active:bg-brand-800">
              {{ saving ? 'Menyimpan...' : 'Ubah' }}
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- ── Konfirmasi Logout ───────────────────────────────────── -->
    <Transition name="sheet-up">
      <div v-if="confirmLogout"
           class="fixed bottom-0 left-0 right-0 z-[70] bg-white rounded-t-3xl shadow-2xl px-5 pt-5"
           :style="{ paddingBottom: 'calc(1.5rem + env(safe-area-inset-bottom, 0px))' }">
        <div class="w-10 h-1 bg-gray-200 rounded-full mx-auto mb-5" />
        <div class="text-center mb-6">
          <div class="w-16 h-16 rounded-2xl bg-red-50 flex items-center justify-center mx-auto mb-4">
            <ArrowRightOnRectangleIcon class="w-8 h-8 text-red-500" />
          </div>
          <h3 class="text-gray-800 font-bold text-lg mb-1">Keluar dari Akun?</h3>
          <p class="text-gray-400 text-sm">Anda harus login kembali untuk mengakses aplikasi</p>
        </div>
        <div class="flex gap-3">
          <button @click="confirmLogout = false"
                  class="flex-1 py-4 rounded-2xl border border-gray-200 text-gray-700 font-semibold">
            Batal
          </button>
          <button @click="doLogout"
                  class="flex-1 py-4 rounded-2xl bg-red-600 text-white font-bold active:bg-red-700">
            Ya, Keluar
          </button>
        </div>
      </div>
    </Transition>

  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { defineComponent, h } from 'vue'
import {
  ChevronLeftIcon, ChevronRightIcon,
  UserIcon, AtSymbolIcon, PhoneIcon, LockClosedIcon,
  BellIcon, ShieldCheckIcon,
  BuildingLibraryIcon, DevicePhoneMobileIcon,
  ArrowRightOnRectangleIcon,
} from '@heroicons/vue/24/outline'

// ── InfoRow inline component ───────────────────────────────────
const InfoRow = defineComponent({
  props: { icon: Object, label: String, value: String },
  setup(props) {
    return () => h('div', { class: 'flex items-center gap-4 px-4 py-4' }, [
      h('div', { class: 'w-9 h-9 rounded-xl bg-gray-50 flex items-center justify-center flex-shrink-0' },
        [h(props.icon as any, { class: 'w-4 h-4 text-gray-400' })]),
      h('div', { class: 'flex-1 min-w-0' }, [
        h('p', { class: 'text-[10px] text-gray-400 leading-none mb-0.5' }, props.label),
        h('p', { class: 'text-sm font-semibold text-gray-800 truncate' }, props.value),
      ]),
    ])
  },
})

const auth    = useAuthStore()
const router  = useRouter()

const initials = computed(() =>
  (auth.user?.full_name ?? 'W').split(' ').slice(0, 2).map((w: string) => w[0]).join('').toUpperCase()
)

const sheet         = ref<null | 'phone' | 'password'>(null)
const confirmLogout = ref(false)
const notifOn       = ref(true)
const saving        = ref(false)
const formError     = ref('')
const formSuccess   = ref('')

const phoneForm = ref({ phone: '', password: '' })
const passForm  = ref({ old: '', new: '', confirm: '' })

function openSheet(type: 'phone' | 'password') {
  formError.value   = ''
  formSuccess.value = ''
  if (type === 'phone') phoneForm.value = { phone: auth.user?.phone ?? '', password: '' }
  if (type === 'password') passForm.value = { old: '', new: '', confirm: '' }
  sheet.value = type
}

async function savePhone() {
  formError.value = ''
  if (!phoneForm.value.phone || phoneForm.value.phone.length < 10) {
    formError.value = 'Masukkan nomor HP yang valid (min. 10 digit)'
    return
  }
  saving.value = true
  await new Promise(r => setTimeout(r, 800))
  // Demo: simulasi berhasil
  auth.updateUser({ phone: phoneForm.value.phone })
  formSuccess.value = 'Nomor HP berhasil diperbarui!'
  saving.value = false
  setTimeout(() => { sheet.value = null }, 1500)
}

async function savePassword() {
  formError.value = ''
  if (!passForm.value.old) { formError.value = 'Masukkan password lama'; return }
  if (passForm.value.new.length < 8) { formError.value = 'Password baru minimal 8 karakter'; return }
  if (passForm.value.new !== passForm.value.confirm) { formError.value = 'Konfirmasi password tidak cocok'; return }
  saving.value = true
  await new Promise(r => setTimeout(r, 800))
  formSuccess.value = 'Password berhasil diubah!'
  saving.value = false
  setTimeout(() => { sheet.value = null }, 1500)
}

async function doLogout() {
  await auth.logout()
  router.push('/login')
}
</script>
