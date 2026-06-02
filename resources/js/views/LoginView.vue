<template>
  <main class="bg-background h-screen overflow-hidden px-4 py-4 md:px-8">
    <div class="auth-shell">
      <section class="auth-left-pane">
        <header class="auth-brand-nav">
          <nav class="auth-nav-links w-full justify-center">
            <router-link to="/" class="auth-nav-link">Beranda</router-link>
            <router-link to="/about-us" class="auth-nav-link">Tentang Kami</router-link>
          </nav>
        </header>

        <section class="auth-form-wrap text-center flex flex-col items-center md:pt-12">
          <h1 class="text-[2.5rem] md:text-[3rem] font-extrabold tracking-tight leading-none text-[#40136c]">{{ t('auth.login.title') }}</h1>

          <div v-if="error" class="mt-4 w-full rounded-xl border border-red-100 bg-red-50 p-3 text-sm text-red-700 flex items-center gap-2">
            <CircleAlert class="h-4 w-4" />
            {{ error }}
          </div>

          <form class="mt-8 w-full space-y-4" @submit.prevent="onSubmit">
            <div>
              <input
                id="username"
                v-model="username"
                name="username"
                type="text"
                autocomplete="username"
                required
                class="auth-input"
                :placeholder="t('auth.login.usernameLabel')"
              />
            </div>
            <div>
              <input
                id="password"
                v-model="password"
                name="password"
                type="password"
                autocomplete="current-password"
                required
                class="auth-input"
                :placeholder="t('auth.login.passwordLabel')"
              />
            </div>

            <div class="flex items-center justify-between pt-1 text-[0.78rem] text-[#686074]">
              <label class="inline-flex items-center gap-2">
                <input
                  id="remember-me"
                  v-model="remember"
                  name="remember-me"
                  type="checkbox"
                  class="h-3.5 w-3.5 rounded border-[#a596c0] text-[#541197] focus:ring-[#541197]"
                />
                {{ t('auth.login.rememberMe') }}
              </label>
              <a href="#" class="text-[#777084] hover:text-[#541197] transition-colors">{{ t('auth.login.forgotPassword') }}</a>
            </div>

            <button
              type="submit"
              :disabled="loading"
              class="auth-submit-btn mt-2"
            >
              {{ loading ? t('auth.login.submitting') : t('auth.login.submit') }}
            </button>
          </form>

          <p class="mt-5 text-sm text-[#4a3e5e] text-center">
            {{ t('auth.login.noAccount') }}
            <router-link to="/signup" class="font-semibold text-[#541197] hover:text-[#30085c]">{{ t('auth.login.createAccount') }}</router-link>
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

const username = ref('')
const password = ref('')
const remember = ref(false)
const loading = ref(false)
const error = ref('')
const router = useRouter()
const store = useAppStore()
const toast = useToast()
const { t } = useI18n()

const onSubmit = async () => {
  loading.value = true
  error.value = ''

  const result = await store.login({
    username: username.value,
    password: password.value,
    remember: remember.value,
  })

  loading.value = false

  if (result.success) {
    toast.success(t('auth.login.toastSuccessTitle'), t('auth.login.toastSuccessMessage'))
    setTimeout(() => {
      router.push('/dashboard')
    }, 1000)
  } else {
    error.value = result.message || t('auth.login.genericFailedMessage')
    toast.error(t('auth.login.toastFailedTitle'), result.message || t('auth.login.invalidMessage'))
  }
}
</script>

<style scoped>
input[type='checkbox']:checked {
  background-color: #541197;
  border-color: #541197;
}
</style>
