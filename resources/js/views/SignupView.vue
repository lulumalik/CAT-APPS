<template>
  <main class="auth2-page auth2-page--signup">
    <section class="auth2-marketing">
      <router-link to="/" class="auth2-mobile-back auth2-mobile-back--mobile" aria-label="Kembali ke beranda">
        <ArrowLeft class="h-5 w-5" />
        <span>Kembali</span>
      </router-link>

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
            Gabung sekarang,<br />
            <span class="auth2-heading-accent">Mulai</span> simulasi ujianmu.
          </h1>

          <img :src="registerMobileAsset" alt="" class="auth2-mobile-art auth2-mobile-art--register" />
          <img :src="registerMobileAsset" alt="" class="auth2-desktop-art auth2-desktop-art--register" />

          <div class="auth2-features">
            <div class="space-y-4">
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
      </div>

      <p class="auth2-marketing-footer">&copy; {{ currentYear }} CATLab. All rights reserved.</p>
    </section>

    <section class="auth2-panel">
      <router-link to="/" class="auth2-mobile-back auth2-mobile-back--desktop" aria-label="Kembali ke beranda">
        <ArrowLeft class="h-5 w-5" />
        <span>Kembali</span>
      </router-link>

      <span class="auth2-panel-blob auth2-panel-blob-1" aria-hidden="true" />
      <span class="auth2-panel-blob auth2-panel-blob-2" aria-hidden="true" />

      <div class="auth2-card">
        <div class="auth2-card-icon">
          <UserPlus class="h-6 w-6" />
        </div>
        <h2 class="auth2-card-title">{{ t('auth.signup.title') }}</h2>
        <p class="auth2-card-subtitle">{{ t('auth.signup.subtitle') }}</p>

        <div v-if="error" class="auth2-alert">
          <CircleAlert class="h-4 w-4 flex-shrink-0" />
          {{ error }}
        </div>

        <form class="auth2-form" @submit.prevent="onSubmit">
          <div>
            <label for="name" class="auth2-label">{{ t('auth.signup.nameLabel') }}</label>
            <div class="auth2-field">
              <User class="auth2-field-icon h-4 w-4" />
              <input id="name" v-model="name" name="name" type="text" autocomplete="name" required class="auth2-input"
                :placeholder="t('auth.signup.nameLabel')" />
            </div>
          </div>

          <div>
            <label for="username" class="auth2-label">{{ t('auth.signup.usernameLabel') }}</label>
            <div class="auth2-field">
              <AtSign class="auth2-field-icon h-4 w-4" />
              <input id="username" v-model="username" name="username" type="text" autocomplete="username" required
                class="auth2-input" :placeholder="t('auth.signup.usernameLabel')" />
            </div>
          </div>

          <div>
            <label for="email" class="auth2-label">{{ t('auth.signup.emailLabel') }}</label>
            <div class="auth2-field">
              <Mail class="auth2-field-icon h-4 w-4" />
              <input id="email" v-model="email" name="email" type="email" autocomplete="email" required
                class="auth2-input" :placeholder="t('auth.signup.emailLabel')" />
            </div>
          </div>

          <div>
            <label for="password" class="auth2-label">{{ t('auth.signup.passwordLabel') }}</label>
            <div class="auth2-field">
              <Lock class="auth2-field-icon h-4 w-4" />
              <input id="password" v-model="password" name="password" :type="showPassword ? 'text' : 'password'"
                autocomplete="new-password" required class="auth2-input pr-10"
                :placeholder="t('auth.signup.passwordLabel')" />
              <button type="button" class="auth2-field-toggle"
                :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                @click="showPassword = !showPassword">
                <EyeOff v-if="showPassword" class="h-4 w-4" />
                <Eye v-else class="h-4 w-4" />
              </button>
            </div>
          </div>

          <div>
            <label for="password_confirmation" class="auth2-label">{{ t('auth.signup.confirmPasswordLabel') }}</label>
            <div class="auth2-field">
              <Lock class="auth2-field-icon h-4 w-4" />
              <input id="password_confirmation" v-model="passwordConfirmation" name="password_confirmation"
                :type="showPasswordConfirmation ? 'text' : 'password'" autocomplete="new-password" required
                class="auth2-input pr-10" :placeholder="t('auth.signup.confirmPasswordLabel')" />
              <button type="button" class="auth2-field-toggle"
                :aria-label="showPasswordConfirmation ? 'Sembunyikan konfirmasi kata sandi' : 'Tampilkan konfirmasi kata sandi'"
                @click="showPasswordConfirmation = !showPasswordConfirmation">
                <EyeOff v-if="showPasswordConfirmation" class="h-4 w-4" />
                <Eye v-else class="h-4 w-4" />
              </button>
            </div>
            <p v-if="passwordConfirmation && password !== passwordConfirmation" class="auth2-mismatch">
              {{ t('auth.signup.confirmPasswordMismatch') }}
            </p>
          </div>

          <button type="submit" :disabled="loading" class="auth2-submit">
            {{ loading ? t('auth.signup.submitting') : t('auth.signup.submit') }}
            <ArrowRight v-if="!loading" class="h-4 w-4" />
          </button>
        </form>

        <div class="auth2-divider"><span>atau daftar dengan</span></div>

        <button type="button" class="auth2-google-btn" @click="onGoogleSignup">
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
          {{ t('auth.signup.haveAccount') }}
          <router-link to="/login">{{ t('auth.signup.signIn') }}</router-link>
        </p>
      </div>

      <p class="auth2-footer">&copy; {{ currentYear }} CATLab. All rights reserved.</p>
    </section>
  </main>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import {
  ArrowRight,
  ArrowLeft,
  AtSign,
  BarChart3,
  CheckSquare,
  CircleAlert,
  Eye,
  EyeOff,
  Lock,
  Mail,
  Rocket,
  ShieldCheck,
  User,
  UserPlus,
} from 'lucide-vue-next'
import registerMobileAsset from '@/../assets/properties/registermobile.png'
import { useAppStore } from '@/stores/app'
import { useToast } from '@/composables/useNotification'
import { useI18n } from '@/composables/useI18n'

const name = ref('')
const username = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const showPassword = ref(false)
const showPasswordConfirmation = ref(false)
const loading = ref(false)
const error = ref('')
const router = useRouter()
const store = useAppStore()
const toast = useToast()
const { t } = useI18n()
const currentYear = new Date().getFullYear()

const onSubmit = async () => {
  loading.value = true
  error.value = ''

  if (password.value !== passwordConfirmation.value) {
    loading.value = false
    error.value = t('auth.signup.confirmPasswordMismatch')
    return
  }

  const result = await store.register({
    name: name.value,
    username: username.value,
    email: email.value,
    password: password.value,
    password_confirmation: passwordConfirmation.value,
  })

  loading.value = false

  if (result.success) {
    toast.success(
      t('auth.signup.toastSuccessTitle'),
      result.message || t('auth.signup.toastSuccessMessage')
    )
    setTimeout(() => {
      router.push('/pilih-minat')
    }, 1000)
  } else {
    error.value = result.message || t('auth.signup.genericFailedMessage')
    toast.error(t('auth.signup.toastFailedTitle'), result.message || t('auth.signup.genericFailedMessage'))
  }
}

const onGoogleSignup = () => {
  window.location.href = '/auth/google/redirect'
}
</script>
