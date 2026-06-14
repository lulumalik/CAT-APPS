<template>
  <main class="bg-background h-screen overflow-auto rounded-3xl md:flex md:items-center px-4 py-4 md:px-8">
    <div class="auth-shell">
      <section class="auth-right-pane">
        <span class="auth-visual-stripe" />
        <img src="../../assets/logo.png" alt="Login" class="w-44 relative z-20 mx-auto top-6 md:absolute md:top-1/2 md:left-1/2 md:-translate-x-1/2 md:-translate-y-1/2 z-30 object-cover" />
      </section>

      <section class="auth-left-pane">
        <header class="auth-brand-nav">
          <nav class="auth-nav-links justify-center w-full">
            <router-link to="/" class="auth-nav-link">Beranda</router-link>
            <router-link to="/about-us" class="auth-nav-link">Tentang Kami</router-link>
          </nav>
        </header>

        <section class="auth-form-wrap text-center flex flex-col items-center">
          <h1 class="text-[1.5rem] md:text-[2.7rem] font-extrabold tracking-tight leading-none text-[#333]">{{ t('auth.signup.title') }}</h1>

          <div v-if="error" class="mt-4 w-full rounded-xl border border-red-100 bg-red-50 p-3 text-sm text-red-700 flex items-center gap-2">
            <CircleAlert class="h-4 w-4" />
            {{ error }}
          </div>

          <form class="mt-7 w-full space-y-3.5" @submit.prevent="onSubmit">
            <select
              id="program_category"
              v-model="programCategory"
              required
              class="auth-input"
            >
              <option v-for="p in ONLINE_PROGRAMS" :key="p.value" :value="p.value">
                {{ programSignupOptionLabel(p) }}
              </option>
            </select>

            <input
              id="name"
              v-model="name"
              name="name"
              type="text"
              autocomplete="name"
              required
              class="auth-input"
              :placeholder="t('auth.signup.nameLabel')"
            />

            <input
              id="username"
              v-model="username"
              name="username"
              type="text"
              autocomplete="username"
              required
              class="auth-input"
              :placeholder="t('auth.signup.usernameLabel')"
            />

            <input
              id="email"
              v-model="email"
              name="email"
              type="email"
              autocomplete="email"
              required
              class="auth-input"
              :placeholder="t('auth.signup.emailLabel')"
            />

            <div
              class="flex w-full items-center overflow-hidden rounded-full border border-[#c8bfd8] bg-white transition-colors focus-within:border-[#6f2fc9] focus-within:shadow-[0_0_0_3px_rgba(111,47,201,0.16)]"
            >
              <span class="shrink-0 border-r border-[#c8bfd8] px-4 py-3 text-sm text-gray-500 select-none">+62</span>
              <input
                id="whatsapp"
                v-model="whatsapp"
                name="whatsapp"
                type="text"
                inputmode="numeric"
                autocomplete="tel-national"
                required
                :placeholder="t('auth.signup.whatsappLabel')"
                class="w-full min-w-0 border-0 bg-transparent px-4 py-3 text-sm text-[#2f223f] focus:outline-none focus:ring-0"
              />
            </div>
            <p v-if="whatsapp && !isValidPhoneLocal(whatsapp)" class="w-full text-left text-xs text-red-500 -mt-1">
              Nomor WhatsApp tidak valid. Contoh: 812345678
            </p>

            <div
              class="flex w-full items-center overflow-hidden rounded-full border border-[#c8bfd8] bg-white transition-colors focus-within:border-[#6f2fc9] focus-within:shadow-[0_0_0_3px_rgba(111,47,201,0.16)]"
            >
              <span class="shrink-0 border-r border-[#c8bfd8] px-4 py-3 text-sm text-gray-500 select-none">+62</span>
              <input
                id="parent_phone"
                v-model="parentPhone"
                name="parent_phone"
                type="text"
                inputmode="numeric"
                autocomplete="tel-national"
                required
                :placeholder="t('auth.signup.parentPhoneLabel')"
                class="w-full min-w-0 border-0 bg-transparent px-4 py-3 text-sm text-[#2f223f] focus:outline-none focus:ring-0"
              />
            </div>
            <p v-if="parentPhone && !isValidPhoneLocal(parentPhone)" class="w-full text-left text-xs text-red-500 -mt-1">
              Nomor telepon orang tua tidak valid. Contoh: 812345678
            </p>

            <input
              id="password"
              v-model="password"
              name="password"
              type="password"
              required
              class="auth-input"
              :placeholder="t('auth.signup.passwordLabel')"
            />

            <button
              type="submit"
              :disabled="loading"
              class="auth-submit-btn mt-2"
            >
              {{ loading ? t('auth.signup.submitting') : t('auth.signup.submit') }}
            </button>
          </form>

          <p class="mt-5 text-sm text-[#4a3e5e] text-center">
            {{ t('auth.signup.haveAccount') }}
            <router-link to="/login" class="font-semibold text-[#333] hover:text-[#30085c]">{{ t('auth.signup.signIn') }}</router-link>
          </p>
        </section>
      </section>
    </div>
  </main>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { CircleAlert } from 'lucide-vue-next'
import { useAppStore } from '@/stores/app'
import { useToast } from '@/composables/useNotification'
import { useI18n } from '@/composables/useI18n'
import { ONLINE_PROGRAMS, programSignupOptionLabel } from '@/constants/onlinePrograms'

const programCategory = ref('regular')
const name = ref('')
const username = ref('')
const email = ref('')
const whatsapp = ref('')
const parentPhone = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')
const router = useRouter()
const store = useAppStore()
const toast = useToast()
const { t } = useI18n()

function normalizePhoneLocal(rawPhone) {
  const digitsOnly = String(rawPhone || '').replace(/\D/g, '')
  if (digitsOnly.startsWith('62')) return digitsOnly.slice(2)
  if (digitsOnly.startsWith('0')) return digitsOnly.slice(1)
  return digitsOnly
}

function isValidPhoneLocal(localPhone) {
  return /^8\d{8,11}$/.test(String(localPhone || ''))
}

function formatPhoneForBackend(localPhone) {
  const normalized = normalizePhoneLocal(localPhone)
  return normalized ? `62${normalized}` : ''
}

watch(whatsapp, (value) => {
  const normalized = normalizePhoneLocal(value)
  if (value !== normalized) whatsapp.value = normalized
})

watch(parentPhone, (value) => {
  const normalized = normalizePhoneLocal(value)
  if (value !== normalized) parentPhone.value = normalized
})

const onSubmit = async () => {
  loading.value = true
  error.value = ''

  if (!isValidPhoneLocal(whatsapp.value) || !isValidPhoneLocal(parentPhone.value)) {
    loading.value = false
    error.value = 'Format nomor WhatsApp atau telepon orang tua tidak valid. Gunakan format seperti 812345678.'
    return
  }

  const result = await store.register({
    program_category: programCategory.value,
    name: name.value,
    username: username.value,
    email: email.value,
    whatsapp: formatPhoneForBackend(whatsapp.value),
    phone: formatPhoneForBackend(parentPhone.value),
    password: password.value,
  })

  loading.value = false

  if (result.success) {
    toast.success(
      t('auth.signup.toastSuccessTitle'),
      result.message || t('auth.signup.toastSuccessMessage')
    )
    setTimeout(() => {
      router.push('/registration')
    }, 1000)
  } else {
    error.value = result.message || t('auth.signup.genericFailedMessage')
    toast.error(t('auth.signup.toastFailedTitle'), result.message || t('auth.signup.genericFailedMessage'))
  }
}
</script>

