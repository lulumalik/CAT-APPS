<template>
  <main class="auth2-page auth2-page--login">
    <section class="auth2-marketing">
      <router-link to="/" class="auth2-brand" aria-label="CATLab beranda">
        <span class="auth2-brand-icon">
          <CheckSquare class="h-5 w-5" />
        </span>
        <span class="auth2-brand-text">
          <span class="auth2-brand-name">CAT<span>Lab</span></span>
          <span class="auth2-brand-tagline">Computer Assisted Test</span>
        </span>
      </router-link>

      <div class="auth2-marketing-body flex items-center h-full w-full justify-center">
        <div>
          <h1 class="auth2-heading">
            Latihan hari ini,<br />
            <span class="auth2-heading-accent">Sukses</span> di ujian nanti.
          </h1>

          <img :src="loginMobileAsset" alt="" class="auth2-mobile-art auth2-mobile-art--login" />
          <img :src="loginMobileAsset" alt="" class="auth2-desktop-art auth2-desktop-art--login" />

          <div class="auth2-features">
            <div class="flex space-x-4 items-center">
              <ShieldCheck class="h-24 w-24 text-blue-500 p-4 bg-blue-500/10 rounded-xl" />
              <div>
                <strong>Aman &amp; Terpercaya</strong>
                <div>Data dan aktivitasmu terlindungi dengan aman.</div>
              </div>
            </div>
            <div class="flex space-x-4 items-center">
              <BarChart3 class="h-24 w-24 text-green-500 p-4 bg-green-500/10 rounded-xl" />
              <div>
                <strong>Evaluasi Akurat</strong>
                <div>Dapatkan analisis hasil yang akurat dan mendetail.</div>
              </div>
            </div>
            <div class="flex space-x-4 items-center">
              <Rocket class="h-24 w-24 text-yellow-500 p-4 bg-yellow-500/10 rounded-xl" />
              <div>
                <strong>Tingkatkan Kemampuan</strong>
                <div>Latihan terarah untuk hasil yang lebih maksimal.</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <p class="auth2-marketing-footer">&copy; {{ currentYear }} CATLab. All rights reserved.</p>
    </section>

    <section class="auth2-panel">
      <span class="auth2-panel-blob auth2-panel-blob-1" aria-hidden="true" />
      <span class="auth2-panel-blob auth2-panel-blob-2" aria-hidden="true" />

      <div class="auth2-card">
        <div class="auth2-card-icon">
          <CheckSquare class="h-6 w-6" />
        </div>
        <h2 class="auth2-card-title">{{ t('auth.login.title') }}</h2>
        <p class="auth2-card-subtitle">{{ t('auth.login.subtitle') }}</p>

        <div v-if="error" class="auth2-alert">
          <CircleAlert class="h-4 w-4 flex-shrink-0" />
          {{ error }}
        </div>

        <form class="auth2-form" @submit.prevent="onSubmit">
          <div>
            <label for="username" class="auth2-label">{{ t('auth.login.usernameLabel') }}</label>
            <div class="auth2-field">
              <Mail class="auth2-field-icon h-4 w-4" />
              <input id="username" v-model="username" name="username" type="text" autocomplete="username" required
                class="auth2-input" :placeholder="t('auth.login.usernameLabel')" />
            </div>
          </div>

          <div>
            <label for="password" class="auth2-label">{{ t('auth.login.passwordLabel') }}</label>
            <div class="auth2-field">
              <Lock class="auth2-field-icon h-4 w-4" />
              <input id="password" v-model="password" name="password" :type="showPassword ? 'text' : 'password'"
                autocomplete="current-password" required class="auth2-input pr-10"
                :placeholder="t('auth.login.passwordLabel')" />
              <button type="button" class="auth2-field-toggle"
                :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                @click="showPassword = !showPassword">
                <EyeOff v-if="showPassword" class="h-4 w-4" />
                <Eye v-else class="h-4 w-4" />
              </button>
            </div>
          </div>

          <div class="auth2-row">
            <label class="auth2-checkbox">
              <input id="remember-me" v-model="remember" name="remember-me" type="checkbox" />
              {{ t('auth.login.rememberMe') }}
            </label>
            <a href="#" class="auth2-link-muted">{{ t('auth.login.forgotPassword') }}</a>
          </div>

          <button type="submit" :disabled="loading" class="auth2-submit">
            {{ loading ? t('auth.login.submitting') : t('auth.login.submit') }}
            <ArrowRight v-if="!loading" class="h-4 w-4" />
          </button>
        </form>

        <div class="auth2-divider"><span>atau masuk dengan</span></div>

        <button type="button" class="auth2-google-btn" @click="onGoogleLogin">
          <svg class="h-4 w-4" viewBox="0 0 48 48" aria-hidden="true">
            <path fill="#FFC107"
              d="M43.6 20.5H42V20H24v8h11.3C33.9 32.4 29.4 35.5 24 35.5c-6.4 0-11.7-4.5-13.2-10.5H2.5C4.5 34.7 13.4 42 24 42c11 0 20-9 20-20 0-1.2-.1-2.4-.4-3.5z" />
            <path fill="#FF3D00"
              d="M6.3 14.7l6.6 4.8C14.5 15.9 18.9 13 24 13c3.1 0 5.9 1.1 8.1 2.9l6-6C34.5 6.5 29.5 4.5 24 4.5c-7.7 0-14.4 4.4-17.7 10.2z" />
            <path fill="#4CAF50"
              d="M24 42c5.4 0 10.3-1.9 14-5l-6.5-5.4c-2 1.4-4.6 2.2-7.5 2.2-5.4 0-9.9-3.1-11.4-7.5l-6.6 5.1C9.5 37.6 16.2 42 24 42z" />
            <path fill="#1976D2"
              d="M43.6 20.5H42V20H24v8h11.3c-1.1 3.1-3.4 5.6-6.3 7.1l6.5 5.4c3.8-3.5 6.5-8.7 6.5-15.5 0-1.2-.1-2.4-.4-3.5z" />
          </svg>
          Google
        </button>

        <p class="auth2-switch">
          {{ t('auth.login.noAccount') }}
          <router-link to="/signup">{{ t('auth.login.createAccount') }}</router-link>
        </p>
      </div>

      <p class="auth2-footer">&copy; {{ currentYear }} CATLab. All rights reserved.</p>
    </section>
  </main>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import {
  ArrowRight,
  BarChart3,
  CheckSquare,
  CircleAlert,
  Eye,
  EyeOff,
  Lock,
  Mail,
  Rocket,
  ShieldCheck,
} from 'lucide-vue-next'
import loginMobileAsset from '@/../assets/properties/loginmobile.png'
import { useAppStore } from '@/stores/app'
import { useToast } from '@/composables/useNotification'
import { useI18n } from '@/composables/useI18n'

const username = ref('')
const password = ref('')
const showPassword = ref(false)
const remember = ref(false)
const loading = ref(false)
const error = ref('')
const router = useRouter()
const route = useRoute()
const store = useAppStore()
const toast = useToast()
const { t } = useI18n()
const currentYear = new Date().getFullYear()

onMounted(() => {
  if (route.query.oauth === 'failed') {
    error.value = 'Login dengan Google gagal. Silakan coba lagi.'
  } else if (route.query.oauth === 'unconfigured') {
    error.value = 'Login dengan Google belum dikonfigurasi. Hubungi admin.'
  }
})

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
    const needsInterest = store.user?.role === 'user' && !store.user?.exam_track_id
    setTimeout(() => {
      router.push(needsInterest ? '/pilih-minat' : '/dashboard')
    }, 1000)
  } else {
    error.value = result.message || t('auth.login.genericFailedMessage')
    toast.error(t('auth.login.toastFailedTitle'), result.message || t('auth.login.invalidMessage'))
  }
}

const onGoogleLogin = () => {
  window.location.href = '/auth/google/redirect'
}
</script>
