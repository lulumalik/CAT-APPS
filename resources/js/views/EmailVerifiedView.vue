<template>
  <main class="min-h-screen flex items-center justify-center px-4 py-12">
    <div
      class="w-full max-w-md rounded-[2rem] border border-gray-100 bg-white p-8 text-center shadow-xl shadow-black/5">
      <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full" :class="iconWrapClass">
        <component :is="statusIcon" class="h-10 w-10" :class="iconClass" />
      </div>

      <h1 class="mt-6 text-2xl font-black text-[#1A1A1A]">{{ content.title }}</h1>
      <p class="mt-3 text-sm leading-relaxed text-gray-600">{{ content.message }}</p>

      <div v-if="isSuccessState" class="mt-6 rounded-2xl bg-emerald-50 border border-emerald-100 px-4 py-3">
        <p class="text-sm font-semibold text-emerald-700">
          Mengalihkan ke {{ redirectLabel }} dalam {{ countdown }} detik...
        </p>
      </div>

      <div class="mt-7 flex flex-col gap-3">
        <button type="button" @click="goNow"
          class="w-full rounded-full bg-[#123B8F] px-6 py-3 text-sm font-bold text-white transition hover:bg-[#0d2c6b]">
          {{ isSuccessState ? `Buka ${redirectLabel} Sekarang` : 'Lanjutkan' }}
        </button>
        <router-link to="/"
          class="w-full rounded-full border border-gray-200 px-6 py-3 text-sm font-semibold text-gray-600 transition hover:bg-gray-50">
          Kembali ke Beranda
        </router-link>
      </div>
    </div>
  </main>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { CheckCircle2, AlertTriangle, MailCheck } from 'lucide-vue-next'
import { useAppStore } from '@/stores/app'

const route = useRoute()
const router = useRouter()
const store = useAppStore()

const status = computed(() => String(route.query.status || 'success'))
const isSuccessState = computed(() => status.value === 'success' || status.value === 'already')

const redirectTarget = ref('/login')
const redirectLabel = computed(() => (redirectTarget.value === '/profile' ? 'Profil' : 'Halaman Masuk'))

const STATUS_CONTENT = {
  success: {
    title: 'Verifikasi Berhasil!',
    message: 'Alamat email Anda telah berhasil diverifikasi. Terima kasih telah menyelesaikan langkah ini.',
  },
  already: {
    title: 'Email Sudah Terverifikasi',
    message: 'Alamat email Anda memang sudah terverifikasi sebelumnya. Anda dapat melanjutkan seperti biasa.',
  },
  expired: {
    title: 'Tautan Kedaluwarsa',
    message: 'Tautan verifikasi sudah tidak berlaku. Silakan masuk dan kirim ulang email verifikasi dari halaman profil Anda.',
  },
  invalid: {
    title: 'Tautan Tidak Valid',
    message: 'Tautan verifikasi tidak valid. Silakan masuk dan kirim ulang email verifikasi dari halaman profil Anda.',
  },
}

const content = computed(() => STATUS_CONTENT[status.value] || STATUS_CONTENT.success)

const statusIcon = computed(() => {
  if (status.value === 'success') return CheckCircle2
  if (status.value === 'already') return MailCheck
  return AlertTriangle
})

const iconWrapClass = computed(() => (isSuccessState.value ? 'bg-emerald-50' : 'bg-amber-50'))
const iconClass = computed(() => (isSuccessState.value ? 'text-emerald-500' : 'text-amber-500'))

const countdown = ref(4)
let timer = null

const goNow = () => {
  if (timer) clearInterval(timer)
  router.replace(isSuccessState.value ? redirectTarget.value : '/login')
}

onMounted(async () => {
  if (!store.isAuthChecked) {
    await store.fetchUser()
  }
  redirectTarget.value = store.isAuthenticated ? '/profile' : '/login'

  if (isSuccessState.value) {
    timer = setInterval(() => {
      countdown.value -= 1
      if (countdown.value <= 0) {
        goNow()
      }
    }, 1000)
  }
})

onUnmounted(() => {
  if (timer) clearInterval(timer)
})
</script>
