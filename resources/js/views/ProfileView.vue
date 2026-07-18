<template>
  <main class="max-w-4xl mx-auto px-4 py-8 space-y-6">
    <PageHeroHeader
      title="Profil Pendaftar"
      subtitle="Kelola data akun dan informasi pendaftaran Anda."
      theme="slate"
      :icon="UserCircle"
    />

    <div
      v-if="isAppExpired(user)"
      class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800"
    >
      Masa aktif aplikasi Anda telah berakhir. Anda hanya dapat mengakses profil dan
      <router-link to="/activity-history" class="font-semibold underline">riwayat aktivitas</router-link>.
    </div>

    <div v-if="loading" class="py-20 text-center text-gray-500">Memuat profil...</div>
    <div v-else-if="errorMessage" class="rounded-2xl border border-red-100 bg-red-50 p-6 text-sm text-red-700">
      {{ errorMessage }}
    </div>

    <template v-else>
      <section class="rounded-[2rem] bg-white border border-gray-100 shadow-lg shadow-black/5 p-6 overflow-hidden">
        <h2 class="font-semibold text-lg mb-4">Data Akun</h2>
        <div v-if="approvedPhotoUrl" class="mb-4 flex items-center gap-3">
          <img :src="approvedPhotoUrl" alt="Foto profil" class="w-16 h-16 rounded-full object-cover border border-gray-200" />
          <div class="text-sm text-gray-600">Foto profil aktif (administrasi disetujui)</div>
        </div>
        <div class="overflow-x-auto -mx-1">
          <table class="w-full text-sm border-collapse">
            <tbody>
              <tr class="border-b border-gray-100">
                <th class="text-left font-medium text-gray-500 py-3 pr-4 w-[40%] align-top">Nama</th>
                <td class="py-3 font-medium text-[#1A1A1A]">{{ user?.name || '-' }}</td>
              </tr>
              <tr class="border-b border-gray-100">
                <th class="text-left font-medium text-gray-500 py-3 pr-4 align-top">Email</th>
                <td class="py-3 font-medium text-[#1A1A1A]">{{ user?.email || '-' }}</td>
              </tr>
              <tr class="border-b border-gray-100">
                <th class="text-left font-medium text-gray-500 py-3 pr-4 align-top">Role</th>
                <td class="py-3 font-medium capitalize text-[#1A1A1A]">{{ user?.role || '-' }}</td>
              </tr>
              <tr class="border-b border-gray-100">
                <th class="text-left font-medium text-gray-500 py-3 pr-4 align-top">Tier</th>
                <td class="py-3">
                  <span class="text-xs rounded-full px-2 py-1" :class="programBadge.className">{{ programBadge.label }}</span>
                </td>
              </tr>
              <tr class="border-b border-gray-100">
                <th class="text-left font-medium text-gray-500 py-3 pr-4 align-top">Program</th>
                <td class="py-3 font-medium text-[#1A1A1A]">{{ programLabel }}</td>
              </tr>
              <tr class="border-b border-gray-100">
                <th class="text-left font-medium text-gray-500 py-3 pr-4 align-top">Karantina</th>
                <td class="py-3 font-medium text-[#1A1A1A]">{{ quarantineLabel }}</td>
              </tr>
              <tr v-if="user?.role === 'user'">
                <th class="text-left font-medium text-gray-500 py-3 pr-4 align-top">Masa Aktif Aplikasi</th>
                <td class="py-3 font-medium text-[#1A1A1A]">{{ appExpiresDisplay }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

      <section class="rounded-[2rem] bg-white border border-gray-100 shadow-lg shadow-black/5 p-6">
        <div class="flex items-start justify-between gap-4 flex-wrap">
          <div>
            <h2 class="font-semibold text-lg">Verifikasi Email</h2>
            <p class="mt-1 text-sm text-gray-500">{{ user?.email || '-' }}</p>
          </div>
          <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full"
            :class="isEmailVerified ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-amber-50 text-amber-700 border border-amber-100'">
            <component :is="isEmailVerified ? CheckCircle2 : AlertTriangle" class="h-4 w-4" />
            {{ isEmailVerified ? 'Terverifikasi' : 'Belum terverifikasi' }}
          </span>
        </div>

        <div v-if="!isEmailVerified" class="mt-5 rounded-2xl bg-amber-50/60 border border-amber-100 p-4">
          <p class="text-sm text-amber-800 leading-relaxed">
            Email Anda belum terverifikasi. Silakan cek kotak masuk (dan folder spam) untuk tautan verifikasi, atau
            kirim ulang di bawah ini.
          </p>
          <div class="mt-4 flex items-center gap-3 flex-wrap">
            <button type="button" :disabled="resendDisabled" @click="resendVerification"
              class="inline-flex items-center gap-2 rounded-full px-5 py-2.5 text-sm font-semibold text-white transition"
              :class="resendDisabled ? 'bg-gray-300 cursor-not-allowed' : 'bg-[#123B8F] hover:bg-[#0d2c6b]'">
              <Send v-if="!isSending" class="h-4 w-4" />
              <Loader2 v-else class="h-4 w-4 animate-spin" />
              <span v-if="cooldownRemaining > 0">Kirim ulang dalam {{ cooldownRemaining }}s</span>
              <span v-else-if="isSending">Mengirim...</span>
              <span v-else>Kirim Ulang Email Verifikasi</span>
            </button>
          </div>
        </div>
        <p v-else class="mt-4 text-sm text-emerald-700">
          Terima kasih, email Anda sudah terverifikasi.
        </p>
      </section>

      <section v-if="isSimplifiedProgram" class="rounded-[2rem] bg-white border border-gray-100 shadow-lg shadow-black/5 p-6 space-y-4">
        <div class="flex items-start justify-between gap-4 flex-wrap">
          <div>
            <h2 class="font-semibold text-lg">Pembayaran Program</h2>
            <p class="mt-1 text-sm text-gray-500">Kelas Online dan Kelas Ujian memerlukan konfirmasi pembayaran oleh admin.</p>
          </div>
          <span
            class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full"
            :class="paymentConfirmed ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-amber-50 text-amber-700 border border-amber-100'"
          >
            <component :is="paymentConfirmed ? CheckCircle2 : AlertTriangle" class="h-4 w-4" />
            {{ paymentConfirmed ? 'Sudah dibayar' : 'Menunggu konfirmasi' }}
          </span>
        </div>

        <p v-if="!isEmailVerified" class="text-sm text-amber-800 bg-amber-50 border border-amber-100 rounded-xl px-4 py-3">
          Verifikasi email terlebih dahulu sebelum menghubungi admin untuk pembayaran.
        </p>
        <ProgramAdminContactCard
          v-else-if="!paymentConfirmed"
          :whatsapp-message="adminWhatsAppMessage"
        />
        <p v-else class="text-sm text-emerald-700">
          Admin telah mengonfirmasi pembayaran Anda. Fitur program sekarang aktif.
        </p>
      </section>
    </template>
  </main>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue'
import axios from 'axios'
import { useAppStore } from '@/stores/app'
import { storeToRefs } from 'pinia'
import { CheckCircle2, AlertTriangle, Send, Loader2, UserCircle } from 'lucide-vue-next'
import PageHeroHeader from '@/components/PageHeroHeader.vue'
import ProgramAdminContactCard from '@/components/ProgramAdminContactCard.vue'
import { getProgramBadge, programCategoryLabel, supportsProgramQuarantine, formatAppExpiresAt, isAppExpired, usesSimplifiedOnboarding, normalizeProgramCategory } from '@/utils/userMeta'
import { registrationFileHref } from '@/utils/storageUrl'
import { getCookie, setCookie } from '@/utils/cookies'
import { useToast } from '@/composables/useNotification'

const store = useAppStore()
const { user } = storeToRefs(store)
const toast = useToast()

const loading = ref(true)
const errorMessage = ref('')
const progress = ref(null)

const RESEND_COOLDOWN_SECONDS = 60
const RESEND_COOKIE = 'email_verify_resend_until'
const isSending = ref(false)
const cooldownRemaining = ref(0)
let cooldownTimer = null

const isEmailVerified = computed(() => Boolean(user.value?.email_verified_at))
const isSimplifiedProgram = computed(() => usesSimplifiedOnboarding(user.value))
const paymentConfirmed = computed(() => Boolean(progress.value?.payment_confirmed))
const appExpiresDisplay = computed(() => {
  if (user.value?.app_expires_at) {
    return formatAppExpiresAt(user.value.app_expires_at)
  }
  if (usesSimplifiedOnboarding(user.value)) {
    const months = normalizeProgramCategory(user.value?.program_category) === 'try_out' ? 3 : 6
    return `Belum aktif — ${months} bulan setelah pembayaran dikonfirmasi admin`
  }
  return 'Tidak dibatasi'
})
const adminWhatsAppMessage = computed(() => {
  const label = programLabel.value
  return `Halo admin, saya ${user.value?.name || 'peserta'} mendaftar ${label}. Mohon info biaya dan cara pembayaran.`
})
const resendDisabled = computed(() => isSending.value || cooldownRemaining.value > 0)

const startCooldownFrom = (untilMs) => {
  if (cooldownTimer) clearInterval(cooldownTimer)
  const tick = () => {
    const remaining = Math.ceil((untilMs - Date.now()) / 1000)
    cooldownRemaining.value = remaining > 0 ? remaining : 0
    if (cooldownRemaining.value <= 0 && cooldownTimer) {
      clearInterval(cooldownTimer)
      cooldownTimer = null
    }
  }
  tick()
  if (cooldownRemaining.value > 0) {
    cooldownTimer = setInterval(tick, 1000)
  }
}

const resendVerification = async () => {
  if (resendDisabled.value) return
  isSending.value = true
  try {
    const { data } = await axios.post('/api/email/verification-notification')
    const untilMs = Date.now() + RESEND_COOLDOWN_SECONDS * 1000
    setCookie(RESEND_COOKIE, String(untilMs), untilMs)
    startCooldownFrom(untilMs)
    toast.success('Terkirim', data?.message || 'Email verifikasi telah dikirim ulang.')
  } catch (error) {
    const status = error?.response?.status
    if (status === 429) {
      const untilMs = Date.now() + RESEND_COOLDOWN_SECONDS * 1000
      setCookie(RESEND_COOKIE, String(untilMs), untilMs)
      startCooldownFrom(untilMs)
      toast.warning('Terlalu sering', 'Mohon tunggu sebelum mengirim ulang lagi.')
    } else {
      toast.error('Gagal', error?.response?.data?.message || 'Tidak dapat mengirim email verifikasi.')
    }
  } finally {
    isSending.value = false
  }
}
const programBadge = computed(() => getProgramBadge(user.value))
const programLabel = computed(() => programCategoryLabel(user.value?.program_category))
const quarantineLabel = computed(() => {
  if (!supportsProgramQuarantine(user.value?.program_category)) {
    return 'Tidak berlaku'
  }
  return user.value?.in_quarantine ? 'Ya' : 'Tidak'
})

const approvedPhotoUrl = computed(() => {
  if (progress.value?.administration_status !== 'approved') return ''
  const d = progress.value?.administration_data
  if (!d) return ''
  const isPassportUploaded = Boolean(progress.value?.administration_files_present?.passport_photo_path)
  if (isPassportUploaded && d.passport_photo_path) {
    return registrationFileHref(progress.value, 'passport_photo_path') || ''
  }
  if (typeof d.passport_photo_url === 'string' && /^https?:\/\//i.test(d.passport_photo_url)) return d.passport_photo_url
  if (typeof d.profile_photo_url === 'string' && /^https?:\/\//i.test(d.profile_photo_url)) return d.profile_photo_url
  return ''
})

onMounted(async () => {
  loading.value = true

  const savedUntil = Number(getCookie(RESEND_COOKIE))
  if (savedUntil && savedUntil > Date.now()) {
    startCooldownFrom(savedUntil)
  }

  try {
    await store.fetchUser()
    const { data } = await axios.get('/api/my-registration')
    progress.value = data
    errorMessage.value = ''
  } catch (error) {
    errorMessage.value = error?.response?.data?.message || 'Gagal memuat data profil.'
  } finally {
    loading.value = false
  }
})

onUnmounted(() => {
  if (cooldownTimer) clearInterval(cooldownTimer)
})
</script>
