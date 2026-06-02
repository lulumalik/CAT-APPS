<template>
  <main class="bg-background h-screen overflow-hidden px-4 py-4 md:px-8">
    <div class="auth-shell">
      <section class="auth-left-pane">
        <header class="auth-brand-nav">
          <nav class="auth-nav-links justify-center w-full">
            <router-link to="/" class="auth-nav-link">Beranda</router-link>
            <router-link to="/about-us" class="auth-nav-link">Tentang Kami</router-link>
          </nav>
        </header>

        <section class="auth-form-wrap text-center flex flex-col items-center">
          <h1 class="text-[2.7rem] font-extrabold tracking-tight leading-none text-[#40136c]">{{ t('auth.signup.title') }}</h1>

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
            <router-link to="/login" class="font-semibold text-[#541197] hover:text-[#30085c]">{{ t('auth.signup.signIn') }}</router-link>
          </p>
        </section>
      </section>

      <section class="auth-right-pane">
        <span class="auth-visual-stripe" />
        <img src="../../assets/logo.png" alt="Login" class="w-72 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-30 object-cover" />
      </section>
    </div>
  </main>
</template>

<script setup>
import { ref } from 'vue'
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
const password = ref('')
const loading = ref(false)
const error = ref('')
const router = useRouter()
const store = useAppStore()
const toast = useToast()
const { t } = useI18n()

const onSubmit = async () => {
  loading.value = true
  error.value = ''

  const result = await store.register({
    program_category: programCategory.value,
    name: name.value,
    username: username.value,
    email: email.value,
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

